<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\contracts;

use yii\grid\GridView;

interface ExportableServiceInterface
{
    /**
     * Generates and exports the content to the browser.
     *
     * @param GridView $grid
     * @param string $type the format type to export
     * @param array $columns
     * @param string $filename the name of the file when exporting
     *
     * @return string content
     */
    public function run(GridView $grid, $type, array $columns, $filename);
}
