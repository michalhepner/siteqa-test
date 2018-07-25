<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Event;

use Siteqa\App\Test\Domain\Model\HttpSuite;

class HttpSuiteEvent implements EventInterface
{
    const NAME = 'http_suite';

    /**
     * @var HttpSuite
     */
    protected $suite;

    public function __construct(HttpSuite $suite)
    {
        $this->suite = $suite;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSuite(): HttpSuite
    {
        return $this->suite;
    }
}
