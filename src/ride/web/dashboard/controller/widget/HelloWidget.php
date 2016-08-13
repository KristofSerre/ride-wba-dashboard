<?php

namespace ride\web\dashboard\controller\widget;

use ride\web\dashboard\controller\AbstractWidget;

class HelloWidget extends AbstractWidget {

    const NAME = "hello";

    public function indexAction() {
        $this->setTemplateView('dashboard/hello');
    }

}
