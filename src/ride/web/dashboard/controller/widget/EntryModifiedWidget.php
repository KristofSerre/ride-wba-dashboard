<?php
/**
 * Created by PhpStorm.
 * User: kristofserre
 * Date: 27/02/16
 * Time: 22:28
 */
namespace ride\web\dashboard\controller\widget;

use ride\library\orm\OrmManager;
use ride\library\system\System;
use ride\web\dashboard\controller\AbstractWidget;

class EntryModifiedWidget extends AbstractWidget {

    const NAME = "entry.modified";

    const TEMPLATE_NAMESPACE = "/dashboard";


    public function indexAction(OrmManager $orm, System $system) {

        $models = $orm->getModels();
        $visibleModels = array();
        foreach ($models as $index => $model) {
            $meta = $model->getMeta();
            if (!$meta->getOption('scaffold.expose')) {
                continue;
            } else {
                $visibleModels[$index] = $model;
            }
        }

        $result = null;
        if ($visibleModels) {
            $conditionPart = array();
            $conditionVar = array();
            foreach ($visibleModels as $index => $model) {
                $conditionPart[] = '{model} = %model' . $index . '%';
                $conditionVar['model'. $index] = $model->getName();
            }
            $entryLogModel = $orm->getModel('EntryLog');
            $query = $entryLogModel->createQuery();
            $query->addConditionWithVariables(implode(' OR ', $conditionPart), $conditionVar);
            $query->setLimit(5);
            $result = $query->query();
        }

        $entries = array();

//        foreach ($result as $model) {
//
//            $modelName = $model->getModel();
//            $modelNameModel = $orm->getModel($modelName);
//            $entry = $modelNameModel->getById($model->getEntry());
//            $entries[] = $entry;
//        }

        $this->setTemplateView(self::TEMPLATE_NAMESPACE . '/entries.modified', array(
            'result' => $result,
            'locale' => $this->locale,
            'models' => $visibleModels
        ));

    }
    
}