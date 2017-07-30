Exportable Behavior
===================

This behavior implements the `dosamigos\grid\contracts\RunnableBehaviorInterface` of our our 
[GridView Library](https://github.com/2amigos/yii2-grid-view-library). This interface allows behaviors attached to the 
grid to be executing code after the GridView's `run()` method. 

> **Note** You should really checkout the [GridView Library](https://github.com/2amigos/yii2-grid-view-library). Its 
> behaviors mechanism makes it the most flexible and extendable grid view widget for Yii2. 

The great thing about this behavior is that it automatically checks whether there is an `export` post parameter with the 
value of `1` in order to do anything at all. That is, nothing is initialized but the behavior class if no actions are to 
be taken.

That is actually the reason why we created our very own exporting library, the ones that are currently in use 
have far too much initialization code and is really hard to actually improve without breaking stuff. 

It is super simple to use and will provide your application with the amazing speed of the [Spout library](http://opensource.box.com/spout/) 
and its great API that will allow you to create your very own custom writers. Plus, 
[its great memory management](http://opensource.box.com/spout/faq/). 


###Usage

The library works with [GridView Library](https://github.com/2amigos/yii2-grid-view-library). The following is how you 
configure it: 

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

Done... Really, that's it. 

###The Exportable Service

The behavior makes use of a `dosamigos\exportable\services\ExportableService`. You can actually create your very own 
by creating a class that implements `dosamigos\exportable\contracts\ExportableServiceInterface` and set its 
`exportableService` attribute. 

Please, visit [how to override behavivor's functionality guide](../guides/how-to-override-behavior-functionality.md) for 
further information on how to implement your own exportable service. 


© [2amigos](http://www.2amigos.us/) 2013-2017
