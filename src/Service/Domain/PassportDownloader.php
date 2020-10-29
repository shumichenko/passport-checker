<?php

namespace App\Service\Domain;

use App\Service\Domain\PassportHandler;

class PassportDownloader extends PassportHandler
{
    public function updateResource(): bool
    {
        if ($this->downloadResource()) {
            $resourceName = $this->decompressResource(str_replace('.bz2', '.csv', $this->outputFile));
            return $this->splitFileByChunks($resourceName);
        }
        return false;
    }

    /**
     * Downloads passport register resource
     * @return bool Returns true if resource was successfully downloaded
     */
    private function downloadResource(): bool
    {   
        $lastModifiedHash = $this->getRemoteLastModified();
        
        $downloadStatus = false;
        try {
            $downloadStatus = file_put_contents(
                $this->outputFile,
                fopen($this->resourceLocation, 'r')
            );
            if (file_exists($this->outputFile))
                xattr_set($this->outputFile, 'etag', $lastModifiedHash);
        } catch (\Throwable $exception) {
            $this->logger->info($exception->getMessage());
        }
        if ($downloadStatus) {
            $this->logger->info('Remote resource was successfully downloaded.');
            return true;
        }
        return false;
    }

    private function decompressResource(): string
    {
        $extractionFileName = str_replace('.bz2', '.csv', $this->outputFile);
        try {
            if (file_exists($this->outputFile)) {
                $compressedFile = bzopen($this->outputFile, 'r');
                $extractionFile = fopen($extractionFileName, 'w');
                
                while(!feof($compressedFile)) {
                    $decompressedString = bzread($compressedFile, 4096);
                    fwrite($extractionFile, $decompressedString);
                }
                bzclose($compressedFile);
                fclose($extractionFile);
            }
        } catch (\Throwable $exception) {
            $this->logger->info($exception->getMessage());
            return '';
        }
        $this->logger->info('Resource was successfully decompressed');
        return $extractionFileName;
    }

    private function splitFileByChunks(string $extractionFileName): bool
    {
        $file = fopen($extractionFileName, 'r');
        $expiredChunksLocation = $this->chunksLocation . 'expired_chunks/';
        $lastChunksLocation = $this->chunksLocation . 'last_chunks/';
        $newChunksLocation = $this->chunksLocation . 'new_chunks/';
        $count = 0;
        try {
            while (!feof($file)) {
                $row = fgetcsv($file);
                
                $passportSeries = $row[0];
                $passportNumber = $row[1];
                if ((strlen($passportSeries) === 4) && (strlen($passportNumber) === 6)) {
                    if (!is_dir($newChunksLocation))
                        mkdir($newChunksLocation);
                    file_put_contents(
                        $newChunksLocation . $passportSeries . '.csv',
                        $passportNumber . PHP_EOL,
                        FILE_APPEND
                    );   
                }
                $count++;
                if ($count === 100000)
                    break;
            }
        } catch (\Throwable $exception) {
            $this->logger->info($exception->getMessage());
            return false;
        }
        fclose($file);
        
        if (!is_dir($lastChunksLocation))
            mkdir($lastChunksLocation);
        rename($lastChunksLocation, $expiredChunksLocation);
        rename($newChunksLocation, $lastChunksLocation);

        $files = array_diff(scandir($expiredChunksLocation), array('.','..')); 
        foreach ($files as $file) { 
            (is_dir("$expiredChunksLocation/$file")) ? 
                delTree("$expiredChunksLocation/$file") : 
                unlink("$expiredChunksLocation/$file"); 
        } 
        rmdir($expiredChunksLocation);
        
        $this->logger->info('Resource was successfully splitted by chunks');
        return true;
    }
}
