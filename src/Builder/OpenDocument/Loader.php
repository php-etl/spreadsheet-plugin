<?php

declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet\Builder\OpenDocument;

use Kiboko\Contract\Configurator\StepBuilderInterface;
use PhpParser\Node;

final class Loader implements StepBuilderInterface
{
    private ?Node\Expr $writer = null;
    private ?Node\Expr $logger = null;

    public function __construct(
        private readonly Node\Expr $filePath,
        private Node\Expr $sheetName
    ) {
    }

    public function withLogger(?Node\Expr $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function withRejection(Node\Expr $rejection): self
    {
        return $this;
    }

    public function withState(Node\Expr $state): self
    {
        return $this;
    }

    public function withSheet(Node\Expr $sheet): self
    {
        $this->sheetName = $sheet;

        return $this;
    }

    public function withWriter(Node\Expr $writer): self
    {
        $this->writer = $writer;

        return $this;
    }

    public function getNode(): Node
    {
        $arguments = [
            new Node\Arg(
                value: $this->writer,
                name: new Node\Identifier('writer'),
            ),
            new Node\Arg(
                value: $this->logger ?? new Node\Expr\New_(new Node\Name\FullyQualified(\Psr\Log\NullLogger::class)),
                name: new Node\Identifier('logger'),
            ),
        ];

        return new Node\Expr\New_(
            class: new Node\Name\FullyQualified(\Kiboko\Component\Flow\Spreadsheet\Sheet\Safe\Loader::class),
            args: $arguments
        );
    }
}
