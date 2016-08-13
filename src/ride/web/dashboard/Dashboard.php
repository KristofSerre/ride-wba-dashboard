<?php

namespace ride\web\dashboard;

use ride\library\widget\GenericWidgetProperties;
use ride\library\widget\WidgetProperties;

use ride\web\dashboard\widget\Widget;

/**
 * Data container of a dashboard
 */
class Dashboard {

    /**
     * Name of the dashboard
     * @var string
     */
    private $name;

    /**
     * Array with the widget id as key and the widget machine name as value
     * @var array
     */
    private $widgets;

    /**
     * Array with the widget id as key and a WidgetProperties instance as value
     * @var array
     */
    private $properties;

    /**
     * Array with the widget order per region
     * @var array
     */
    private $layout;

    /**
     * Constructs a new dashboard
     * @param string $name Name for the dashboard
     * @return null
     */
    public function __construct($name) {
        if (empty($name)) {
            throw new \Exception('Provided name is empty');
        }

        $this->name = $name;
        $this->widgets = array();
        $this->properties = array();
        $this->layout = array(
            1 => array(),
            2 => array(),
            3 => array(),
            4 => array(),
        );
    }

    /**
     * Gets the name of the dashboard
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the layout of the dashboard
     * @return array Array with the name of the region as key and an array of
     * widget instance ids as value
     */
    public function getLayout() {
        return $this->layout;
    }

    /**
     * Sets the layout of the dashboard
     * @param array $layout Array with the name of the region as key and an
     * array of widget instance ids as value
     * @return null
     */
    public function setLayout(array $layout) {
        $widgets = $this->widgets;
        $newLayout = array();

        foreach ($layout as $region => $widgetIds) {
            $newLayout[$region] = array();

            foreach ($widgetIds as $index => $widgetId) {
                if (!isset($widgets[$widgetId])) {
                    throw new \Exception('Could not set layout: no widget instance with id ' . $widgetId . ' in this dashboard');
                }

                $newLayout[$region][$widgetId] = $widgetId;

                unset($widgets[$widgetId]);
            }
        }

        if ($widgets) {
            throw new \Exception('Could not set layout: missing widgets ' . implode(', ', array_keys($widgets)));
        }

        $this->layout = $newLayout;
    }

    /**
     * Adds a widget to this dashboard
     * @param \ride\web\dashboard\widget\Widget $widget
     * @param integer $region Name of the region
     * @return integer Instance id of the widget
     */
    public function addWidget(Widget $widget, $region = 1) {
        $widgetId = $this->getNewWidgetId();

        if (!isset($this->layout[$region])) {
            $this->layout[$region] = array();
        }

        $this->layout[$region][$widgetId] = $widgetId;
        $this->widgets[$widgetId] = $widget->getName();

        return $widgetId;
    }

    public function getWidget($id) {
        if (!isset($this->widgets[$id])) {
            throw new \Exception('Could not find widget with id ' . $id);
        }

        return $this->widgets[$id];
    }

    /**
     * Removes a widget from this dashboard
     * @param integer $id Instance id of the widget
     * @return null
     */
    public function removeWidget($id) {
        if (!isset($this->widgets[$id])) {
            throw new \Exception('Could not find widget with id ' . $id);
        }

        unset($this->widgets[$id]);

        if (isset($this->properties[$id])) {
            unset($this->properties[$id]);
        }

        foreach ($this->layout as $region => $widgetIds) {
            if (isset($widgetIds[$id])) {
                unset($this->layout[$region][$id]);
            }
        }
    }

    /**
     * Sets the widget properties for the provided widget instance
     * @param integer $id Instance id of the widget
     * @param \ride\library\widget\WidgetProperties $widgetProperties
     * @return null
     */
    public function setWidgetProperties($id, WidgetProperties $widgetProperties) {
        $this->properties[$id] = $widgetProperties;
    }

    /**
     * Gets the widget properties for the provided widget instance
     * @param integer $id Instance id of the widget
     * @return \ride\library\widget\WidgetProperties
     */
    public function getWidgetProperties($id) {
        if (!isset($this->properties[$id])) {
            $this->properties[$id] = new GenericWidgetProperties($id);
        }

        return $this->properties[$id];
    }

    /**
     * Gets a new widget id
     * @return integer
     */
    private function getNewWidgetId() {
        $newWidgetId = 1;

        foreach ($this->widgets as $widgetId => $widget) {
            if ($widgetId >= $newWidgetId) {
                $newWidgetId = $widgetId + 1;
            }
        }

        return $newWidgetId;
    }

}
