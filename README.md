# php-object-mapping

# 说明
PHP对象映射，可以将array/object映射为类对象。可做参数的传递、Request参数映射、Entity、Bean等。并可根据定义的映射对象生成API文档。

# 示例
```php
class TestMapping extends BaseMapping
{
    /** @var int 总数 */
    public int $total;

    public ?TestItem $item = null;

    /** @var \Xbyter\PhpObjectMappingTests\Data\TestItem[] 列表对象 */
    public array $list = [];

    /** @var string 受保护的值，不会被设置 */
    protected string $protected_key = 'protected value';
}

class TestItem extends BaseMapping
{
    /** @var string|null 标题 */
    public ?string $title = null;

    /**
     * @var string|null 会返回装饰过的值
     * @return TitleDecorator
     */
    public ?string $decorate_title = null;
}

//装饰类
class TitleDecorator implements DecoratorInterface
{

    public function decorate($value)
    {
        return sprintf("this is a decorated value: %s", $value);
    }
}

//映射对象，多维数组可用 TestMapping::fromList([[...], [...]]);
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

//转为数组
$array = $testMapping->toArray();

var_dump($testMapping->total); //10
var_dump($testMapping->item->title); //a
var_dump($testMapping->list[0]->title); //b
var_dump($testMapping->list[0]->decorate_title); //this is a decorated value: c
var_dump($testMapping->protected_key); //Error : Cannot access protected property $protected_key
```