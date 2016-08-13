<?php

namespace ride\web\dashboard\controller;

use ride\library\widget\WidgetProperties;

use ride\web\base\controller\AbstractController;
use ride\web\dashboard\widget\Widget;

/**
 * Abstract implementation for a widget
 */
class AbstractWidget extends AbstractController implements Widget {

    /**
     * Path to the default icon of the widget
     * @var string
     */
    const ICON =  'img/dashboard/widget.png';

    /**
     * Unique identifier of this widget
     * @var string
     */
    protected $id;

    /**
     * Properties of this widget
     * @var \ride\web\dashboard\widget\DashboardWidgetProperties
     */
    protected $properties;

    /**
     * Code of the locale for the widget request
     * @var string
     */
    protected $locale;

    /**
     * Gets the machine name of the widget
     * @return string
     */
    public function getName() {
        return static::NAME;
    }

    /**
     * Gets the path to the icon of the widget
     * @return string|boolean
     */
    public function getIcon() {
        return static::ICON;
    }

    /**
     * Sets the unique identifier of the widget
     * @param string $identifier Unique identifier
     * @return null
     */
    public function setIdentifier($identifier) {
        $this->id = $identifier;
    }

    /**
     * Sets the code of the locale for the widget request
     * @param string $locale Code of the locale
     * @return null
     */
    public function setLocale($locale) {
        $this->locale = $locale;
    }

    /**
     * Gets the callback for the widget action
     * @return callback Callback for the action
     */
    public function getCallback() {
        return array($this, 'indexAction');
    }

    /**
     * Sets the properties of the widget instance
     * @param \ride\library\widget\WidgetProperties $properties Properties for
     * the widget instance
     * @return null
     */
    public function setProperties(WidgetProperties $properties) {
        $this->properties = $properties;
    }

    /**
     * Gets the properties of this widget instance
     * @return \ride\library\widget\WidgetProperties
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     * Gets the callback for the properties action
     * @return null|callback Null if the widget does not implement a properties
     * action, a callback for the action otherwise
     */
    public function getPropertiesCallback() {
        if (method_exists($this, 'propertiesAction')) {
            return array($this, 'propertiesAction');
        }

        return null;
    }

    /**
     * Sets a template view to the response
     * @param string $resource Resource to the template
     * @param array $variables Variables for the template
     * @param string $id Id of the template view in the dependency injector
     * @return \ride\web\mvc\view\TemplateView
     */
    protected function setTemplateView($resource, array $variables = null, $id = null) {
        if ($id === null) {
            $id = 'widget';
        }

        return parent::setTemplateView($resource, $variables, $id);
    }

}
