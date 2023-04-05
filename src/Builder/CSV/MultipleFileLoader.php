<?php

declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet\Builder\CSV;

use Kiboko\Contract\Configurator\StepBuilderInterface;
use PhpParser\Node;

final class MultipleFileLoader implements StepBuilderInterface
{
    private ?Node\Expr $logger = null;

    public function __construct(
        private readonly Node\Expr $filePath,
        private Node\Expr $maxLines,
        private readonly Node\Expr $delimiter,
        private readonly Node\Expr $enclosure,
        private bool $safeMode = true
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

    public function withMaxLines(Node\Expr $maxLines): self
    {
        $this->maxLines = $maxLines;

        return $this;
    }

    public function withSafeMode(): self
    {
        $this->safeMode = true;

        return $this;
    }

    public function getNode(): Node
    {
        $arguments = [
            new Node\Arg(
                value: new Node\Expr\FuncCall(
                    new Node\Expr\Closure(
                        subNodes: [
                            'uses' => [
                                new Node\Expr\Variable('index'),
                            ],
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
            new Node\Stmt\Class_(
                name: null,
                subNodes: [
                    'implements' => [
                        new Node\Name\FullyQualified(\Kiboko\Contract\Pipeline\LoaderInterface::class),
                        new Node\Name\FullyQualified(\Kiboko\Contract\Pipeline\FlushableInterface::class),
                    ],
                    'stmts' => [
                        new Node\Stmt\Property(
                            flags: 4,
                            props: [
                                new Node\Stmt\PropertyProperty(
                                    name: new Node\Name('loader')
                                ),
                            ],
                            type: new Node\Name\FullyQualified(\Kiboko\Contract\Pipeline\LoaderInterface::class)
                        ),
                        new Node\Stmt\ClassMethod(
                            name: new Node\Identifier(name: '__construct'),
                            subNodes: [
                                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                                'stmts' => [
                                    new Node\Stmt\Expression(
                                        expr: new Node\Expr\Assign(
                                            var: new Node\Expr\Variable('index'),
                                            expr: new Node\Scalar\LNumber(0)
                                        )
                                    ),
                                    new Node\Stmt\Expression(
                                        expr: new Node\Expr\Assign(
                                            var: new Node\Expr\PropertyFetch(
                                                var: new Node\Expr\Variable('this'),
                                                name: new Node\Name('loader')
                                            ),
                                            expr: new Node\Expr\New_(
                                                class: new Node\Name\FullyQualified(
                                                    $this->safeMode
                                                        ? \Kiboko\Component\Flow\Spreadsheet\CSV\Safe\Loader::class
                                                        : \Kiboko\Component\Flow\Spreadsheet\CSV\FingersCrossed\Loader::class,
                                                ),
                                                args: $arguments
                                            ),
                                        )
                                    ),
                                ],
                            ]
                        ),
                        new Node\Stmt\ClassMethod(
                            name: new Node\Identifier(name: 'load'),
                            subNodes: [
                                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                                'returnType' => new Node\Name\FullyQualified(\Generator::class),
                                'stmts' => [
                                    new Node\Stmt\Expression(
                                        expr: new Node\Expr\Assign(
                                            var: new Node\Expr\Variable('coroutine'),
                                            expr: new Node\Expr\MethodCall(
                                                var: new Node\Expr\Variable('this'),
                                                name: new Node\Name('coroutineFactory'),
                                                args: [
                                                    new Node\Arg(
                                                        value: new Node\Expr\Assign(
                                                            var: new Node\Expr\Variable('index'),
                                                            expr: new Node\Scalar\LNumber(0)
                                                        ),
                                                    ),
                                                ],
                                            ),
                                        ),
                                    ),
                                    new Node\Stmt\Expression(
                                        expr: new Node\Expr\Assign(
                                            var: new Node\Expr\Variable('readLines'),
                                            expr: new Node\Scalar\LNumber(0)
                                        )
                                    ),
                                    new Node\Stmt\Expression(
                                        expr: new Node\Expr\Assign(
                                            var: new Node\Expr\Variable('line'),
                                            expr: new Node\Expr\Yield_()
                                        )
                                    ),
                                    new Node\Stmt\Do_(
                                        cond: new Node\Expr\Assign(
                                            var: new Node\Expr\Variable('line'),
                                            expr: new Node\Expr\Yield_(
                                                value: new Node\Expr\Variable('bucket')
                                            )
                                        ),
                                        stmts: [
                                            new Node\Stmt\If_(
                                                cond: new Node\Expr\BinaryOp\GreaterOrEqual(
                                                    new Node\Expr\PostInc(
                                                        var: new Node\Expr\Variable('readLines')
                                                    ),
                                                    $this->maxLines
                                                ),
                                                subNodes: [
                                                    'stmts' => [
                                                        new Node\Stmt\Expression(
                                                            new Node\Expr\Assign(
                                                                var: new Node\Expr\Variable('coroutine'),
                                                                expr: new Node\Expr\MethodCall(
                                                                    var: new Node\Expr\Variable('this'),
                                                                    name: new Node\Identifier('coroutineFactory'),
                                                                    args: [
                                                                        new Node\Arg(
                                                                            value: new Node\Expr\PreInc(
                                                                                var: new Node\Expr\Variable('index'),
                                                                            ),
                                                                        ),
                                                                    ],
                                                                ),
                                                            ),
                                                        ),
                                                        new Node\Stmt\Expression(
                                                            new Node\Expr\Assign(
                                                                new Node\Expr\Variable('readLines'),
                                                                new Node\Scalar\LNumber(0),
                                                            ),
                                                        ),
                                                    ],
                                                ],
                                            ),
                                            new Node\Stmt\Expression(
                                                new Node\Expr\Assign(
                                                    var: new Node\Expr\Variable('bucket'),
                                                    expr: new Node\Expr\MethodCall(
                                                        var: new Node\Expr\Variable('coroutine'),
                                                        name: new Node\Identifier('send'),
                                                        args: [
                                                            new Node\Arg(
                                                                value: new Node\Expr\Variable('line')
                                                            ),
                                                        ],
                                                    ),
                                                ),
                                            ),
                                        ],
                                    ),
                                    new Node\Stmt\Expression(
                                        new Node\Expr\Yield_(
                                            value: new Node\Expr\Variable('bucket')
                                        ),
                                    ),
                                ],
                            ],
                        ),
                        new Node\Stmt\ClassMethod(
                            name: new Node\Identifier(name: 'coroutineFactory'),
                            subNodes: [
                                'flags' => Node\Stmt\Class_::MODIFIER_PRIVATE,
                                'returnType' => new Node\Name\FullyQualified(\Generator::class),
                                'params' => [
                                    new Node\Param(
                                        var: new Node\Expr\Variable('index'),
                                        type: new Node\Identifier('int')
                                    ),
                                ],
                                'stmts' => [
                                    new Node\Stmt\Expression(
                                        new Node\Expr\MethodCall(
                                            var: new Node\Expr\PropertyFetch(
                                                var: new Node\Expr\Variable('this'),
                                                name: new Node\Identifier('loader')
                                            ),
                                            name: new Node\Identifier('flush')
                                        ),
                                    ),
                                    new Node\Stmt\Expression(
                                        new Node\Expr\Assign(
                                            var: new Node\Expr\PropertyFetch(
                                                var: new Node\Expr\Variable('this'),
                                                name: new Node\Identifier('loader')
                                            ),
                                            expr: new Node\Expr\New_(
                                                class: new Node\Name\FullyQualified(
                                                    $this->safeMode
                                                    ? \Kiboko\Component\Flow\Spreadsheet\CSV\Safe\Loader::class
                                                    : \Kiboko\Component\Flow\Spreadsheet\CSV\FingersCrossed\Loader::class,
                                                ),
                                                args: $arguments
                                            ),
                                        ),
                                    ),
                                    new Node\Stmt\Expression(
                                        new Node\Expr\Assign(
                                            var: new Node\Expr\Variable('coroutine'),
                                            expr: new Node\Expr\MethodCall(
                                                var: new Node\Expr\PropertyFetch(
                                                    var: new Node\Expr\Variable('this'),
                                                    name: new Node\Identifier('loader')
                                                ),
                                                name: new Node\Identifier('load')
                                            ),
                                        ),
                                    ),
                                    new Node\Stmt\Expression(
                                        new Node\Expr\MethodCall(
                                            var: new Node\Expr\Variable('coroutine'),
                                            name: new Node\Identifier('rewind')
                                        ),
                                    ),
                                    new Node\Stmt\Return_(
                                        expr: new Node\Expr\Variable('coroutine'),
                                    ),
                                ],
                            ],
                        ),
                        new Node\Stmt\ClassMethod(
                            name: new Node\Identifier('flush'),
                            subNodes: [
                                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                                'returnType' => new Node\Name\FullyQualified(\Kiboko\Contract\Bucket\ResultBucketInterface::class),
                                'stmts' => [
                                    new Node\Stmt\Expression(
                                        new Node\Expr\MethodCall(
                                            var: new Node\Expr\PropertyFetch(
                                                var: new Node\Expr\Variable('this'),
                                                name: new Node\Identifier('loader')
                                            ),
                                            name: new Node\Identifier('flush')
                                        )
                                    ),
                                    new Node\Stmt\Return_(
                                        new Node\Expr\New_(
                                            new Node\Name\FullyQualified(\Kiboko\Component\Bucket\EmptyResultBucket::class)
                                        ),
                                    ),
                                ],
                            ],
                        ),
                    ],
                ],
            ),
        );
    }
}
