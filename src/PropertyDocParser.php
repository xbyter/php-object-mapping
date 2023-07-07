<?php

namespace Xbyter\PhpObjectMapping;

class PropertyDocParser
{
    /**
     * 文档属性类型
     *
     * @var array
     */
    protected static array $_properties = [];

    /**
     * 获取对象文档属性类型
     *
     * @param string $className
     * @return array
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function getProperties(string $className): array
    {
        $_properties = &self::$_properties[$className];
        if (isset($_properties)) {
            return self::$_properties[$className];
        }

        $_properties = [];
        $reflection = new \ReflectionClass($className);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            if (!$property->isPublic()) {
                continue;
            }

            $name = $property->getName();

            $_properties[$name] = $propertyInfo = new PropertyInfo();
            $propertyInfo->name = $name;
            $propertyInfo->type = 'string';
            $propertyInfo->class_name = null;
            $propertyInfo->nullable = true;
            $propertyInfo->decorator = null;


            //根据参数Doc文档设置指定对象值
            $doc = $property->getDocComment();
            //解析$type装饰类，用于转换某种具体规则的值。比如转换时区（业务逻辑层以固定时区做逻辑处理，展示层可以加$type转为相应时区值）
            if ($doc) {
                $propertyInfo->decorator = self::getDecoratorByDoc($doc);
            }

            //如果属性是强类型, 则以强类型为主将类型注入到propertyTypes里
            if ($property->hasType() && $property->getType() instanceof \ReflectionNamedType) {
                $typeName = $property->getType()->getName();
                $propertyInfo->type = $typeName;
                $propertyInfo->class_name = $propertyInfo->isScalarType() || $propertyInfo->isArrayType() ? null : $typeName;
                $propertyInfo->nullable = $property->getType()->allowsNull();
                $propertyInfo->type = $typeName;

                //如果不是数组的话则已经确定类型了, 不必往下走
                if (!$propertyInfo->isArrayType()) {
                    continue;
                }
            }


            //非强类型解析
            if ($doc) {
                //解析类似@var \DemoNamespace\DemoProperty[]的文档对象
                preg_match("/@var \??(.*?)(\[\])?[\|\s\*]/", $doc, $matches);

                //解析属性类型到propertyTypes
                //如果未设置强类型, 或者强类型为数组并且注释类型为对象数组时则重新设置类型
                $matchType = $matches[1] ?? '';
                if (!$matchType) {
                    continue;
                }

                //如果注释没有命名空间, 则查看是否再当前命名空间下存在该类
                if (strpos($matchType, "\\") !== 0) {
                    $matchType = $reflection->getNamespaceName() . "\\" . $matchType;
                }

                $isArray = isset($matches[2]);
                if ($isArray && $propertyInfo->type === 'array' && class_exists($matchType)) {
                    $propertyInfo->class_name = $matchType;
                }
            }
        }

        return $_properties;
    }

    /**
     * 获取装饰类
     *
     * @param string $doc
     * @return string|null
     * @throws \Exception
     */
    protected static function getDecoratorByDoc(string $doc): ?string
    {
        //解析类似@return DecoratorInterface 的文档对象
        preg_match("/@return[ ]+([\w\\\]+)/", $doc, $matches);
        $decorator = $matches[1] ?? '';
        if (!$decorator || !class_exists($decorator)) {
            return null;
        }

        return $decorator;
    }
}
