Download Service
================

This class allows sends the specified content as a file to the browser by making use of Yii's 
`Response::sendContentAsFile()` method. You can actually use it anywhere.

Please, see the code of `dosamigos\exportable\action\ExportableAction` for a practical example of its use. 

###Usage

```php 
use dosamigos\exportable\services\DownloadService;

$service = new DownloadService();

$service->run('filename.txt', 'text/plain', 'This is the contents of the file!'); 

``` 


© [2amigos](http://www.2amigos.us/) 2013-2017
