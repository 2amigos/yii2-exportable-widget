Exportable Action
=================

This action provides you with the functionality to handle form request for your custom file download code. It works in 
conjunction with the [DownloadService](../services/download-service.md) to force download the contents you create 
throughout the anonymous function you set. 

###Usage

First we need to configure the action on the controller we wish to force download the contents of our grid: 

```php 
namespace app\controllers;

use yii\web\Controller;

class MyController extends Controller {
    
    // ...
    
    public function getActions() {
        return [
            'export' => [
                'class' => '\dosamigos\exportable\actions\ExportableAction',
                'filename' => 'users', // without file extension!
                'contentValue' => function($type) { // could be any callable
                       // ... create contents based on $type
                       // ... $type can be any of the TypeHelper values or custom ones

                       return $contents;
                }
            ]
        ];
    }

    // ...
}

```

Then we need to render the `ExportableButton` widget and configure its `url` attribute to point to our controller's 
action. 

####Render the Button

The button widget extends from `yii\bootstrap\ButtonDropdown` widget. So you can use any of the options available there. 

```php 
use dosamigos\exportable\ExportableButton; 
use yii\helpers\Url;

echo ExportableButton::widget(
    [
        'label' => '<i class="glyphicon glyphicon-export"></i>',
        'url' => Url::to(['my/export]), // setup the URL
        'options' => ['class' => 'btn-default'],
        'dropdown' => [
            'options' => ['class' => 'dropdown-menu-right']
        ]
    ]
);

```

####Using 2amigos GridView Library

The next example is by using our [GridView Library](https://github.com/2amigos/yii2-grid-view-library): 

```php
use dosamigos\exportable\ExportableButton; 
use dosamigos\grid\GridView;
use dosamigos\grid\behaviors\LoadingBehavior;
use dosamigos\grid\behaviors\ResizableColumnsBehavior;
use dosamigos\grid\behaviors\ToolbarBehavior;
use dosamigos\grid\buttons\ReloadButton;
use yii\helpers\Url;

echo \dosamigos\grid\GridView::widget(
    [
        'behaviors' => [
            'ExportableBehavior',
            'ResizableColumnsBehavior',
            [
                'class' => 'LoadingBehavior',
                'type' => 'bars'
            ],
            [
                'class' => 'ToolbarBehavior',
                'options' => ['style' => 'margin-bottom: 5px'],
                'encodeLabels' => false, // like this we will be able to display HTML on our buttons
                'buttons' => [
                    ReloadButton::widget(['options' => ['class' => 'btn-success']]),
                    '-',
                    ExportableButton::widget(
                        [
                            'label' => '<i class="glyphicon glyphicon-export"></i>',
                            'url' => Url::to(['my/export]), // setup the URL
                            'options' => ['class' => 'btn-default'],
                            'dropdown' => [
                                'options' => ['class' => 'dropdown-menu-right']
                            ]
                        ]
                    )
                ]
            ]

        ],
        'layout' => "{toolbar}\n{items}\n{pager}",
        
        // ...  more grid view configuration stuff here
```

####Custom Types

Please, see the documentation regarding [Exportable Button custom types](../widgets/exportable-button.md#custom-types) 
to find out how you can display your custom type export button so the plugin POSTs that `type` value to the action 
component.


© [2amigos](http://www.2amigos.us/) 2013-2017
