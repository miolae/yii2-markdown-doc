<?php

namespace miolae\yii2\doc\controllers;

use kartik\markdown\Markdown;
use miolae\yii2\doc\helpers\FileHelper;
use miolae\yii2\doc\Module;
use Yii;
use yii\caching\Cache;
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
        $rootDocDir = Yii::getAlias($this->module->rootDocDir);

        if (!chdir($rootDocDir)) {
            throw new NotFoundHttpException(sprintf("Directory '%s' doesn't exist", $rootDocDir));
        }

        $list = FileHelper::scanDoc($rootDocDir);
        $content = null;

        if (($item = ArrayHelper::getValue($list, $page, null)) !== null) {
            Yii::info("File to load: " . ArrayHelper::getValue($item, 'filename'));
            $filepath = ArrayHelper::getValue($item, 'filepath');

            if (file_exists($filepath)) {
                $content = $this->getContent($filepath, $item, $this->module->cache);
            }
        }

        $title = $this->getPageTitle($item);

        return $this->render('index', [
            'list' => $list,
            'title' => $title,
            'content' => $content,
            'pageCurrent' => $page,
        ]);
    }

    /**
     * @param string $filepath path for md file
     * @param array $item An item from FileHelper
     * @param bool $cacheUse
     * @return bool|mixed|string
     * @throws \yii\base\InvalidConfigException
     */
    public function getContent($filepath, $item, $cacheUse = true)
    {
        $controller = $this;

        $getContent = function () use ($controller, $filepath, $item) {
            return $controller->getContent($filepath, $item, false);
        };

        /** @var false|Cache $cache */
        if ($cacheUse && $cache = Yii::$app->get('cache')) {
            $cacheKey = "miolae-markdown-doc:$filepath";
            $dependency = new \yii\caching\FileDependency(['fileName' => $filepath]);

            return $cache->getOrSet($cacheKey, $getContent, null, $dependency);
        }

        $content = file_get_contents($filepath);
        $content = Markdown::convert($content, [
            'markdown' => [
                'url_filter_func' => function ($url) use ($item, $controller) {
                    if (Url::isRelative($url)) {
                        $pageUrl = explode('/', $item['url']);
                        if ($item['type'] !== 'directory' && strpos($url, '/') !== 0 && count($pageUrl) > 0) {
                            array_pop($pageUrl);
                        }
                        $pageUrl[] = trim($url, '/');

                        $page = implode('/', $pageUrl);
                        $page = FileHelper::getEntryUrl($page);

                        if (FileHelper::isMenuItem($page)) {
                            $url = Url::to(['index', 'page' => $page]);
                            $url = str_replace(urlencode('/'), '/', $url);
                        } else {
                            $url = $controller->module->imageAsset->baseUrl . '/' . $page;
                        }
                    }

                    return $url;
                },
            ],
        ]);

        return $content;
    }

    /**
     * @param $item
     *
     * @return string
     */
    protected function getPageTitle($item)
    {
        $title = '';
        if (is_array($item)) {
            $title = empty($item['name']) ? $item['filename'] : $item['name'];
        }

        $prefix = $this->module->getTitlePrefix();
        $title = trim($prefix) . ' ' . $title;

        return $title;
    }
}
