<?php

namespace ride\web\dashboard\controller\widget;

use ride\library\config\Config;
use ride\library\validation\exception\ValidationException;
use ride\web\dashboard\controller\AbstractWidget;

use ride\web\dashboard\service\GoogleService;

class GoogleAnalyticsWidget extends AbstractWidget {

    const NAME = "analytics";

    const TEMPLATE_NAMESPACE = "dashboard";

    public function indexAction(Config $config, GoogleService $googleService) {
        //$googleService->getAccessToken();
        //$analytics = $googleService->getAnalytics($config->get("google.analytics.id"));
        $analytics = "YOLO";
        $this->setTemplateView(self::TEMPLATE_NAMESPACE . '/analytics', array(
            'analytics' => $analytics,
            'clientId' => $config->get("google.api.key")
        ));
    }

    /**
     * Action to handle and show the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $translator = $this->getTranslator();

        $data = array(
            'accountId' => $this->properties->getWidgetProperty('accountId'),
        );

        $form = $this->createFormBuilder($data);
        $form->addRow('accountId', 'string', array(
            'label' => $translator->translate('label.id'),
            'filters' => array(
                'trim' => array(),
            )
        ));

        $form = $form->build();
        if ($form->isSubmitted()) {
            if ($this->request->getBodyParameter('cancel')) {
                return false;
            }

            try {
                $form->validate();

                $data = $form->getData();

                $this->properties->setWidgetProperty('accountId', $data['accountId']);

                return true;
            } catch (ValidationException $exception) {
                $this->setValidationException($exception, $form);
            }
        }

        $this->setTemplateView('dashboard/analytics.properties', array(
            'form' => $form->getView(),
        ));

        return false;
    }

    
}