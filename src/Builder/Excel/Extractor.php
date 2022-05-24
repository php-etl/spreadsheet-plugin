<?php

declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet\Builder\Excel;

use Kiboko\Contract\Configurator\StepBuilderInterface;
use PhpParser\Node;

final class Extractor implements StepBuilderInterface
{
    private ?Node\Expr $logger;

    public function __construct(
        private Node\Expr $filePath,
        private Node\Expr $sheetName,
        private Node\Expr $skipLines
    ) {
        $this->logger = null;
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
                                                value: $this->filePath,
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
                value: $this->sheetName,
                name: new Node\Identifier('sheetName'),
            ),
            new Node\Arg(
                value: $this->skipLines,
                name: new Node\Identifier('skipLines'),
            ),
            new Node\Arg(
                value: $this->logger ?? new Node\Expr\New_(new Node\Name\FullyQualified('Psr\\Log\\NullLogger')),
                name: new Node\Identifier('logger'),
            ),
        ];

        return new Node\Expr\New_(
            class: new Node\Name\FullyQualified('Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Extractor'),
            args: $arguments
        );
    }
}
