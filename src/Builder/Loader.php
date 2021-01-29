<?php


namespace Kiboko\Plugin\Spreadsheet\Builder;

use PhpParser\Builder;
use PhpParser\Node;

final class Loader implements Builder
{
    private ?Node\Expr $logger = null;

    public function __construct(
        private Node\Expr $filePath,
        //private Node\Expr $sheets,
        private Node\Expr $delimiter,
        private Node\Expr $enclosure,
        private Node\Expr $escape,
    )
    {
    }

    public function withLogger(?Node\Expr $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function getNode(): Node
    {
        $instance = new Node\Expr\New_(
            class: new Node\Name\FullyQualified('Kiboko\\Component\\Flow\\Spreadsheet\\Safe\\Loader'),
            args: [
                new Node\Expr\New_(
                    class: new Node\Name\FullyQualified('SplFileObject'),
                    args: [
                        new Node\Arg($this->filePath),
                        new Node\Arg(new Node\Scalar\String_('w')),
                    ]
                ),
                new Node\Arg($this->delimiter),
                new Node\Arg($this->enclosure),
                new Node\Arg($this->escape),
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
