<?php

namespace ride\web\dashboard\controller;

use ride\library\reflection\Invoker;

use ride\web\base\controller\AbstractController;
use ride\web\dashboard\DashboardDispatcher;
use ride\web\dashboard\DashboardModel;
use ride\web\dashboard\widget\WidgetModel;
use ride\web\mvc\view\TemplateView;

/**
 * Controller for the dashboard
 */
class DashboardController extends AbstractController {

    /**
     * Instance of the dashboard model
     * @var \ride\web\dashboard\DashboardModel
     */
    private $dashboardModel;

    /**
     * Instance of the user's dashboard
     * @var \ride\web\dashboard\Dashboard
     */
    private $dashboard;

    /**
     * Constructs a new dashboard controller
     * @param \ride\web\dashboard\DashboardModel $dashboardModel
     * @param \ride\web\dashboard\widget\WidgetModel $widgetModel
     * @return null
     */
    public function __construct(DashboardModel $dashboardModel, WidgetModel $widgetModel) {
        $this->dashboardModel = $dashboardModel;
        $this->widgetModel = $widgetModel;
    }

    /**
     * Initializes the dashboard before every action
     * @return null
     */
    public function preAction() {
        $dashboardName = $this->getDashboardName();

        $this->dashboard = $this->dashboardModel->getDashboard($dashboardName);
        if (!$this->dashboard) {
            $this->dashboard = $this->dashboardModel->createDashboard($dashboardName);
        }

        return true;
    }

    /**
     * Action to dispatch the user's dashboard
     * @param \ride\web\dashboard\DashboardDispatcher $dispatcher
     * @return null
     */
    public function indexAction(DashboardDispatcher $dispatcher) {
        $widgets = $this->widgetModel->getWidgets();
        $layout = $dispatcher->dispatch($this->request, $this->response, $this->dashboard, $this->getLocale());

        $this->setTemplateView('asphalt/dashboard/index', array(
            'widgets' => $widgets,
            'layout' => $layout,
        ), 'dashboard');
    }

    /**
     * Action to add a new instance of a widget on the dashboard
     * @param string $region Name of the region
     * @param string $widget Machine name of the widget
     * @return null
     */
    public function addAction($region, $widget) {
        $widget = $this->widgetModel->getWidget($widget);
        if (!$widget) {
            $this->response->setNotFound();

            return;
        }

        $this->dashboard->addWidget($widget, $region);

        $this->dashboardModel->setDashboard($this->dashboard);

        //FOR NOW A RETURN TO ADMIN
        $this->response->setRedirect($this->getUrl('admin'));
        return;
    }

    /**
     * Action to remove a widget instance from the dashboard
     * @param string $widget Instance id of the widget
     * @return null
     */
    public function removeAction($widget) {
        $this->dashboard->removeWidget($widget);

        $this->dashboardModel->setDashboard($this->dashboard);

        //FOR NOW A RETURN TO ADMIN
        $this->response->setRedirect($this->getUrl('admin'));
        return;
    }

    /**
     * Action to dispatch to the properties of a widget
     */
    public function propertiesAction(Invoker $invoker, $widget) {
        $locale = $this->getLocale();
        $widgetId = $widget;

        $widget = $this->dashboard->getWidget($widgetId);
        $widget = $this->widgetModel->getWidget($widget);

        $widget = clone $widget;
        $widget->setRequest($this->request);
        $widget->setResponse($this->response);
        $widget->setProperties($this->dashboard->getWidgetProperties($widgetId));
        $widget->setLocale($locale);
        $widget->setIdentifier($widgetId);

        if ($widget instanceof AbstractController) {
            $widget->setDependencyInjector($this->dependencyInjector);
        }

        $propertiesCallback = $widget->getPropertiesCallback();
        if (!$propertiesCallback) {
            $this->response->setNotFound();

            return;
        }

        if ($invoker->invoke($propertiesCallback)) {
            $this->dashboardModel->setDashboard($this->dashboard);

            $this->response->setRedirect($this->getUrl('admin'));

            return;
        }

        $widgetView = $this->response->getView();
        if (!$widgetView && !$this->response->getBody() && $this->response->isOk()) {
            $this->response->setRedirect($this->getUrl('admin'));

            return;
        }

        if (!$widgetView instanceof TemplateView) {
            return;
        }

        $template = $widgetView->getTemplate();
        $variables = array(
            'locale' => $locale,
            'widget' => $widget,
            'widgetId' => $widgetId,
            'widgetName' => $this->getTranslator()->translate('widget.' . $widget->getName()),
            'propertiesTemplate' => $template->getResource(),
        ) + $template->getVariables();

        $view = $this->setTemplateView('dashboard/properties', $variables);
        $view->getTemplate()->setResourceId(substr(md5($template->getResource()), 0, 7));
        $view->addJavascript('js/form.js');
        $view->mergeResources($widgetView);
    }

    /**
     * Gets the name of the dashboard for the current user
     * @return string
     */
    private function getDashboardName() {
        $user = $this->getUser();
        if ($user) {
            return 'user-' . $user->getId();
        }

        $session = $this->request->getSession();

        return 'session-' . $session->getId();
    }

}
