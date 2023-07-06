<?php

namespace Xbyter\PhpObjectMapping\Traits;

use Xbyter\PhpObjectMapping\PropertySetter;

trait PropertyFillerTrait
{
    /**
     * 将数组配置注入实体对象
     *
     * @param iterable|object $data
     * @param array $parentProperties
     * @return \Xbyter\PhpObjectMapping\Traits\PropertyFillerTrait|\Xbyter\PhpObjectMapping\BaseMapping
     * @throws \ErrorException
     * @throws \ReflectionException|\Throwable
     */
    public function fill($data, array $parentProperties = []): self
    {
        $this->beforeFill($data);
        $this->fillData($data, $parentProperties);
        $this->afterFill();
        return $this;
    }

    /**
     * 将对象数组转为当前对象数组
     *
     * @param iterable|object $data
     * @return self
     * @throws \ErrorException
     * @throws \ReflectionException|\Throwable
     */
    final public static function fromItem($data): self
    {
        $static = new static();
        $static->fill($data);
        return $static;
    }

    /**
     * 将多维数组转为当前对象数组
     *
     * @param array $list
     * @return static[]
     * @throws \ReflectionException|\ErrorException|\Throwable
     */
    final public static function fromList(array $list): array
    {
        $data = [];
        foreach ($list as $item) {
            $data[] = self::fromItem($item);
        }
        return $data;
    }

    /**
     * 填充数据
     *
     * @param iterable|object $data
     * @param array $parentProperties
     * @throws \ErrorException
     * @throws \ReflectionException|\Throwable
     */
    protected function fillData($data, array $parentProperties = []): void
    {
        $setter = new PropertySetter($this);
        $setter->fill($data, function (string $className, $value, array $parentProperties = []) {
            /** @var self $class */
            $class = new $className;
            return $class->fill($value, $parentProperties);
        }, $parentProperties);
    }

    /**
     * 填充前操作
     */
    protected function beforeFill(&$data): void
    {

    }

    /**
     * 填充后操作
     */
    protected function afterFill(): void
    {

    }

}
