<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\Spreadsheet\Builder\OpenDocument;

use functional\Kiboko\Plugin\Spreadsheet\PipelineRunner;
use Kiboko\Component\PHPUnitExtension\Assert\LoaderBuilderAssertTrait;
use Kiboko\Component\PHPUnitExtension\Assert\PipelineBuilderAssertTrait;
use Kiboko\Contract\Pipeline\PipelineRunnerInterface;
use Kiboko\Plugin\Spreadsheet\Builder;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;
use PhpParser\Node;

final class LoaderTest extends TestCase
{
    use LoaderBuilderAssertTrait;
    use PipelineBuilderAssertTrait;

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

    public function testWritingFile(): void
    {
        $load = new Builder\OpenDocument\Loader(
            filePath: new Node\Scalar\String_('vfs://destination.ods'),
            sheetName: new Node\Scalar\String_('Sheet1')
        );

        $this->assertBuildsLoaderProducesFile(
            __DIR__.'/../../files/expected-to-load.ods',
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont'],
            ],
            $load
        );
    }

    public function testIfLoaderProducedIsRight(): void
    {
        $load = new Builder\OpenDocument\Loader(
            filePath: new Node\Scalar\String_('vfs://destination.ods'),
            sheetName: new Node\Scalar\String_('Sheet1')
        );

        $this->assertBuilderProducesInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Loader',
            $load
        );
    }

    public function testIfLoaderIsNotAnExtractor(): void
    {
        $load = new Builder\OpenDocument\Loader(
            filePath: new Node\Scalar\String_('vfs://destination.ods'),
            sheetName: new Node\Scalar\String_('Sheet1')
        );

        $this->assertBuilderProducesNotInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Extractor',
            $load
        );
    }

    public function testWithoutOption(): void
    {
        $load = new Builder\OpenDocument\Loader(
            filePath: new Node\Scalar\String_('vfs://destination.ods'),
            sheetName: new Node\Scalar\String_('Sheet1')
        );

        $this->assertBuildsLoaderLoadsLike(
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont'],
            ],
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont'],
            ],
            $load
        );
    }

    public function testWithSheet(): void
    {
        $load = new Builder\OpenDocument\Loader(
            filePath: new Node\Scalar\String_('vfs://destination.ods'),
            sheetName: new Node\Scalar\String_('Sheet1')
        );

        $load->withSheet(new Node\Scalar\String_('Sheet1'));

        $this->assertBuildsLoaderLoadsLike(
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont'],
            ],
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont'],
            ],
            $load
        );
    }

    public function pipelineRunner(): PipelineRunnerInterface
    {
        return new PipelineRunner();
    }
}
