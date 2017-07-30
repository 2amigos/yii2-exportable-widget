How to Override Behavior Functionality
======================================

When exporting, the behavior makes use of the [dosamigos\exportable\services\ExportableService](../services/exportable-service.md) 
to create the contents and force download the file. If you look at the class's code, you see that it implements the 
`dosamigos\exportable\contracts\ExportableServiceInterface` interface. That means, that you can create your very own 
exportable service class as long as it implements that interface and set it to the `exportableService` attribute of the 
behavior. 

For the sake of the example, we will image we create a PdfWriter (no code will be included)

###Create your PdfWriter 

Writers extend from `Box\Spout\Writer\AbstractWriter` and require to override three methods:

```php
namespace app\components\writers;

use Box\Spout\Writer\AbstractWriter;
 
class PdfWriter extends AbstractWriter
{

    protected function openWriter()
    {
        // ... init code for pdf writer
    }

    protected function addRowToWriter(array $dataRow, $style)
    {
        // ... write code to stream handler ($this->filePointer)
    }

    protected function closeWriter()
    {
        // .. end code for pdf writer
    }

```
###Create your ExportableService

```php 
namespace app\components\services;

use app\components\writers\PdfWriter;
use dosamigos\exportable\contracts\ExportableServiceInterface;
use dosamigos\exportable\factory\WriterFactory;
use dosamigos\exportable\helpers\TypeHelper;
use dosamigos\exportable\iterators\DataProviderIterator;
use dosamigos\exportable\iterators\SourceIterator;
use dosamigos\exportable\mappers\ColumnValueMapper;
use Yii;
use yii\data\BaseDataProvider;
use yii\grid\GridView;

class MyCustomExportableService implements ExportableServiceInterface 
{
    /**
     * @inheritdoc
     */
    public function run(GridView $grid, $type, array $columns, $filename)
    {
        /** @var BaseDataProvider $dataProvider */
        $dataProvider = $grid->dataProvider;
        // Ff using mpdf we could actually state that is HTML as it converts from HTML to PDF.
        $isHtml = $type === TypeHelper::HTML || $type === 'pdf';
        $mapper = new ColumnValueMapper($grid->columns, $columns, $isHtml);
        $source = new SourceIterator(new DataProviderIterator($dataProvider, $mapper));
        $model = $dataProvider->getTotalCount() > 0 ? $dataProvider->models[0] : null;
        
        if('pdf' === $type) {
            $writer = new PdfWriter(); // your writer!!!
        } else {
            $writer = WriterFactory::create($type); // rollback to defaults
        }

        $this->clearBuffers();
        $writer->openToBrowser($filename);
        if ($model !== null && !in_array($type, [TypeHelper::JSON, TypeHelper::XML])) {
            $writer->addRow($mapper->getHeaders($model));
        }
        foreach ($source as $data) {
            $writer->addRow($data);
        }
        $writer->close();

        Yii::$app->end();
    }
}

```

###Add Custom Type & Service

```php
use app\components\services\MyCustomExportableService;
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
            [
                'class' => 'ExportableBehavior',
                'exportableService' => new MyCustomExportableService() // Your service! Yay!
            ],
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
                            'options' => ['class' => 'btn-default'],
                            'dropdown' => [
                                'options' => ['class' => 'dropdown-menu-right']
                            ],
                            'types' => [
                                'pdf' => 'PDF <span class="label label-default">.pdf</span>',
                                TypeHelper::CSV => 'CSV <span class="label label-default">.csv</span>',
                                TypeHelper::XLSX => 'Excel 2007+ <span class="label label-default">.xlsx</span>',
                                TypeHelper::ODS => 'Open Document Spreadsheet <span class="label label-default">.ods</span>',
                            ] 
                        ]
                    )
                ]
            ]

        ],
        'layout' => "{toolbar}\n{items}\n{pager}",
        
        // ...  more grid view configuration stuff here
```



© [2amigos](http://www.2amigos.us/) 2013-2017
