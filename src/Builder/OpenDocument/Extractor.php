<?php declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet\Builder\OpenDocument;

use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Contract\Configurator\StepBuilderInterface;
use PhpParser\Node;
use PhpParser\ParserFactory;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class Extractor implements StepBuilderInterface
{
    private ?Node\Expr $logger;
    private ?Node\Expr $rejection;
    private ?Node\Expr $state;

    public function __construct(
        private string|Expression $filePath,
        private string|Expression $sheetName,
        private int $skipLines,
        private ?ExpressionLanguage $interpreter = null
    ) {
        $this->logger = null;
        $this->rejection = null;
        $this->state = null;
    }

    public function withLogger(Node\Expr $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function withRejection(Node\Expr $rejection): self
    {
        $this->rejection = $rejection;

        return $this;
    }

    public function withState(Node\Expr $state): self
    {
        $this->state = $state;

        return $this;
    }

    private function compileValue(string|Expression $value): Node\Expr
    {
        if (is_string($value)) {
            return new Node\Scalar\String_(value: $value);
        }
        if ($value instanceof Expression) {
            $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, null);
            return $parser->parse('<?php ' . $this->interpreter->compile($value, ['input']) . ';')[0]->expr;
        }

        throw new InvalidConfigurationException(
            message: 'Could not determine the correct way to compile the provided filter.',
        );
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
                                        name: 'createODSReader'
                                    ),
                                ),
                            ),
                            new Node\Stmt\Expression(
                                new Node\Expr\MethodCall(
                                    var: new Node\Expr\Variable('reader'),
                                    name: new Node\Identifier('open'),
                                    args: [
                                        new Node\Arg(
                                            value: $this->compileValue($this->filePath),
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
                value: $this->compileValue($this->sheetName),
                name: new Node\Identifier('sheetName'),
            ),
            new Node\Arg(
                value: new Node\Scalar\LNumber($this->skipLines),
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
