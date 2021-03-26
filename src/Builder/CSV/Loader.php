<?php


namespace Kiboko\Plugin\Spreadsheet\Builder\CSV;

use PhpParser\Builder;
use PhpParser\Node;

class Loader implements Builder
{
    private ?Node\Expr $logger = null;

    public function __construct(
        private string $filePath,
        private string $delimiter,
        private string $enclosure
    ) {
    }

    public function withLogger(?Node\Expr $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function getNode(): Node
    {
        $arguments = [
            new Node\Arg(
                value: new Node\Expr\MethodCall(
                    new Node\Expr\StaticCall(
                        class: new Node\Name\FullyQualified('Box\Spout\Writer\Common\Creator\WriterEntityFactory'),
                        name: 'createCSVWriter'
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
                value: new Node\Scalar\String_($this->delimiter),
                name: new Node\Identifier('delimiter'),
            ),
            new Node\Arg(
                value: new Node\Scalar\String_($this->enclosure),
                name: new Node\Identifier('enclosure'),
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
            class: new Node\Name\FullyQualified('Kiboko\\Component\\Flow\\Spreadsheet\\CSV\\Safe\\Loader'),
            args: $arguments
        );
    }
}
