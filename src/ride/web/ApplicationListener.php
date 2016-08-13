<?php

namespace ride\web;

use ride\library\cms\Cms;
use ride\library\event\EventManager;
use ride\library\event\Event;
use ride\library\i18n\I18n;
use ride\library\security\SecurityManager;

use ride\web\base\controller\AbstractController;
use ride\web\base\menu\MenuItem;

class ApplicationListener {

    /**
     * Name of the event to prepare the content menu
     * @var string
     */
    const EVENT_MENU_CONTENT = 'cms.menu.dashboard';


    /**
     * Sets the instance of the CMS
     * @param \ride\web\cms\Cms $cms Instance of the CMS facade
     * @return null
     */
    public function setCms(Cms $cms) {
        $this->cms = $cms;
    }

    /**
     * Sets the instance of the security manager
     * @param \ride\library\security\SecurityManager $securityManager
     * @return null
     */
    public function setSecurityManager(SecurityManager $securityManager) {
        $this->securityManager = $securityManager;
    }

    /**
     * Action to add the Dashboard menu to the taskbar
     * @param \ride\library\event\Event $event Triggered event
     * @param \ride\library\i18n\I18N $i18n
     * @param \ride\web\WebApplication $web
     * @param \ride\library\event\EventManager $eventManager
     * @return null
     */
    public function loadTaskbar(Event $event, I18n $i18n, WebApplication $web) {
        $locale = null;
        $request = $web->getRequest();
        $route = $request->getRoute();

        if ($route) {
            $locale = $route->getArgument('locale');
        }

        if (!$locale && $request->hasSession()) {
            $session = $request->getSession();
            $locale = $session->get(AbstractController::SESSION_LOCALE_CONTENT);
        }

        if (!$locale) {
            $locale = $i18n->getLocale()->getCode();
        }

        $taskbar = $event->getArgument('taskbar');
        $applicationMenu = $taskbar->getApplicationsMenu();

        $menuItem = new MenuItem();
        $menuItem->setTranslation('title.dashboard');
        $menuItem->setUrl($web->getUrl('admin'));
        $menuItem->setWeight(-200);
        //$applicationMenu->addMenuItem($menuItem);

    }

    
}