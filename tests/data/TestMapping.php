<?php

namespace Xbyter\PhpObjectMappingTests\Data;


use Xbyter\PhpObjectMapping\BaseMapping;

class TestMapping extends BaseMapping
{
    /** @var int 总数 */
    public int $total;

    public ?TestItem $item = null;

    /** @var \Xbyter\PhpObjectMappingTests\Data\TestItem[] 列表对象 */
    public array $list = [];

    protected string $protected_key = 'protected value';
}
