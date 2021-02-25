<?php declare(strict_types=1);


namespace functional\Kiboko\Plugin\Spreadsheet\Builder;

use functional\Kiboko\Plugin\Spreadsheet;
use PhpParser\Builder as DefaultBuilder;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;

abstract class BuilderTestCase extends TestCase
{
    private ?FileSystem $fs = null;

    protected function setUp(): void
    {
        $this->fs = FileSystem::factory('vfs://');
        $this->fs->mount();
    }

    protected function tearDown(): void
    {
        $this->fs->unmount();
        $this->fs = null;
    }

    protected function assertBuilderProducesAnInstanceOf(string $expected, DefaultBuilder $builder, string $message = '')
    {
        static::assertThat(
            $builder,
            new Spreadsheet\BuilderProducesAnInstanceOf($expected),
            $message
        );
    }

    protected function assertBuilderNotProducesAnInstanceOf(string $expected, DefaultBuilder $builder, string $message = '')
    {
        static::assertThat(
            $builder,
            new LogicalNot(
                new Spreadsheet\BuilderProducesAnInstanceOf($expected),
            ),
            $message
        );
    }

    protected function assertBuilderHasLogger(string $expected, DefaultBuilder $builder, string $message = '')
    {
        static::assertThat(
            $builder,
            new Spreadsheet\BuilderHasLogger($expected),
            $message
        );
    }

    protected function assertExtractorIteratesAs(array $expected, DefaultBuilder $builder, string $message = '')
    {
        static::assertThat(
            $builder,
            new Spreadsheet\ExtractorIteratesAs($expected),
            $message
        );
    }

    protected function assertLoaderProducesFile(string $expected, string $actual, DefaultBuilder $builder, array $input, string $message = '')
    {
        static::assertThat(
            $builder,
            new Spreadsheet\LoaderProducesFile($expected, $actual, $input),
            $message
        );
    }
}
