<?php
/**
 * @Author:      余兴
 * @DateTime:    2020/2/17 0017 22:04
 * @Description: 可数组试访问对象
 */

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;

trait RequestValidationTrait
{
    /**
     * The validator instance.
     *
     * @var \Illuminate\Contracts\Validation\Validator
     */
    private Validator $_validator;

    private $_validateData;

    private array $_failedMessages = [];

    /** @var array 自定义验证消息, 你可以通过重写表单请求的 messages 方法来自定义错误消息。此方法应返回属性 / 规则对及其对应错误消息的数组 */
    protected array $messages = [];


    /**
     * 验证规则
     *
     * @param null $data
     */
    public function validate($data = null): void
    {
        if ($data === null && method_exists($this, 'toArray')) {
            $data = $this->toArray();
        }

        $this->_validateData = $data;

        //验证前处理
        $this->prepareForValidation();

        //获取验证器
        $instance = $this->getValidatorInstance();

        //验证是否有错误, 有错误则抛出异常
        if ($instance->fails()) {
            //错误消息
            $this->_failedMessages = $instance->errors()->toArray();

            $this->failedValidation($instance);
        }

        //验证通过后处理
        $this->passedValidation();
    }

    /**
     * 校验之后的操作
     *
     * @return void
     */
    public function afterValidate(): void
    {

    }

    /**
     * 获取错误消息
     * @return array
     */
    public function getFailedMessages(): array
    {
        return $this->_failedMessages;
    }


    /**
     * 制定规则
     *
     * @return array
     */
    final public function rules(): array
    {
        return [];
    }

    /**
     * 验证前处理
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        //
    }


    /**
     * 验证通过后处理
     *
     * @return void
     */
    protected function passedValidation(): void
    {
        //
    }


    /**
     * 获取验证器
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance(): Validator
    {
        if (isset($this->_validator)  && $this->_validator) {
            return $this->_validator;
        }

        $factory = app(ValidationFactory::class);

        $validator = $this->createDefaultValidator($factory);

        $this->_validator = $validator;

        return $this->_validator;
    }

    /**
     * 创建验证器, rules方法可以传入容器对象
     *
     * @param \Illuminate\Contracts\Validation\Factory $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function createDefaultValidator(ValidationFactory $factory)
    {
        return $factory->make($this->_validateData, app()->call([
            $this,
            'rules'
        ]), $this->messages(), $this->attributes());
    }

    /**
     * 设置自定义验证消息, 你可以通过重写表单请求的 messages 方法来自定义错误消息。此方法应返回属性 / 规则对及其对应错误消息的数组
     * return [
     *    'title.required' => 'A title is required',
     *    'body.required'  => 'A message is required',
     * ];
     *
     * @return array
     */
    protected function messages()
    {
        return $this->messages;
    }

    /**
     * 设置自定义验证属性, 如果你希望将验证消息的 :attribute 部分替换为自定义属性名称，则可以重写 attributes 方法来指定自定义名称。此方法应返回属性 / 名称对的数组：
     * return [
     *         'email' => 'email address',
     * ];
     *
     * @return array
     */
    protected function attributes()
    {
        return [];
    }


    protected function failedValidation(Validator $validator)
    {
        \App\Helpers\Validator::validate($validator);
    }

}
