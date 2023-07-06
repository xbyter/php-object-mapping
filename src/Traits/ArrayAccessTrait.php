<?php

namespace Xbyter\PhpObjectMapping\Traits;

trait ArrayAccessTrait
{
    public function offsetSet($offset, $value): void
    {
        $this->{$offset} = $value;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->{$offset});
    }

    public function offsetUnset($offset): void
    {
        unset($this->{$offset});
    }

    public function offsetGet($offset)
    {
        return $this->{$offset};
    }
}
