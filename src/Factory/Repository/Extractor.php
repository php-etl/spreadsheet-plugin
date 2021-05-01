<?php declare(strict_types=1);


namespace Kiboko\Plugin\Spreadsheet\Factory\Repository;

use Kiboko\Contract\Configurator;
use Kiboko\Plugin\Spreadsheet;

final class Extractor implements Configurator\StepRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(private Spreadsheet\Builder\Excel\Extractor|Spreadsheet\Builder\OpenDocument\Extractor|Spreadsheet\Builder\CSV\Extractor $builder)
    {
        $this->files = [];
        $this->packages = [];
    }

    public function getBuilder(): Spreadsheet\Builder\Excel\Extractor|Spreadsheet\Builder\OpenDocument\Extractor|Spreadsheet\Builder\CSV\Extractor
    {
        return $this->builder;
    }

    public function merge(Configurator\RepositoryInterface $friend): self
    {
        array_push($this->files, ...$friend->getFiles());
        array_push($this->packages, ...$friend->getPackages());

        return $this;
    }
}
