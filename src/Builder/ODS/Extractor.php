<?php


namespace Kiboko\Plugin\Spreadsheet\Builder\ODS;


use PhpParser\Builder;
use PhpParser\Node;

class Extractor implements Builder
{
    private ?Node\Expr $logger = null;

    public function __construct(
        private string $filePath,
        private string $sheet,
        private int $skipLine
    ) {
    }

    public function withLogger(?Node\Expr $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function getNode(): Node
    {
        $instance = new Node\Expr\New_(
            class: new Node\Name\FullyQualified('Kiboko\\Component\\Flow\\Spreadsheet\\Safe\\Extractor'),
            args: [
                new Node\Arg(
                    new Node\Expr\MethodCall(
                        var: new Node\Expr\StaticCall(
                        class: new Node\Name\FullyQualified('Box\Spout\Reader\Common\Creator\ReaderEntityFactory'),
                        name: 'createODSReader'
                    ),
                        name: 'open',
                        args: [
                            new Node\Arg(
                                new Node\Scalar\String_($this->filePath)
                            )
                        ]
                    )
                ),
                new Node\Arg(
                    new Node\Scalar\String_($this->sheet)
                ),
                new Node\Arg(
                    new Node\Scalar\LNumber($this->skipLine)
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
