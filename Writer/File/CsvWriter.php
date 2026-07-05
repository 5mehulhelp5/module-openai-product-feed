<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Writer\File;

use Angeo\OpenAiProductFeed\Api\Data\OpenAiProductHeadersInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Phrase;

class CsvWriter
{
    private const string DIRECTORY_NAME = 'angeo';
    private ?string $filePath = null;

    public function __construct(
        private readonly Filesystem $filesystem
    ) {}

    /**
     * Writes feed rows. Rows are associative arrays keyed by feed column
     * name and are normalized against the header definition, so column
     * order is always guaranteed and missing values become empty cells.
     *
     * @param array<string, array<string, string>> $products
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function write(array $products): void
    {
        if (empty($this->filePath)) {
            throw new LocalizedException(
                new Phrase('The OpenAi file path is not set')
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
            $stream->writeCsv($this->normalizeRow($product));
        }

        $stream->unlock();
        $stream->close();
    }

    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<int, string>
     */
    private function normalizeRow(array $row): array
    {
        $line = [];

        foreach (OpenAiProductHeadersInterface::HEADERS as $header) {
            $line[] = (string) ($row[$header] ?? '');
        }

        return $line;
    }
}
