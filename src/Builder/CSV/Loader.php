<?php

declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet\Builder\CSV;

use Kiboko\Contract\Configurator\StepBuilderInterface;
use PhpParser\Node;

final class Loader implements StepBuilderInterface
{
    private ?Node\Expr $logger = null;

    public function __construct(private readonly Node\Expr $filePath, private readonly Node\Expr $delimiter, private readonly Node\Expr $enclosure)
    {
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

    public function getNode(): Node
    {
        $arguments = [
            new Node\Arg(
                value: new Node\Expr\FuncCall(
                    new Node\Expr\Closure(
                        subNodes: [
                            'stmts' => [
                                new Node\Stmt\Expression(
                                    new Node\Expr\Assign(
                                        new Node\Expr\Variable('writer'),
                                        new Node\Expr\StaticCall(
                                            class: new Node\Name\FullyQualified(\Box\Spout\Writer\Common\Creator\WriterEntityFactory::class),
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
                                                value: $this->delimiter
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
                                                value: $this->enclosure
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
                                                value: $this->filePath
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
            ),
            new Node\Arg(
                value: $this->logger ?? new Node\Expr\New_(new Node\Name\FullyQualified(\Psr\Log\NullLogger::class)),
                name: new Node\Identifier('logger'),
            ),
        ];

        return new Node\Expr\New_(
            class: new Node\Name\FullyQualified(\Kiboko\Component\Flow\Spreadsheet\CSV\Safe\Loader::class),
            args: $arguments
        );
    }
}
