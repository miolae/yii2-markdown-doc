<?php

namespace miolae\yii2\doc;

use yii\helpers\ArrayHelper;

class Module extends \yii\base\Module
{
    public $rootDocDir = '@app/docs';
    public $search = [];

    protected $searchDefaults = [
        'enabled' => false,
        'connection' => 'db',
        'cacheTime' => 5,
    ];

    public function init()
    {
        parent::init();

        if (!\Yii::$app->hasModule('markdown')) {
            \Yii::$app->setModule('markdown', ['class' => 'kartik\markdown\Module']);
        }

        $this->search = ArrayHelper::merge($this->search, $this->searchDefaults);
    }
}
