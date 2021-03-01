<?php

namespace Kiboko\Plugin\Spreadsheet\Builder\OpenDocument;

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
            class: new Node\Name\FullyQualified('Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Extractor'),
            args: [
                new Node\Arg(
                    new Node\Expr\FuncCall(
                        new Node\Expr\Closure(
                            subNodes: [
                                'stmts' => [
                                    new Node\Stmt\Expression(
                                        new Node\Expr\Assign(
                                            new Node\Expr\Variable('reader'),
                                            new Node\Expr\StaticCall(
                                                class: new Node\Name\FullyQualified('Box\Spout\Reader\Common\Creator\ReaderEntityFactory'),
                                                name: 'createXLSXReader'
                                            ),
                                        ),
                                    ),
                                    new Node\Stmt\Expression(
                                        new Node\Expr\MethodCall(
                                            var: new Node\Expr\Variable('reader'),
                                            name: new Node\Identifier('open'),
                                            args: [
                                                new Node\Arg(
                                                    new Node\Scalar\String_($this->filePath)
                                                ),
                                            ]
                                        )
                                    ),
                                    new Node\Stmt\Return_(
                                        new Node\Expr\Variable('reader')
                                    ),
                                ],
                            ],
                        ),
                    ),
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
