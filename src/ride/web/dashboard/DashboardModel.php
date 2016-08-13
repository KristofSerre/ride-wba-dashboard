<?php

namespace ride\web\dashboard;

use ride\library\cache\pool\CachePool;

/**
 * The model of the dashboards
 */
class DashboardModel {

    /**
     * Cache pool as data store of the dashboards
     * @var ride\library\cache\pool\CachePool
     */
    private $cachePool;

    /**
     * Constructs a new dashboard model
     * @param ride\library\cache\pool\CachePool $cache
     * @return null
     */
    public function __construct(CachePool $cachePool) {
        $this->cachePool = $cachePool;
    }

    /**
     * Creates a new dashboard
     * @param string $name
     * @return Dashboard
     */
    public function createDashboard($name) {
        return new Dashboard($name);
    }

    /**
     * Gets a dashboard from the model
     * @param string $name Name of the dashboard
     * @return Dashboard
     */
    public function getDashboard($name) {
        $cacheItem = $this->cachePool->get($name);
        if (!$cacheItem->isValid()) {
            return null;
        }

        return $cacheItem->getValue();
    }

    /**
     * Sets a dashboard to the model
     * @param Dashboard $dashboard
     * @return null
     */
    public function setDashboard(Dashboard $dashboard) {
        $cacheItem = $this->cachePool->get($dashboard->getName());
        $cacheItem->setValue($dashboard);

        $this->cachePool->set($cacheItem);
    }

    /**
     * Removes a dashboard from the model
     * @param Dashboard $dashboard
     * @return null
     */
    public function removeDashboard(Dashboard $dashboard) {
        $this->cachePool->flush($dashboard->getName());
    }

}
