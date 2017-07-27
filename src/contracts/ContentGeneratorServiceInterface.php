<?php

/*
 * This file is part of the 2amigos/yii2-export-grid-button-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportgrid\contracts;

use yii\grid\GridView;

interface ContentGeneratorServiceInterface
{
    /**
     * Generates the content for the file to download.
     *
     * @param GridView $grid
     * @param string $type the format type to export
     * @param array $columns
     *
     * @return string content
     */
    public function run(GridView $grid, $type, array $columns);
}
