<?php

namespace App\Service\Application;

use Psr\Log\LoggerInterface;
use App\Service\Domain\PassportFetcher;
use App\Service\Domain\PassportDownloader;

class PassportRegisterUpdater
{
    private $passportFetcher;

    private $passportDownloader;

    private $logger;

    public function __construct(
        PassportFetcher $passportFetcher,
        PassportDownloader $passportDownloader,
        LoggerInterface $logger
    ) {
        $this->passportFetcher = $passportFetcher;
        $this->passportDownloader = $passportDownloader;
        $this->logger = $logger;
    }

    /**
     * Updates passport register
     * @return bool Returns true if register was updated
     */
    public function updatePassportRegister(): bool
    {
        if ($this->passportFetcher->isResourceModified()) {
            $this->logger->info('Remote resource was modified. Updating now...');
            return $this->passportDownloader->updateResource();
        }
        return false;
    }
}
