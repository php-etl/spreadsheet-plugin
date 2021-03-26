<?php declare(strict_types=1);


namespace Kiboko\Plugin\Spreadsheet\Builder\Excel;

use PhpParser\Builder;
use PhpParser\Node;

final class Loader implements Builder
{
    private ?Node\Expr $logger;

    public function __construct(
        private string $filePath,
        private string $sheetName
    ) {
        $this->logger = null;
    }

    public function withLogger(?Node\Expr $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function withSheet(string $sheet): self
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
                            new Node\Scalar\String_($this->filePath)
                        )
                    ]
                ),
                name: new Node\Identifier('writer'),
            ),
            new Node\Arg(
                value: new Node\Scalar\String_($this->sheetName),
                name: new Node\Identifier('sheetName'),
            ),
        ];

        if ($this->logger !== null) {
            array_push(
                $arguments,
                new Node\Arg(
                    value: $this->logger,
                    name: new Node\Identifier('logger'),
                ),
            );
        }

        return new Node\Expr\New_(
            class: new Node\Name\FullyQualified('Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Loader'),
            args: $arguments
        );
    }
}
