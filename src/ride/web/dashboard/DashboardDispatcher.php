<?php

namespace ride\web\dashboard;

use ride\library\mvc\dispatcher\Dispatcher;
use ride\library\mvc\Request;
use ride\library\mvc\Response;

use ride\web\dashboard\widget\WidgetModel;
use ride\web\dashboard\widget\Widget;

/**
 * Dispatcher for a dashboard
 */
class DashboardDispatcher {

    /**
     * Constructs a new dashboard dispatcher
     * @param \ride\library\mvc\dispatcher\Dispatcher $dispatcher
     * @param \ride\web\dashboard\widget\WidgetModel $widgetModel
     */
    public function __construct(Dispatcher $dispatcher, WidgetModel $widgetModel) {
        $this->dispatcher = $dispatcher;
        $this->widgetModel = $widgetModel;
    }

    /**
     * Dispatches a dashboard
     * @param \ride\library\mvc\Request $request
     * @param \ride\library\mvc\Response $response
     * @param \ride\web\dashboard\model\Dashboard $dashboard Instance of the
     * dashboard
     * @param string $locale Code of the locale
     * @return array Array with the layout of the dashboard filled with widget
     * views
     */
    public function dispatch(Request $request, Response $response, Dashboard $dashboard, $locale) {
        $layout = $dashboard->getLayout();

        foreach ($layout as $region => $widgetIds) {
            foreach ($widgetIds as $widgetId) {
                // load widget
                $widget = $this->widgetModel->getWidget($dashboard->getWidget($widgetId));
                if (!$widget) {
                    // widget does not exist
                    unset($layout[$region][$widgetId]);

                    continue;
                }

                // initialize widget
                $widget = clone $widget;
                $widget->setIdentifier($widgetId);
                $widget->setLocale($locale);
                $widget->setProperties($dashboard->getWidgetProperties($widgetId));

                // dispatch the widget
                $layout[$region][$widgetId] = $this->dispatchWidget($request, $response, $widget);
            }
        }

        return $layout;
    }

    /**
     * Dispatches a widget
     * @param \ride\library\mvc\Request $request
     * @param \ride\library\mvc\Response $response
     * @param \ride\web\dashboard\widget\Widget $widget Instance of the widget
     * @return \ride\library\mvc\view\View
     */
    protected function dispatchWidget(Request $request, Response $response, Widget $widget) {
        $route = $request->getRoute();
        $route->setIsDynamic(false);
        $route->setCallback($widget->getCallback());

        $this->dispatcher->dispatch($request, $response);

        return $response->getView();
    }

}
