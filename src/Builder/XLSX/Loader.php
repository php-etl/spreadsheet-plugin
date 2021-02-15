<?php


namespace Kiboko\Plugin\Spreadsheet\Builder\XLSX;

use PhpParser\Builder;
use PhpParser\Node;

final class Loader implements Builder
{
    private ?Node\Expr $logger;

    public function __construct(
        private Node\Expr $filePath,
        private Node\Expr\Array_ $sheets
    )
    {
        $this->logger = null;
    }

    public function withLogger(?Node\Expr $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function getNode(): Node
    {
        $optionManager = new Node\Expr\New_(
            class: new Node\Name\FullyQualified('Box\Spout\Writer\XLSX\Manager\OptionsManager'),
            args: [
                new Node\Expr\New_(
                    class: new Node\Name\FullyQualified('Box\Spout\Writer\Common\Creator\Style\StyleBuilder'),
                )
            ]
        );

        $functionsHelper = new Node\Expr\New_(
            class: new Node\Name\FullyQualified('Box\Spout\Common\Helper\GlobalFunctionsHelper')
        );

        $helperFactory = new Node\Expr\New_(
            class: new Node\Name\FullyQualified('Box\Spout\Writer\XLSX\Creator\HelperFactory')
        );

        $managerFactory = new Node\Expr\New_(
            class: new Node\Name\FullyQualified('Box\Spout\Writer\XLSX\Creator\ManagerFactory'),
            args: [
                new Node\Expr\New_(
                    class: new Node\Name\FullyQualified('Box\Spout\Writer\Common\Creator\InternalEntityFactory'),
                ),
                $helperFactory
            ]
        );

        $instance = new Node\Expr\New_(
            class: new Node\Name\FullyQualified('Kiboko\\Component\\Flow\\Spreadsheet\\Safe\\Loader'),
            args: [
                new Node\Expr\New_(
                    class: new Node\Name\FullyQualified('Box\Spout\Writer\XLSX\Writer'),
                    args: [
                        new Node\Arg($optionManager),
                        new Node\Arg($functionsHelper),
                        new Node\Arg($helperFactory),
                        new Node\Arg($managerFactory)
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
