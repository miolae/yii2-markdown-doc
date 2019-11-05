# yii2-markdown-doc

Yii2 module to display the content of all markdown file in a directory and its sub-folder.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist "miolae/yii2-markdown-doc" "2"
```

or add

```
"miolae/yii2-markdown-doc": "2"
```

to the require section of your `composer.json` file.

Configure
------------

1. Configure the module in **config/web.php** as follows  
```php
'modules' => [
    //////////////////
    'doc'  => [
        'class' => 'miolae\yii2\doc\Module',
        // Directory to list
        'rootDocDir' => '@app/docs',
        // set false if you don't want to cache generated html, useful for debugging 
        'cache' => true,
        // Prefix for browser title, i.e: Documentation: Page Title
        'titlePrefix' => 'Documentation:',
    ],
    //////////////////
],
```
1. Add `miolae\yii2\doc\controllers\DefaultController` either to `controllerMap` or to `urlManager`. Example for url manager with pretty url enabled:
```php
'urlManager' => [
    'enablePrettyUrl' => true,
    'showScriptName'  => false,
    'rules'           => [
        '/doc/<page:[\w\/-\#]+>' => 'doc/default/index',
        '/doc'                   => 'doc/default/index',
    ],
],
```

Usage
------------

1. Add `README.md` to your `docs` directory (or other one you specified in `rootDocDir` option)
1. To access the doc, go to http://yoursite.com/doc/
