<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Domain\Model;

class HttpHeader
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string[]
     */
    protected $values;

    public function __construct(string $name, array $values)
    {
        $this->setName($name);
        $this->setValues($values);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): self
    {
        $this->values = $values;

        return $this;
    }
}
