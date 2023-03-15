<?php

declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet\Builder\CSV;

use Kiboko\Contract\Configurator\StepBuilderInterface;
use PhpParser\Node;

final class Extractor implements StepBuilderInterface
{
    private ?Node\Expr $logger = null;

    public function __construct(private readonly Node\Expr $filePath, private readonly Node\Expr $skipLines, private readonly Node\Expr $delimiter, private readonly Node\Expr $enclosure, private readonly Node\Expr $encoding)
    {
    }

    public function withLogger(Node\Expr $logger): self
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
                                        new Node\Expr\Variable('reader'),
                                        new Node\Expr\StaticCall(
                                            class: new Node\Name\FullyQualified(\Box\Spout\Reader\Common\Creator\ReaderEntityFactory::class),
                                            name: 'createCSVReader'
                                        ),
                                    ),
                                ),
                                new Node\Stmt\Expression(
                                    new Node\Expr\MethodCall(
                                        var: new Node\Expr\Variable('reader'),
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
                                        var: new Node\Expr\Variable('reader'),
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
                                        var: new Node\Expr\Variable('reader'),
                                        name: new Node\Identifier('setEncoding'),
                                        args: [
                                            new Node\Arg(
                                                value: $this->encoding
                                            ),
                                        ]
                                    )
                                ),
                                new Node\Stmt\Expression(
                                    new Node\Expr\MethodCall(
                                        var: new Node\Expr\Variable('reader'),
                                        name: new Node\Identifier('open'),
                                        args: [
                                            new Node\Arg(
                                                value: $this->filePath
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
                value: $this->skipLines,
                name: new Node\Identifier('skipLines'),
            ),
            new Node\Arg(
                value: $this->logger ?? new Node\Expr\New_(new Node\Name\FullyQualified(\Psr\Log\NullLogger::class)),
                name: new Node\Identifier('logger'),
            ),
        ];

        return new Node\Expr\New_(
            class: new Node\Name\FullyQualified(\Kiboko\Component\Flow\Spreadsheet\CSV\Safe\Extractor::class),
            args: $arguments
        );
    }
}
