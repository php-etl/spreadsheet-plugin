<?php


namespace Kiboko\Plugin\Spreadsheet\Factory\Repository;

use Kiboko\Contract\Configurator;
use Kiboko\Plugin\Spreadsheet;

class Extractor implements Configurator\RepositoryInterface
{
    use RepositoryTrait;

    public function __construct(private Spreadsheet\Builder\XLSX\Extractor|Spreadsheet\Builder\ODS\Extractor $builder)
    {
        $this->files = [];
        $this->packages = [];
    }

    public function getBuilder(): Spreadsheet\Builder\XLSX\Extractor|Spreadsheet\Builder\ODS\Extractor
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
