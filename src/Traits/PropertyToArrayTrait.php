<?php

namespace Xbyter\PhpObjectMapping\Traits;

use Xbyter\PhpObjectMapping\PropertyDocParser;

trait PropertyToArrayTrait
{
    /**
     * 转化为数组形式, 非公开属性和未初始化的属性会被忽略
     *
     * @return array
     * @throws \JsonException
     */
    public function toArray(): array
    {
        return json_decode(json_encode($this, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \JsonException
     */
    public function toJson(): string
    {
        return json_encode($this, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }


    /**
     * 获取属性集合
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function getProperties(): array
    {
        return PropertyDocParser::getProperties(static::class);
    }


    /**
     * 获取属性集合
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function getPropertyNames(): array
    {
        return array_keys(static::getProperties());
    }
}
