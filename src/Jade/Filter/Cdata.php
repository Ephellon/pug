<?php
/**
 * @Author      ronan.tessier@vaconsulting.lu
 * @Date        11/05/13
 * @File        Cdata.php
 * @Copyright   Copyright (c) jadephp - All rights reserved
 * @Licence     Unauthorized copying of this source code, via any medium is strictly
 *              prohibited, proprietary and confidential.
 */

namespace Jade\Filter;

use Jade\Compiler;
use Jade\Nodes\Filter;

class Cdata Extends FilterAbstract {

    public function __invoke(Filter $node, Compiler $compiler)
    {
        return "<!CDATA[\n".$this->getNodeString($node, $compiler)."\n]]>";
    }

}