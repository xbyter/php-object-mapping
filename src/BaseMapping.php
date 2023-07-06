<?php

namespace Xbyter\PhpObjectMapping;

use Xbyter\PhpObjectMapping\Traits\PropertyFillerTrait;
use Xbyter\PhpObjectMapping\Traits\PropertyToArrayTrait;

abstract class BaseMapping
{
    use PropertyToArrayTrait, PropertyFillerTrait;
}
