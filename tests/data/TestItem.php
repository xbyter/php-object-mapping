<?php

namespace Xbyter\PhpObjectMappingTests\Data;


use Xbyter\PhpObjectMapping\BaseMapping;


class TestItem extends BaseMapping
{
    /** @var string|null 标题 */
    public ?string $title = null;

    /**
     * @var string|null 会返回装饰过的值
     * @return \Xbyter\PhpObjectMappingTests\Data\TitleDecorator
     */
    public ?string $decorate_title = null;
}
