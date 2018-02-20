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

Configure **config/web.php** as follows

```php
'modules' => [
    ................
    'doc'  => [
        'class' => 'miolae\yii2\doc\Module',
        'rootDocDir' => '@app/docs', // Directory to list
    ]
    ................
],
```

Usage
------------

To access the doc, go to http://yoursite.com/doc/
