<?php


namespace Kiboko\Plugin\Spreadsheet\Builder;

use Box\Spout\Common\Helper\GlobalFunctionsHelper;
use Box\Spout\Writer\Common\Creator\InternalEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\XLSX\Creator\HelperFactory;
use Box\Spout\Writer\XLSX\Creator\ManagerFactory;
use Box\Spout\Writer\XLSX\Manager\OptionsManager;
use PhpParser\Builder;
use PhpParser\Node;

final class Loader implements Builder
{
    private ?Node\Expr $logger = null;

    public function __construct(
        private Node\Expr $filePath,
        private Node\Expr $sheets,
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
        $entityFactory = new InternalEntityFactory();
        $optionManager = new OptionsManager(new StyleBuilder());
        $helperFactory = new HelperFactory();
        $managerFactory = new ManagerFactory(
            $entityFactory,
            $helperFactory
        );

        $instance = new Node\Expr\New_(
            class: new Node\Name\FullyQualified('Kiboko\\Component\\Flow\\Spreadsheet\\Safe\\Loader'),
            args: [
                new Node\Expr\New_(
                    class: new Node\Name\FullyQualified('Box\Spout\Writer\XLSX\Writer'),
                    args: [
                        new Node\Arg($this->filePath),
                        new Node\Arg(new Node\Scalar\String_('w')),
                        new Node\Arg(new Node\Scalar\String_('w')),
                        new Node\Arg(new Node\Scalar\String_('w')),
                    ]
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
