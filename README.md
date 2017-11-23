# yii2-markdown-doc

Yii2 module to display the content of all markdown file in a directory and its sub-folder.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist "macfly/yii2-markdown-doc" "*"
```

or add

```
"macfly/yii2-markdown-doc": "*"
```

to the require section of your `composer.json` file.

Configure
------------

> **NOTE:** Make sure that you have [`markdown`](https://github.com/kartik-v/yii2-markdown) module in your config files.

Configure **config/web.php** as follows

```php
'modules' => [
    ................
    'doc'  => [
        'class' => 'macfly\yii2\doc\Module',
        'rootDocDir' => '@app/docs', // Directory to list
        'saltKey' => '', // Key use to encrypt file name
    ],
    'markdown' => [
        'class' => 'kartik\markdown\Module',
    ],
    ................
],
```

Usage
------------

To access the doc, go to http://yoursite.com/doc/
