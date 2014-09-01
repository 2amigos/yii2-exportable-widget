Table Export Widget for Yii2
============================

ButtonTableExport widget is a wrapper for the [2amigOS!](http://2amigos.us) TableExport jQuery plugin. It provides a
number of options to export an HTML table in different formats.

The current supported formats are:

* json
* csv
* html
* excel
* pdf

The plugin can also be used separately, that is, by using ButtonTableExportTable and registering its assets. By doing it,
you could configure any click'able HTMLElement on your page.

The widget extends `yii\bootstrap\DropdownButton`, therefore, you can use all its properties.

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require "2amigos/yii2-table-export-widget" "*"
```
or add

```json
"2amigos/yii2-table-export-widget" : "*"
```

to the require section of your application's `composer.json` file.

Usage
-----

Using `data-uri` method. Please, check [http://caniuse.com/#feat=datauri](http://caniuse.com/#feat=datauri) to find out
about browser support for this method.

```php
use dosamigos\tableexport\ButtonTableExport;

echo ButtonTableExport::widget([
     [
        'label' => 'Export Table Grid',
        'selector' => '#my-grid-id > table',
        'split' => true,
        'exportClientOptions' => [
            'ignoredColumns' => [0, 7], // lets ignore some columns
            'useDataUri' => true,
        ]
    ]
]);
```

Using `TableExportAction` - the most reliable option:

```php
// On your controller
use dosamigos\tableexport\ButtonTableExport;

// ...
public function actions()
{
    return [
        // ...
        'download' => [
            'class' => TableExportAction::className()
        ]
        // ...
    ];
}

// ...

// On your view
use dosamigos\tableexport\ButtonTableExport;

<?= ButtonTableExport::widget(
    [
        'label' => 'Export Table',
        'selector' => '#tableId', // any jQuery selector
        'exportClientOptions' => [
            'ignoredColumns' => [0, 7],
            'useDataUri' => false,
            'url' => \yii\helpers\Url::to('controller/download')
        ]
    ]
);?>

```

> [![2amigOS!](http://www.gravatar.com/avatar/55363394d72945ff7ed312556ec041e0.png)](http://www.2amigos.us)  
<i>Web development has never been so fun!</i>  
[www.2amigos.us](http://www.2amigos.us)