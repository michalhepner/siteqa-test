<?php

declare(strict_types = 1);

namespace Siteqa\Test\Domain\Model;

use DateTime;

class HttpRedirect
{
    /**
     * @var HttpRequest
     */
    protected $from;

    /**
     * @var HttpRequest
     */
    protected $to;

    /**
     * @var DateTime
     */
    protected $created;

    public function __construct(HttpRequest $from, HttpRequest $to, ?DateTime $created = null)
    {
        $this->setFrom($from);
        $this->setTo($to);
        $this->setCreated($created ?? new DateTime('now'));
    }

    public function getFrom(): HttpRequest
    {
        return $this->from;
    }

    public function setFrom(HttpRequest $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): HttpRequest
    {
        return $this->to;
    }

    public function setTo(HttpRequest $to): self
    {
        $this->to = $to;

        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }
}
