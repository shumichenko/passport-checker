<?php

namespace App\Service\Domain;

use App\Service\Domain\PassportHandler;

class PassportFetcher extends PassportHandler
{
    /**
     * Returns true if resource was modified
     * @return bool
     */
    public function isResourceModified(): bool
    {
        $actualHash = $this->getRemoteLastModified();
        $previousHash = $this->getLocalLastModified();

        if ($actualHash) {
            if ($previousHash !== $actualHash)
                return true;
            else
                $this->logger->info('Remote resource is already up to date.');
        }
        return false;
    }

    /**
     * Returns remote resource last modified hash or empty string
     * @return string Last modified hash
     */
    public function getLocalLastModified(): string
    {
        if (file_exists($this->outputFile))
            $hash = \xattr_get($this->outputFile, 'etag');
        
        return $hash ?? '';
    }

    public function fetchPassport(string $passportSeries, string $passportNumber): bool
    {
        $chunkNumber = '/home/igor_elephant/Server/passport_register/src/Resource/passports/last_chunks/' . $passportSeries . '.csv';
        if (strlen($passportSeries) !== 4 || strlen($passportNumber) !== 6)
            return false;
        if (file_exists($chunkNumber)) {
            $file = fopen($chunkNumber, 'r');
            while (($raw_string = fgets($file)) !== false) {
                $row = str_getcsv($raw_string);
                if (intval($passportNumber) === intval($raw_string))
                    return true;
            }
        }
        return false;
    }
}
