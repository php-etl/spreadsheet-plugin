<?php


namespace Kiboko\Plugin\Spreadsheet\Builder\OpenDocument;

use PhpParser\Builder;
use PhpParser\Node;

final class Loader implements Builder
{
    private ?Node\Expr $logger;

    public function __construct(
        private string $filePath,
        private string $sheetName
    )
    {
        $this->logger = null;
    }

    public function withLogger(?Node\Expr $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function withSheet(string $sheet): self
    {
        $this->sheet = $sheet;

        return $this;
    }

    public function getNode(): Node
    {
        $instance = new Node\Expr\New_(
            class: new Node\Name\FullyQualified('Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Loader'),
            args: [
                new Node\Arg(
                    new Node\Expr\MethodCall(
                        new Node\Expr\StaticCall(
                            class: new Node\Name\FullyQualified('Box\Spout\Writer\Common\Creator\WriterEntityFactory'),
                            name: 'createODSWriter'
                        ),
                        name: 'openToFile',
                        args: [
                            new Node\Arg(
                                new Node\Scalar\String_($this->filePath)
                            )
                        ]
                    ),
                ),
                new Node\Arg(
                    new Node\Scalar\String_($this->sheetName)
                )
            ]
        );

        if ($this->logger !== null) {
            return new Node\Expr\MethodCall(
                var: $instance,
                name: 'setLogger',
                args: [
                    new Node\Arg($this->logger),
                ]
            );
        }

        return $instance;
    }
}
