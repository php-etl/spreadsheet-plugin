<?php


namespace Kiboko\Plugin\Spreadsheet\Builder\CSV;

use PhpParser\Builder;
use PhpParser\Node;

class Extractor implements Builder
{
    private ?Node\Expr $logger = null;

    public function __construct(
        private string $filePath,
        private int $skipLine,
        private string $delimiter,
        private string $enclosure,
        private string $encoding
    ) {
    }

    public function withLogger(Node\Expr $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function getNode(): Node
    {
        $arguments = [
            new Node\Arg(
                value : new Node\Expr\FuncCall(
                    new Node\Expr\Closure(
                    subNodes: [
                    'stmts' => [
                        new Node\Stmt\Expression(
                            new Node\Expr\Assign(
                                new Node\Expr\Variable('reader'),
                                new Node\Expr\New_(
                                    class: new Node\Name('Box\Spout\Reader\CSV\Reader')
                                ),
                            ),
                        ),
                        new Node\Stmt\Expression(
                            new Node\Expr\MethodCall(
                                var: new Node\Expr\Variable('reader'),
                                name: new Node\Identifier('open'),
                                args: [
                                    new Node\Arg(
                                        value: new Node\Scalar\String_($this->filePath)
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
                name: new Node\Identifier('reader'),
            ),
            new Node\Arg(
                value: new Node\Scalar\LNumber($this->skipLine),
                name: new Node\Identifier('skipLines'),
            ),
            new Node\Arg(
                value: new Node\Scalar\String_($this->delimiter),
                name: new Node\Identifier('delimiter'),
            ),
            new Node\Arg(
                value: new Node\Scalar\String_($this->enclosure),
                name: new Node\Identifier('enclosure'),
            ),
            new Node\Arg(
                value: new Node\Scalar\String_($this->encoding),
                name: new Node\Identifier('encoding'),
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
            class: new Node\Name\FullyQualified('Kiboko\\Component\\Flow\\Spreadsheet\\CSV\\Safe\\Extractor'),
            args: $arguments
        );
    }
}
