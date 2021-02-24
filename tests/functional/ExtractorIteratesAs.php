<?php


namespace Test\Kiboko\Plugin\Spreadsheet\functional;

use PhpParser\Builder;
use PhpParser\Node;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\Constraint\Constraint;
use function sprintf;

class ExtractorIteratesAs extends Constraint
{
    private array $lines;

    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return sprintf(
            'file iterates %s',
            \json_encode($this->lines)
        );
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param Builder $other value or object to evaluate
     * @return bool
     * @throws \Exception
     */
    protected function matches($other): bool
    {
        $printer = new PrettyPrinter\Standard();

        try {
            $filename = 'vfs://' . hash('sha512', random_bytes(512)) .'.php';

            file_put_contents($filename, $printer->prettyPrintFile([
                new Node\Stmt\Return_($other->getNode())
            ]));

            $extractor = include $filename;
        } catch (\Error $exception) {
            $this->fail($printer->prettyPrintExpr($other->getNode()), $exception->getMessage());
        }

        $result = [];
        foreach ($extractor->extract() as $line) {
            $result[] = $line;
        }

        return $result === $this->lines;
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param Builder $other evaluated value or object
     *
     * @return string
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    protected function failureDescription($other): string
    {
        return sprintf(
            'The following generated code should iterate like %s'.PHP_EOL.'%s',
            \json_encode($this->lines),
            (new PrettyPrinter\Standard())->prettyPrint([$other->getNode()]),
        );
    }
}
