<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\EntityLoader\FilterIn;

use Arachne\EntityLoader\FilterInInterface;
use Nette\Object;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class BooleanFilterIn extends Object implements FilterInInterface
{
    public function filterIn($value)
    {
        return (bool) $value;
    }
}
