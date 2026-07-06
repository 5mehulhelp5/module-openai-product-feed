<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Writer;

use Magento\Store\Api\Data\StoreInterface;
use Angeo\OpenAiProductFeed\Writer\File\CsvWriter;
use Angeo\OpenAiProductFeed\Writer\File\CsvWriterFactory;

class CsvFileWriterProvider
{
    public const DIRECTORY_PATH = 'angeo/openai_feed/';

    public function __construct(
        private readonly CsvWriterFactory $csvWriterFactory,
    ) {}

    public function provide(StoreInterface $store): CsvWriter
    {
        $destination = self::DIRECTORY_PATH . $store->getCode() . '.csv';

        $fileWriter = $this->csvWriterFactory->create();
        $fileWriter->setFilePath($destination);

        return $fileWriter;
    }
}
