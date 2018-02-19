<?php

namespace miolae\yii2\doc\controllers;

use kartik\markdown\Markdown;
use miolae\yii2\doc\helpers\FileHelper;
use miolae\yii2\doc\Module;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/** @property Module $module */
class DefaultController extends Controller
{
    /**
     * @param string|null $page
     *
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex($page = '')
    {
        $saltKey = $this->module->saltKey;
        $rootDocDir = Yii::getAlias($this->module->rootDocDir);

        if (!chdir($rootDocDir)) {
            throw new NotFoundHttpException(sprintf("Directory '%s' doesn't exist", $rootDocDir));
        }

        $list = FileHelper::scanDoc($rootDocDir, $saltKey);
        $content = null;

        if (($item = ArrayHelper::getValue($list, $page, null)) !== null) {
            Yii::info("File to load: " . ArrayHelper::getValue($item, 'filename'));
            $filepath = ArrayHelper::getValue($item, 'filepath');

            if (file_exists($filepath)) {
                $content = file_get_contents($filepath);
                $content = Markdown::convert($content, [
                    'markdown' => [
                        'url_filter_func' => function ($url) use ($saltKey) {
                            return Url::isRelative($url) ? Url::to(['index', 'page' => $url]) : $url;
                        },
                    ],
                ]);
            }
        }

        return $this->render('index', [
            'list' => $list,
            'title' => ArrayHelper::getValue($item, 'filename'),
            'content' => $content,
            'pageCurrent' => $page,
        ]);
    }
}
