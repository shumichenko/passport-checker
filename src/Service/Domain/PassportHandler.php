<?php

namespace App\Service\Domain;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class PassportHandler
{
    protected $client;

    protected $logger;

    protected $outputFile;

    protected $resourceLocation;

    protected $chunksLocation;

    public function __construct(
        HttpClientInterface $client,
        LoggerInterface $logger,
        string $outputFile,
        string $resourceLocation,
        string $chunksLocation
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->outputFile = $outputFile;
        $this->resourceLocation = $resourceLocation;
        $this->chunksLocation = $chunksLocation;
    }

    /**
     * Returns remote resource last modified hash or empty string
     * @return string Last modified hash
     */
    protected function getRemoteLastModified(): string
    {
        $response = '';
        $actualHash = '';
        try {
            $response = $this->client->request(
                'HEAD',
                $this->resourceLocation
            );
            $actualHash = $response->getHeaders()['etag'][0] ?? '';
        } catch (\Throwable $exception) {
            $this->logger->info($exception->getMessage());
        }
        return $actualHash;
    }
}