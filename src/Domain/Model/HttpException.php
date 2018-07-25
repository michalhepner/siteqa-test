<?php

declare(strict_types = 1);

namespace Siteqa\Test\Domain\Model;

use DateTime;

class HttpException
{
    /**
     * @var int|null
     */
    private $errorNumber;

    /**
     * @var string|null
     */
    private $errorMessage;

    /**
     * @var string|null
     */
    private $url;

    /**
     * @var string|null
     */
    private $contentType;

    /**
     * @var int|null
     */
    private $httpCode;

    /**
     * @var int|null
     */
    private $headerSize;

    /**
     * @var int|null
     */
    private $requestSize;

    /**
     * @var int|null
     */
    private $fileTime;

    /**
     * @var int|null
     */
    private $sslVerifyResult;

    /**
     * @var int|null
     */
    private $redirectCount;

    /**
     * @var double|null
     */
    private $totalTime;

    /**
     * @var double|null
     */
    private $nameLookupTime;

    /**
     * @var double|null
     */
    private $connectTime;

    /**
     * @var double|null
     */
    private $preTransferTime;

    /**
     * @var double|null
     */
    private $sizeUpload;

    /**
     * @var double|null
     */
    private $sizeDownload;

    /**
     * @var double|null
     */
    private $speedDownload;

    /**
     * @var double|null
     */
    private $speedUpload;

    /**
     * @var double|null
     */
    private $downloadContentLength;

    /**
     * @var double|null
     */
    private $uploadContentLength;

    /**
     * @var double|null
     */
    private $startTransferTime;

    /**
     * @var double|null
     */
    private $redirectTime;

    /**
     * @var array|null
     */
    private $certInfo;

    /**
     * @var string|null
     */
    private $requestHeader;

    /**
     * @var DateTime
     */
    private $created;

    public function __construct(?DateTime $created = null)
    {
        $this->setCreated($created ?? new DateTime('now'));
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

    public function getErrorNumber(): ?int
    {
        return $this->errorNumber;
    }

    public function setErrorNumber(?int $errorNumber): self
    {
        $this->errorNumber = $errorNumber;

        return $this;
    }

    public function hasErrorNumber(): bool
    {
        return $this->errorNumber !== null;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function hasErrorMessage(): bool
    {
        return $this->errorMessage !== null;
    }

    public function setErrorMessage(?string $errorMessage): self
    {
        $errorMessage = trim((string) $errorMessage);
        $this->errorMessage = $errorMessage !== '' ? $errorMessage : null;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function hasUrl(): bool
    {
        return $this->url !== null;
    }

    public function setUrl(?string $url): self
    {
        $url = trim((string) $url);
        $this->url = $url !== '' ? $url : null;

        return $this;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function hasContentType(): bool
    {
        return $this->contentType !== null;
    }

    public function setContentType(?string $contentType): self
    {
        $contentType = trim((string) $contentType);
        $this->contentType = $contentType !== '' ? $contentType : null;

        return $this;
    }

    public function getHttpCode(): ?int
    {
        return $this->httpCode;
    }

    public function hasHttpCode(): bool
    {
        return $this->httpCode !== null;
    }

    public function setHttpCode(?int $httpCode): self
    {
        $this->httpCode = $httpCode;

        return $this;
    }

    public function getHeaderSize(): ?int
    {
        return $this->headerSize;
    }

    public function hasHeaderSize(): bool
    {
        return $this->headerSize !== null;
    }

    public function setHeaderSize(?int $headerSize): self
    {
        $this->headerSize = $headerSize;

        return $this;
    }

    public function getRequestSize(): ?int
    {
        return $this->requestSize;
    }

    public function hasRequestSize(): bool
    {
        return $this->requestSize !== null;
    }

    public function setRequestSize(?int $requestSize): self
    {
        $this->requestSize = $requestSize;

        return $this;
    }

    public function getFileTime(): ?int
    {
        return $this->fileTime;
    }

    public function hasFileTime(): bool
    {
        return $this->fileTime !== null;
    }

    public function setFileTime(?int $fileTime): self
    {
        $this->fileTime = $fileTime;

        return $this;
    }

    public function getSslVerifyResult(): ?int
    {
        return $this->sslVerifyResult;
    }

    public function hasSslVerifyResult(): bool
    {
        return $this->sslVerifyResult !== null;
    }

    public function setSslVerifyResult(?int $sslVerifyResult): self
    {
        $this->sslVerifyResult = $sslVerifyResult;

        return $this;
    }

    public function getRedirectCount(): ?int
    {
        return $this->redirectCount;
    }

    public function hasRedirectCount(): bool
    {
        return $this->redirectCount !== null;
    }

    public function setRedirectCount(?int $redirectCount): self
    {
        $this->redirectCount = $redirectCount;

        return $this;
    }

    public function getTotalTime(): ?float
    {
        return $this->totalTime;
    }

    public function hasTotalTime(): bool
    {
        return $this->totalTime !== null;
    }

    public function setTotalTime(?float $totalTime): self
    {
        $this->totalTime = $totalTime;

        return $this;
    }

    public function getNameLookupTime(): ?float
    {
        return $this->nameLookupTime;
    }

    public function hasNameLookupTime(): bool
    {
        return $this->nameLookupTime !== null;
    }

    public function setNameLookupTime(?float $nameLookupTime): self
    {
        $this->nameLookupTime = $nameLookupTime;

        return $this;
    }

    public function getConnectTime(): ?float
    {
        return $this->connectTime;
    }

    public function hasConnectTime(): bool
    {
        return $this->connectTime !== null;
    }

    public function setConnectTime(?float $connectTime): self
    {
        $this->connectTime = $connectTime;

        return $this;
    }

    public function getPreTransferTime(): ?float
    {
        return $this->preTransferTime;
    }

    public function hasPreTransferTime(): bool
    {
        return $this->preTransferTime !== null;
    }

    public function setPreTransferTime(?float $preTransferTime): self
    {
        $this->preTransferTime = $preTransferTime;

        return $this;
    }

    public function getSizeUpload(): ?float
    {
        return $this->sizeUpload;
    }

    public function hasSizeUpload(): bool
    {
        return $this->sizeUpload !== null;
    }

    public function setSizeUpload(?float $sizeUpload): self
    {
        $this->sizeUpload = $sizeUpload;

        return $this;
    }

    public function getSizeDownload(): ?float
    {
        return $this->sizeDownload;
    }

    public function hasSizeDownload(): bool
    {
        return $this->sizeDownload !== null;
    }

    public function setSizeDownload(?float $sizeDownload): self
    {
        $this->sizeDownload = $sizeDownload;

        return $this;
    }

    public function getSpeedDownload(): ?float
    {
        return $this->speedDownload;
    }

    public function hasSpeedDownload(): bool
    {
        return $this->speedDownload !== null;
    }

    public function setSpeedDownload(?float $speedDownload): self
    {
        $this->speedDownload = $speedDownload;

        return $this;
    }

    public function getSpeedUpload(): ?float
    {
        return $this->speedUpload;
    }

    public function hasSpeedUpload(): bool
    {
        return $this->speedUpload !== null;
    }

    public function setSpeedUpload(?float $speedUpload): self
    {
        $this->speedUpload = $speedUpload;

        return $this;
    }

    public function getDownloadContentLength(): ?float
    {
        return $this->downloadContentLength;
    }

    public function hasDownloadContentLength(): bool
    {
        return $this->downloadContentLength !== null;
    }

    public function setDownloadContentLength(?float $downloadContentLength): self
    {
        $this->downloadContentLength = $downloadContentLength;

        return $this;
    }

    public function getUploadContentLength(): ?float
    {
        return $this->uploadContentLength;
    }

    public function hasUploadContentLength(): bool
    {
        return $this->uploadContentLength !== null;
    }

    public function setUploadContentLength(?float $uploadContentLength): self
    {
        $this->uploadContentLength = $uploadContentLength;

        return $this;
    }

    public function getStartTransferTime(): ?float
    {
        return $this->startTransferTime;
    }

    public function hasStartTransferTime(): bool
    {
        return $this->startTransferTime !== null;
    }

    public function setStartTransferTime(?float $startTransferTime): self
    {
        $this->startTransferTime = $startTransferTime;

        return $this;
    }

    public function getRedirectTime(): ?float
    {
        return $this->redirectTime;
    }

    public function hasRedirectTime(): bool
    {
        return $this->redirectTime !== null;
    }

    public function setRedirectTime(?float $redirectTime): self
    {
        $this->redirectTime = $redirectTime;

        return $this;
    }

    public function getCertInfo(): ?array
    {
        return $this->certInfo;
    }

    public function hasCertInfo(): bool
    {
        return $this->certInfo !== null;
    }

    public function setCertInfo(?array $certInfo): self
    {
        $this->certInfo = $certInfo;

        return $this;
    }

    public function getRequestHeader(): ?string
    {
        return $this->requestHeader;
    }

    public function hasRequestHeader(): bool
    {
        return $this->requestHeader !== null;
    }

    public function setRequestHeader(?string $requestHeader): self
    {
        $requestHeader = trim((string) $requestHeader);
        $this->requestHeader = $requestHeader !== '' ? $requestHeader : null;

        return $this;
    }
}
