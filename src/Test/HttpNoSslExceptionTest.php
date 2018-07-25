<?php

declare(strict_types = 1);

namespace Siteqa\Test\Test;

use Siteqa\Test\Domain\Model\HttpSuite;

/**
 * Test checking if there were any problems with the SSL cert during the request.
 */
class HttpNoSslExceptionTest implements TestInterface
{
    const NAME = 'http.no_ssl_exception';
    const SSL_CONNECT_ERROR = CURLE_SSL_CONNECT_ERROR;
    const SSL_PEER_CERTIFICATE = CURLE_SSL_PEER_CERTIFICATE;
    const SSL_ENGINE_NOTFOUND = CURLE_SSL_ENGINE_NOTFOUND;
    const SSL_ENGINE_SETFAILED = CURLE_SSL_ENGINE_SETFAILED;
    const SSL_CERTPROBLEM = CURLE_SSL_CERTPROBLEM;
    const SSL_CIPHER = CURLE_SSL_CIPHER;
    const SSL_CACERT = CURLE_SSL_CACERT;
    const SSL_FTP_FAILED = CURLE_FTP_SSL_FAILED;
    const SSL_ENGINE_INITFAILED = 66;
    const SSL_SHUTDOWN_FAILED = 80;
    const SSL_CRL_BADFILE = 82;
    const SSL_ISSUER_ERROR = 83;
    const SSL_PINNEDPUBKEYNOTMATCH = 90;
    const SSL_INVALIDCERTSTATUS = 91;

    /**
     * @var HttpSuite
     */
    protected $httpSuite;

    public function __construct(HttpSuite $httpSuite)
    {
        $this->httpSuite = $httpSuite;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function execute(): TestResult
    {
        $httpSuite = $this->httpSuite;

        return new TestResult(self::NAME, $httpSuite->hasException() ? TestResult::STATUS_ERROR : TestResult::STATUS_OK, [
            'exception' => $httpSuite->hasException() ? $httpSuite->getException()->getErrorMessage() : null,
            'is_ssl_exception' => $httpSuite->hasException() ? in_array($httpSuite->getException()->getErrorNumber(), $this->getSslErrorCodes(), true) : false,
        ]);
    }

    public static function getMessageBuilder(): TestResultMessageBuilderInterface
    {
        return new class implements TestResultMessageBuilderInterface
        {
            public function canBuildMessage(TestResult $testResult): bool
            {
                return $testResult->getTestName() === HttpNoSslExceptionTest::NAME;
            }

            public function buildMessage(TestResult $testResult): string
            {
                if ($testResult->getData()['exception'] !== null) {
                    return $testResult->getData()['is_ssl_exception'] ?
                        sprintf('SSL exception thrown: \'%s\'', $testResult->getData()['exception']) :
                        sprintf('Other exception thrown: \'%s\'', $testResult->getData()['exception'])
                    ;
                }

                return 'No exception thrown';
            }
        };
    }

    protected function getSslErrorCodes(): array
    {
        return [
            self::SSL_CONNECT_ERROR,
            self::SSL_PEER_CERTIFICATE,
            self::SSL_ENGINE_NOTFOUND,
            self::SSL_ENGINE_SETFAILED,
            self::SSL_CERTPROBLEM,
            self::SSL_CIPHER,
            self::SSL_CACERT,
            self::SSL_FTP_FAILED,
            self::SSL_ENGINE_INITFAILED,
            self::SSL_SHUTDOWN_FAILED,
            self::SSL_CRL_BADFILE,
            self::SSL_ISSUER_ERROR,
            self::SSL_PINNEDPUBKEYNOTMATCH,
            self::SSL_INVALIDCERTSTATUS,
        ];
    }
}
