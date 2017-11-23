<?php

use yii\bootstrap\Nav;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
$this->title = ($title === null) ? 'Documentation' : $title;
$this->params['breadcrumbs'][] = $this->title;

$menuItems = [];

foreach ($list as $key => $item) {
    $menuItems[] = [
        'label' => sprintf("%s %s", str_pad('', ArrayHelper::getValue($item, 'pad'), '--'), ArrayHelper::getValue($item, 'name')),
        'url' => ['index' , 'page' => $key],
        'options' => (ArrayHelper::getValue($item, 'type') == 'file') ? [] : ['class' => 'disabled'],
        'active' => ($page == $key),
    ];
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
