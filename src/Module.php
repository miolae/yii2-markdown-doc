<?php

namespace miolae\yii2\doc;

use miolae\yii2\doc\assets\ImageAsset;
use yii\web\AssetManager;

/**
 * @property imageAsset $imageAsset
 */
class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $rootDocDir = '@app/docs';

    /**
     * @var bool
     */
    public $cache = true;

    /**
     * @var string
     */
    public $titlePrefix;

    /**
     * @var ImageAsset
     */
    private $imageAsset;

    public function init()
    {
        parent::init();

        if (!\Yii::$app->hasModule('markdown')) {
            \Yii::$app->setModule('markdown', ['class' => 'kartik\markdown\Module']);
        }
    }

    public function getImageAsset()
    {
        if (!$this->imageAsset) {
            /** @noinspection PhpUnhandledExceptionInspection */
            /** @var ImageAsset $this->imageAsset */
            $this->imageAsset = \Yii::createObject(ImageAsset::class);
            /** @noinspection PhpUnhandledExceptionInspection */
            /** @var AssetManager $am */
            $am = \Yii::createObject(AssetManager::class);
            $this->imageAsset->publish($am);
        }

        return $this->imageAsset;
    }

    /**
     * @return string|null
     */
    public function getTitlePrefix()
    {
        return $this->titlePrefix;
    }
}
