<?php

declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet\Factory\Repository;

use Kiboko\Contract\Configurator;
use Kiboko\Plugin\Spreadsheet;

final class Loader implements Configurator\StepRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(private readonly Spreadsheet\Builder\Excel\Loader
                                        |Spreadsheet\Builder\Excel\MultipleFileLoader
                                        |Spreadsheet\Builder\OpenDocument\Loader
                                        |Spreadsheet\Builder\OpenDocument\MultipleFileLoader
                                        |Spreadsheet\Builder\CSV\Loader
                                        |Spreadsheet\Builder\CSV\MultipleFileLoader $builder)
    {
        $this->files = [];
        $this->packages = [];
    }

    public function getBuilder(): Spreadsheet\Builder\Excel\Loader
    |Spreadsheet\Builder\Excel\MultipleFileLoader
    |Spreadsheet\Builder\OpenDocument\Loader
    |Spreadsheet\Builder\OpenDocument\MultipleFileLoader
    |Spreadsheet\Builder\CSV\Loader
    |Spreadsheet\Builder\CSV\MultipleFileLoader
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
