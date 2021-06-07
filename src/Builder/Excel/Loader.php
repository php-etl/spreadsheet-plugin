<?php declare(strict_types=1);


namespace Kiboko\Plugin\Spreadsheet\Builder\Excel;

use Kiboko\Contract\Configurator\StepBuilderInterface;
use PhpParser\Node;

final class Loader implements StepBuilderInterface
{
    private ?Node\Expr $logger;
    private ?Node\Expr $rejection;
    private ?Node\Expr $state;

    public function __construct(
        private Node\Expr $filePath,
        private Node\Expr $sheetName
    ) {
        $this->logger = null;
        $this->rejection = null;
        $this->state = null;
    }

    public function withLogger(?Node\Expr $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function withRejection(Node\Expr $rejection): self
    {
        $this->rejection = $rejection;

        return $this;
    }

    public function withState(Node\Expr $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function withSheet(Node\Expr $sheet): self
    {
        $this->sheetName = $sheet;

        return $this;
    }

    public function getNode(): Node
    {
        $arguments = [
            new Node\Arg(
                value: new Node\Expr\MethodCall(
                    new Node\Expr\StaticCall(
                        class: new Node\Name\FullyQualified('Box\Spout\Writer\Common\Creator\WriterEntityFactory'),
                        name: 'createXLSXWriter'
                    ),
                    name: 'openToFile',
                    args: [
                        new Node\Arg(
                            value: $this->filePath,
                        )
                    ]
                ),
                name: new Node\Identifier('writer'),
            ),
            new Node\Arg(
                value: $this->sheetName,
                name: new Node\Identifier('sheetName'),
            ),
            new Node\Arg(
                value: $this->logger ?? new Node\Expr\New_(new Node\Name\FullyQualified('Psr\\Log\\NullLogger')),
                name: new Node\Identifier('logger'),
            ),
        ];

        return new Node\Expr\New_(
            class: new Node\Name\FullyQualified('Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Loader'),
            args: $arguments
        );
    }
}
