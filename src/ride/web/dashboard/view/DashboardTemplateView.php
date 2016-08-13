<?php

namespace ride\web\dashboard\view;

use ride\library\mvc\exception\MvcException;
use ride\library\mvc\view\HtmlView;
use ride\library\mvc\view\View;

use ride\web\base\view\BaseTemplateView;

/**
 * Frontend view for a dashboard
 */
class DashboardTemplateView extends BaseTemplateView {

    /**
     * Renders the output for this view
     * @param boolean $willReturnValue True to return the rendered view, false
     * to send it straight to the client
     * @return null|string Null when provided $willReturnValue is set to true, the
     * rendered output otherwise
     */
    public function render($willReturnValue = true) {
        if (!$this->templateFacade) {
            throw new MvcException("Could not render template view: template facade not set, invoke setTemplateFacade() first");
        }

        $app = $this->template->get('app');

        // render the widget templates in the regions
        $layout = $this->template->get('layout');
        if ($layout) {
            foreach ($layout as $region => $widgets) {
                foreach ($widgets as $widgetId => $widgetView) {
                    if (!$widgetView) {
                        continue;
                    }

                    // render the widget
                    $layout[$region][$widgetId] = $this->renderWidget($widgetId, $widgetView, $app);
                }
            }
        }

        $this->template->set('layout', $layout);

        return parent::render($willReturnValue);
    }

    /**
     * Handles the context and shared variables of the widget and renders it
     * @param string $widgetId Id of the widget
     * @param \ride\library\mvc\view\View $widgetView
     * @param array $app Common variables of the main template
     * @param boolean $willReturnValue True to return the rendered view, false
     * to send it straight to the client
     * @return string
     */
    protected function renderWidget($widgetId, View $widgetView, array $app, $willReturnValue = true) {
        if ($widgetView instanceof HtmlView) {
            $this->mergeResources($widgetView);
        }

        if ($widgetView instanceof TemplateView) {
            // merge main app template variable
            $template = $widgetView->getTemplate();
            $template->setResourceId($widgetId);
            $template->setTheme($this->template->getTheme());

            $widgetApp = $template->get('app');
            $widgetApp['dashboard']['widget'] = $widgetId;

            $app['cms'] = $widgetApp['cms'];

            $template->set('app', $app);

            $widgetView->setTemplateFacade($this->templateFacade);
        }

        return $widgetView->render($willReturnValue);
    }

}
