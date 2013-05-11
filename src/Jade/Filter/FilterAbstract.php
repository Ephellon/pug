<?php
/**
 * @Author      ronan.tessier@vaconsulting.lu
 * @Date        11/05/13
 * @File        AFilter.php
 * @Copyright   Copyright (c) documentation - All rights reserved
 * @Licence     Unauthorized copying of this source code, via any medium is strictly
 *              prohibited, proprietary and confidential.
 */

namespace Jade\Filter;

use Jade\Compiler;
use Jade\Nodes\Filter;

/**
 * Class AFilter
 * @package Jade\Filter
 */
abstract class FilterAbstract {

    /**
     * Returns the node string value, line by line.
     * If the compiler is present, that means we need
     * to interpolate line contents
     * @param Filter $node
     * @param Compiler $compiler
     * @return mixed
     */
    protected function getNodeString(Filter $node, Compiler $compiler = null)
    {
        return array_reduce($node->block->nodes, function(&$result, $line) use($compiler) {
            $val = $compiler ? $compiler->interpolate($line->value) : $line->value;
            return $result .= $val."\n";
        });
    }
}