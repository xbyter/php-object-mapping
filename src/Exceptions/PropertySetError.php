<?php

namespace Xbyter\PhpObjectMapping\Exceptions;

class PropertySetError extends \TypeError
{
    /** @var string[] */
    protected array $properties;


    public function __construct(\Throwable $e, array $properties)
    {
        $this->properties = $properties;
        parent::__construct($e->getMessage(), 0, $e);
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
