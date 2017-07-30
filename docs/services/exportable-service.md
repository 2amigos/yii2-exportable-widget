Exportable Service
==================

The exportable service creates the contents to be exported from a `yii\grid\GridView` widget instance and sends them as 
a file. It writes directly to the browser stream by making use of the Spout api writers. 

You can use it anywhere. Please, see the code of `dosamigos\exportable\behavior\ExportableBehavior` for a practical 
example of its use.

###Usage


```php 
use dosamigos\exportable\services\ExportableService;

$service = new ExportableService();

// ...

$service->run($gridView, 'txt', $exportableColumns, 'filename.txt'); 

``` 

© [2amigos](http://www.2amigos.us/) 2013-2017
