<?php

namespace Xbyter\PhpObjectMappingTests\Data;


use Xbyter\PhpObjectMapping\Interfaces\DecoratorInterface;


class TitleDecorator implements DecoratorInterface
{

    public function decorate($value)
    {
        return sprintf("this is a decorated value: %s", $value);
    }
}
