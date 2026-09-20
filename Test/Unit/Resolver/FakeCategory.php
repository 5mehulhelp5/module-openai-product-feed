<?php

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Test\Unit\Resolver;

/**
 * Minimal category fixture (id / name / path) used by the resolver tests.
 */
class FakeCategory
{
    public function __construct(private int $id, private string $name, private string $path) {}
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getPath() { return $this->path; }
}
