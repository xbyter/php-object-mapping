<?php

namespace Xbyter\PhpObjectMapping;

use Xbyter\PhpObjectMapping\Exceptions\PropertySetError;

class PropertySetter
{
    /** @var object 要设置的类 */
    protected object $class;



    public function __construct(object $class)
    {
        $this->class = $class;
    }

    /**
     * 根据属性类型填充数据
     *
     * @param iterable|object $data
     * @param \Closure $newInstanceCallback
     * @param array $parentProperties
     * @param int $depth
     * @return void
     * @throws \ErrorException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function fill($data, \Closure $newInstanceCallback, array $parentProperties, int $depth = 512): void
    {
        if (!is_iterable($data) && !is_object($data)) {
            throw new \ErrorException($this->getIterableTypeErrorMessage($parentProperties));
        }

        if (count($parentProperties) > $depth) {
            throw new \ErrorException(sprintf("Maximum depth is %s", $depth));
        }

        $properties = PropertyDocParser::getProperties(get_class($this->class));
        foreach ($data as $propertyName => $propertyValue) {
            //获取完整的属性名称(包含父属性)
            $fullProperties = [...$parentProperties, $propertyName];

            if (!array_key_exists($propertyName, $properties)) {
                continue;
            }

            try {
                $property = $properties[$propertyName];
                //如果没有包含类对象的话则直接设置值
                if (!$property['class_name']) {
                    $this->setPropertyValue($propertyName, $propertyValue, $property['decorator']);
                    continue;
                }

                //数组对象循环实例化
                if (PropertyDocParser::isArrayType($property['type'])) {
                    if (!is_iterable($propertyValue) && !is_object($propertyValue)) {
                        throw new \ErrorException($this->getIterableTypeErrorMessage($fullProperties));
                    }

                    $values = [];
                    foreach ($propertyValue as $item) {
                        $values[] = $newInstanceCallback($property['class_name'], $item, $fullProperties);
                    }
                    $this->setPropertyValue($propertyName, $values, $property['decorator']);
                } else {
                    $value = $newInstanceCallback($property['class_name'], $propertyValue, $fullProperties);
                    $this->setPropertyValue($propertyName, $value, $property['decorator']);
                }
            } catch (PropertySetError $e) {
                throw $e;
            } catch (\Throwable $e) {
                throw new PropertySetError($e, $fullProperties);
            }
        }
    }


    /**
     * 设置属性值
     *
     * @param string $property
     * @param $value
     * @param string|null $decoratorName
     */
    protected function setPropertyValue(string $property, $value, ?string $decoratorName = null): void
    {
        if ($decoratorName) {
            //如果有装饰器, 则转为最新的值再赋值
            $decorator = new $decoratorName();
            if ($decorator instanceof \Xbyter\PhpObjectMapping\Interfaces\DecoratorInterface) {
                $value = $decorator->decorate($value);
            }
        }
        $this->class->{$property} = $value;
    }


    /**
     * 获取不可迭代的错误消息
     * @param array $parentProperties
     * @return string
     */
    private function getIterableTypeErrorMessage(array $parentProperties): string
    {
        $message = $parentProperties ? '%s is not iterable data' : 'Not iterable data';
        return sprintf($message, implode('.', $parentProperties));
    }
}
