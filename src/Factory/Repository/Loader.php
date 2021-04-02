<?php declare(strict_types=1);


namespace Kiboko\Plugin\Spreadsheet\Factory\Repository;

use Kiboko\Contract\Configurator;
use Kiboko\Plugin\Spreadsheet;

final class Loader implements Configurator\RepositoryInterface
{
    use RepositoryTrait;

    /** @var Configurator\FileInterface[] */
    private array $files;

    /** @var string[] */
    private array $packages;

    public function __construct(private Spreadsheet\Builder\Excel\Loader|Spreadsheet\Builder\OpenDocument\Loader|Spreadsheet\Builder\CSV\Loader $builder)
    {
        $this->files = [];
        $this->packages = [];
    }

    public function getBuilder(): Spreadsheet\Builder\Excel\Loader|Spreadsheet\Builder\OpenDocument\Loader|Spreadsheet\Builder\CSV\Loader
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
