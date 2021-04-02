<?php declare(strict_types=1);


namespace Kiboko\Plugin\Spreadsheet\Builder\CSV;

use PhpParser\Builder;
use PhpParser\Node;

final class Loader implements Builder
{
    private ?Node\Expr $logger = null;

    public function __construct(
        private string $filePath,
        private string $delimiter = ',',
        private string $enclosure = '"'
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
                value : new Node\Expr\FuncCall(
                    new Node\Expr\Closure(
                        subNodes: [
                    'stmts' => [
                        new Node\Stmt\Expression(
                            new Node\Expr\Assign(
                                new Node\Expr\Variable('writer'),
                                new Node\Expr\StaticCall(
                                    class: new Node\Name\FullyQualified('Box\Spout\Writer\Common\Creator\WriterEntityFactory'),
                                    name: 'createCSVWriter'
                                ),
                            ),
                        ),
                        new Node\Stmt\Expression(
                            new Node\Expr\MethodCall(
                                var: new Node\Expr\Variable('writer'),
                                name: new Node\Identifier('setFieldDelimiter'),
                                args: [
                                    new Node\Arg(
                                        value: new Node\Scalar\String_($this->delimiter)
                                    ),
                                ]
                            )
                        ),
                        new Node\Stmt\Expression(
                            new Node\Expr\MethodCall(
                                var: new Node\Expr\Variable('writer'),
                                name: new Node\Identifier('setFieldEnclosure'),
                                args: [
                                    new Node\Arg(
                                        value: new Node\Scalar\String_($this->enclosure)
                                    ),
                                ]
                            )
                        ),
                        new Node\Stmt\Expression(
                            new Node\Expr\MethodCall(
                                var: new Node\Expr\Variable('writer'),
                                name: new Node\Identifier('openToFile'),
                                args: [
                                    new Node\Arg(
                                        value: new Node\Scalar\String_($this->filePath)
                                    ),
                                ]
                            )
                        ),
                        new Node\Stmt\Return_(
                            new Node\Expr\Variable('writer')
                        ),
                    ],
                ],
                    ),
                ),
                name: new Node\Identifier('writer'),
            )
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
