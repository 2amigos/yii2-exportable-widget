<?php

/*
 * This file is part of the 2amigos/yii2-export-grid-button-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\gridexport\actions;

use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;

/**
 * ExportGridAction provides the functionality to handle form requests for file download to the GridExport javascript
 * plugin.
 */
class ExportGridAction extends Action
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
