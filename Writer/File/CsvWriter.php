<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Writer\File;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Phrase;
use Angeo\OpenAiProductFeed\Api\Data\OpenAiProductHeadersInterface;

class CsvWriter
{
    private const string DIRECTORY_NAME = 'angeo';
    private ?string $filePath = null;

    public function __construct(
        private readonly Filesystem $filesystem
    ) {}

    /**
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function write(array $products): void
    {
        if (empty($this->filePath)) {
            throw new LocalizedException(
                new Phrase('The OpenAi file path  is not set')
            );
        }

        if (empty($products)) {
            return;
        }

        $directory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $directory->create(self::DIRECTORY_NAME);
        $stream = $directory->openFile($this->filePath, 'w+');
        $stream->lock();

        $stream->writeCsv(OpenAiProductHeadersInterface::HEADERS);

        foreach ($products as $product) {
            $stream->writeCsv($product);
        }

        $stream->unlock();
        $stream->close();
    }

    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }
}
