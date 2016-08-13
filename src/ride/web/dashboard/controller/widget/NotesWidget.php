<?php

namespace ride\web\dashboard\controller\widget;

use ride\library\validation\exception\ValidationException;
use ride\web\dashboard\controller\AbstractWidget;

class NotesWidget extends AbstractWidget {

    const NAME = "notes";

    public function indexAction() {
        $body = $this->properties->getWidgetProperty('body');

        $this->setTemplateView('dashboard/notes', array(
            'body' => $body,
        ));
    }

    /**
     * Action to handle and show the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $translator = $this->getTranslator();

        $data = array(
            'body' => $this->properties->getWidgetProperty('body'),
        );

        $form = $this->createFormBuilder($data);
        $form->addRow('body', 'text', array(
            'label' => $translator->translate('label.body'),
            'attributes' => array(
                'rows' => 10,
            ),
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

                $this->properties->setWidgetProperty('body', $data['body']);

                return true;
            } catch (ValidationException $exception) {
                $this->setValidationException($exception, $form);
            }
        }

        $this->setTemplateView('dashboard/notes.properties', array(
            'form' => $form->getView(),
        ));

        return false;
    }

}
