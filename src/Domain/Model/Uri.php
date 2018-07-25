<?php

declare(strict_types = 1);

namespace Siteqa\App\Test\Domain\Model;

use League\Uri\Components\UserInfo;
use League\Uri\Uri as ExternalUri;

class Uri extends ExternalUri
{
    public function getUser(): ?string
    {
        /** @var UserInfo $userInfo */
        $userInfo = (new UserInfo())->withContent($this->getUserInfo());

        return $userInfo->getUser();
    }

    public function getPass(): ?string
    {
        /** @var UserInfo $userInfo */
        $userInfo = (new UserInfo())->withContent($this->getUserInfo());

        return $userInfo->getPass();
    }

    public function hasHost(): bool
    {
        return trim($this->getHost()) !== '';
    }
}
