<?php

namespace miolae\yii2\doc;

class Module extends \yii\base\Module
{
    public $rootDocDir = '@app/docs';

    public function init()
    {
        parent::init();

        if (!\Yii::$app->hasModule('markdown')) {
            \Yii::$app->setModule('markdown', ['class' => 'kartik\markdown\Module']);
        }
    }
}
