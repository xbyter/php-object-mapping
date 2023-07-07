<?php

namespace Xbyter\PhpObjectMapping;

class PropertyInfo
{
    /** @var string 属性名称 */
    public string $name;

    /** @var string 属性类型, 同反射类型。array,string,int,float,bool,object,类名 */
    public string $type;

    /** @var string|null 类名 */
    public ?string $class_name = null;

    /** @var bool 是否可空 */
    public bool $nullable = true;

    /** @var string|null 装饰器 */
    public ?string $decorator = null;


    public function isStringType(): bool
    {
        return $this->type === 'string';
    }

    public function isIntType(): bool
    {
        return $this->type === 'int';
    }

    public function isFloatType(): bool
    {
        return $this->type === 'float';
    }

    public function isBoolType(): bool
    {
        return $this->type === 'bool';
    }

    public function isArrayType(): bool
    {
        return $this->type === 'array';
    }

    /**
     * 是否是标量（简单类型）
     *
     * @param
     * @return bool
     */
    public function isScalarType(): bool
    {
        return $this->isStringType() || $this->isIntType() || $this->isFloatType() || $this->isBoolType();
    }


    public function isNumberType(): bool
    {
        return $this->isIntType() || $this->isFloatType();
    }

    public function isClassType(): bool
    {
        return $this->class_name && !$this->isArrayType();
    }

    public function isClassArrayType(array $property): bool
    {
        return $this->class_name && $this->isArrayType();
    }
}
