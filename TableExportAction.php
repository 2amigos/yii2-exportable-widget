<?php
/**
 * @copyright Copyright (c) 2014 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\tableexport;

use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;

/**
 * TableExportAction provides the functionality to handle form requests for file download to the TableExport javascript
 * plugin.
 *
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\tableexport
 */
class TableExportAction extends Action
{
    public function run()
    {
        if (Yii::$app->request->isPost) {
            $filename = Yii::$app->request->post('filename');
            $content = Yii::$app->request->post('content');
            $type = Yii::$app->request->post('type');

            $mime = $this->getMimeType($type);
            if ($mime !== false) {
                Yii::$app->getResponse()->getHeaders()
                    ->set('Pragma', 'public')
                    ->set('Expires', '0')
                    ->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                    ->set('Content-Disposition', 'attachment; filename="' . $filename . '.' . $type . '"')
                    ->set('Content-type', $mime . '; charset=utf-8');
                return $content;
            }
        }
        throw new BadRequestHttpException('Your request is invalid.');
    }

    /**
     * Returns the mime type for the
     * @param $type
     *
     * @return bool|string
     */
    protected function getMimeType($type)
    {
        $mime = false;

        switch ($type) {
            case 'csv':
                $mime = 'text/csv';
                break;
            case 'html':
                $mime = 'text/html';
                break;
            case 'excel':
                $mime = 'application/vnd.ms-excel';
                break;
            case 'xml':
                $mime = 'text/xml';
                break;
            case 'json':
                $mime = 'application/json';
                break;
        }
        return $mime;
    }
} 