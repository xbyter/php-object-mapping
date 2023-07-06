<?php
namespace Xbyter\PhpObjectMappingTests;

use PHPUnit\Framework\TestCase;
use Xbyter\PhpObjectMapping\Exceptions\PropertySetError;
use Xbyter\PhpObjectMappingTests\Data\TestMapping;

class MappingTest extends TestCase
{
    /**
     * 测试填充数据，并且可以用对象/数组方式访问正确的数据
     * @throws \ReflectionException
     * @throws \Throwable
     * @throws \ErrorException
     */
    public function testFill(): void
    {
        $testMapping = TestMapping::fromItem([
            'total' => 10,
            'item'  => [
                'title' => 'a'
            ],
            'list'  => [
                [
                    'title' => 'b',
                    'decorate_title' => 'c',
                ]
            ],
            'protected_key' => '受保护的字段不会被赋值',
        ]);

        $this->assertSame($testMapping->total, 10);
        $this->assertSame($testMapping->item->title, 'a');
        $this->assertSame($testMapping->list[0]->title, 'b');
        $this->assertSame($testMapping->list[0]->decorate_title, 'this is a decorated value: c');
        $this->assertNotTrue(isset($testMapping->protected_key));
    }

    /**
     * 测试转为数组的值是否正确
     * @return void
     * @throws \ErrorException
     * @throws \JsonException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function testToArray(): void
    {
        $testMapping = TestMapping::fromItem([
            'total' => 10,
            'item'  => [
                'title' => 'a'
            ],
            'list'  => [
                [
                    'title' => 'b',
                    'decorate_title' => 'c',
                ]
            ],
            'protected_key' => '受保护的字段不会被赋值',
        ])->toArray();

        $this->assertIsArray($testMapping);
        $this->assertSame($testMapping['total'], 10);
        $this->assertSame($testMapping['item']['title'], 'a');
        $this->assertSame($testMapping['list'][0]['title'], 'b');
        $this->assertSame($testMapping['list'][0]['decorate_title'], 'this is a decorated value: c');
        $this->assertArrayNotHasKey('protected_key', $testMapping);
    }
}