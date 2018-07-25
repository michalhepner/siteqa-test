<?php

declare(strict_types = 1);

namespace Siteqa\Test\Test;

class TestResult
{
    const STATUS_OK = 'ok';
    const STATUS_SUSPICIOUS = 'suspicious';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'error';
    const STATUS_CRITICAL = 'critical';

    /**
     * @var string
     */
    protected $testName;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var array
     */
    protected $data;

    public function __construct(string $testName, string $status, array $data)
    {
        $this->testName = $testName;
        $this->status = $status;
        $this->data = $data;
    }

    public static function createOk(string $testName, array $data): self
    {
        return new self($testName, self::STATUS_OK, $data);
    }

    public static function createSuspicious(string $testName, array $data): self
    {
        return new self($testName, self::STATUS_SUSPICIOUS, $data);
    }

    public static function createWarning(string $testName, array $data): self
    {
        return new self($testName, self::STATUS_WARNING, $data);
    }

    public static function createError(string $testName, array $data): self
    {
        return new self($testName, self::STATUS_ERROR, $data);
    }

    public static function createCritical(string $testName, array $data): self
    {
        return new self($testName, self::STATUS_CRITICAL, $data);
    }

    public function getTestName(): string
    {
        return $this->testName;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function isStatusOk(): bool
    {
        return $this->status === self::STATUS_OK;
    }

    public function isStatusSuspicious(): bool
    {
        return $this->status === self::STATUS_SUSPICIOUS;
    }

    public function isStatusWarning(): bool
    {
        return $this->status === self::STATUS_WARNING;
    }

    public function isStatusError(): bool
    {
        return $this->status === self::STATUS_ERROR;
    }

    public function isStatusCritical(): bool
    {
        return $this->status === self::STATUS_CRITICAL;
    }
}
