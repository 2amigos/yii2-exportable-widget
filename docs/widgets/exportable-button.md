ExportableButton Widget
=======================

This button extends from `yii\bootstrap\ButtonDropdown` widget. So you can use any of the options available there plus 
the following ones: 

- `url`: The action to submit the request to force download the data. The submission posts a `type` and a `export` 
   parameter names. The `type` exposes the file type to download the contents to. The `export` is mostly when using the 
   button in conjunction with the [GridView Library](https://github.com/2amigos/yii2-grid-view-library)'s 
   [ExportableBehavior](../behaviors/exportable-behavior.md) included on this library.
- `label`: The button label. You can set it on HTML, the `encodeLabel` is forcely set to `false`. 
- `types`: Is the types that will be rendered as the button's dropdown options. Its format is set as key=>value array 
   where its `key` is the export type and its `value` the actual label of the button. The default values are: 
   
```php 
public $types = [
    TypeHelper::CSV => 'CSV <span class="label label-default">.csv</span>',
    TypeHelper::XLSX => 'Excel 2007+ <span class="label label-default">.xlsx</span>',
    TypeHelper::ODS => 'Open Document Spreadsheet <span class="label label-default">.ods</span>',
    TypeHelper::JSON => 'JSON <span class="label label-default">.json</span>',
    TypeHelper::XML => 'XML <span class="label label-default">.xml</span>',
    TypeHelper::TXT => 'Text <span class="label label-default">.txt</span>',
    TypeHelper::HTML => 'HTML  <span class="label label-default">.html</span>'
];
```

**Note** If you wish to disable certain exportation types, you just simply configure the button to those you wish to 
to work with: 

```php 
use dosamigos\exportable\ExportableButton; 

echo ExportableButton::widget(
    [
        'label' => '<i class="glyphicon glyphicon-export"></i>',
        'url' => Url::to(['my/download]), // setup the URL
        'options' => ['class' => 'btn-default'],
        'dropdown' => [
            'options' => ['class' => 'dropdown-menu-right']
        ],
        'types' => [
            TypeHelper::CSV => 'CSV <span class="label label-default">.csv</span>',
            TypeHelper::XLSX => 'Excel 2007+ <span class="label label-default">.xlsx</span>',
            TypeHelper::ODS => 'Open Document Spreadsheet <span class="label label-default">.ods</span>',
        ]
    ]
);

```


###Usage

####Using Custom Download Url

See the [ExportableAction guide](../actions/exportable-action.md) for futher information regarding how to configure your 
custom export action.

```php 
use dosamigos\exportable\ExportableButton; 
use yii\helpers\Url;

echo ExportableButton::widget(
    [
        'label' => '<i class="glyphicon glyphicon-export"></i>',
        'url' => Url::to(['my-controller/export]), 
        'options' => ['class' => 'btn-default'],
        'dropdown' => [
            'options' => ['class' => 'dropdown-menu-right']
        ]
    ]
);
```

####Using ExportableBehavior

If you are using [GridView Library](https://github.com/2amigos/yii2-grid-view-library) (recommended), all the default 
export option types are included automatically. You simply need to do the following: 

```php
use dosamigos\exportable\ExportableButton; 
use dosamigos\exportable\behaviors\ExportableBehavior; 
use dosamigos\grid\GridView;
use dosamigos\grid\behaviors\LoadingBehavior;
use dosamigos\grid\behaviors\ToolbarBehavior;
use dosamigos\grid\buttons\ReloadButton;
use yii\helpers\Url;

echo \dosamigos\grid\GridView::widget(
    [
        'behaviors' => [
            'ExportableBehavior',
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
As you can see on the code above, there is not even the need to configure the URL. The widget will automatically 
collect current URL for its submission which is necessary for the `ExportableBehavior`. That behavior will only work 
on current page. 

See the [ExportableBehavior Guide](../behaviors/exportable-behavior.md) for further information regarding this great 
component. 

###Custom Types

The widget allows you to add your very own custom types. These are the steps to configure it. The following example is 
by using our [GridView Library](https://github.com/2amigos/yii2-grid-view-library).

**On your button**

Lets assume we want to provide our very own pdf exporting capabilities as the library doesn't provide that writer: 

```php 
use dosamigos\exportable\ExportableButton; 
use yii\helpers\Url;

echo ExportableButton::widget(
    [
        'label' => '<i class="glyphicon glyphicon-export"></i>',
        'url' => Url::to(['my-controller/export]), 
        'options' => ['class' => 'btn-info'],
        'types' => [
            'pdf' => 'PDF <span class="label label-default">.pdf</span>',
            TypeHelper::CSV => 'CSV <span class="label label-default">.csv</span>',
            TypeHelper::XLSX => 'Excel 2007+ <span class="label label-default">.xlsx</span>',
            TypeHelper::ODS => 'Open Document Spreadsheet <span class="label label-default">.ods</span>',
        ] 
    ]
);
```
**On your Controller**

Now is time to configure our controller: 

```php 
namespace app\controllers;

use dosamigos\exportable\services\DownloadService;
use yii\web\Controller;


class MyController extends Controller {
    
    // ...
    
    public function getActions() {
        return [
            'export' => [
                'class' => '\dosamigos\exportable\actions\ExportableAction',
                'filename' => 'users', // without file extension!
                'contentValue' => function($type) { // could be any callable
                       
                       if($type === 'pdf') {
                            $contents = ... create PDF contents ...
                            
                            return $contents; 
                       }
                       // ... not returning, we redirecting to use behavior for other types :) ...
                       Yii::$app->getResponse()->redirect('where-grid/view-is');
                }
            ]
        ];
    }

    // ...
}

```

**On your View**

```php
use dosamigos\exportable\ExportableButton; 
use dosamigos\exportable\behaviors\ExportableBehavior; 
use dosamigos\grid\GridView;
use dosamigos\grid\behaviors\LoadingBehavior;
use dosamigos\grid\behaviors\ResizableColumnsBehavior;
use dosamigos\grid\behaviors\ToolbarBehavior;
use dosamigos\grid\buttons\ReloadButton;
use yii\helpers\Url;

echo GridView::widget(
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

**Summary**

What we have done is making use of the `ExportableAction` to provide our custom export format. If its not a custom one, 
we redirect to the action that renders the grid, so our `ExportableBehavior` handles the rest of the types configured. 



© [2amigos](http://www.2amigos.us/) 2013-2017
