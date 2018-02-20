<?php

use yii\bootstrap\Nav;
use yii\helpers\ArrayHelper;

/* @var yii\web\View $this  */
/* @var string $content */
/* @var string|null $title */
/* @var string $pageCurrent */
/* @var array $list */

$this->title = ($title === null) ? 'Documentation' : $title;
$this->params['breadcrumbs'][] = $this->title;

$menuItems = [];

foreach ($list as $page => $item) {
    if (!empty($page)) {
        $menuItems[] = [
            'label' => sprintf("%s %s", str_pad('', ArrayHelper::getValue($item, 'pad'), '--'), ArrayHelper::getValue($item, 'name')),
            'url' => ['index', 'page' => $page],
            'active' => ($pageCurrent == $page),
        ];
    }
}

?>
<div class="doc-toc" style="float:left;">
    <?= Nav::widget([
        'options' => [
            'class' =>'nav-pills nav-stacked',
        ],
        'items' => $menuItems,
    ]); ?>
</div>
<div class="doc-content" style="float:left; margin-left:10px;">
    <?= $content ?>
</div>
