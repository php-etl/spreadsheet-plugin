<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\Spreadsheet\Builder\OpenDocument;

use Kiboko\Component\PHPUnitExtension\BuilderAssertTrait;
use Kiboko\Plugin\Spreadsheet\Builder;
use Kiboko\Plugin\Log;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;

final class LoaderTest extends TestCase
{
    use BuilderAssertTrait;

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
            filePath: 'vfs://destination.ods',
            sheetName: 'Sheet1'
        );

        $this->assertBuilderProducesLoaderWritingFile(
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
            filePath: 'vfs://destination.ods',
            sheetName: 'Sheet1'
        );

        $this->assertBuilderProducesInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Loader',
            $load
        );
    }

    public function testIfLoaderIsNotAnExtractor(): void
    {
        $load = new Builder\OpenDocument\Loader(
            filePath: 'vfs://destination.ods',
            sheetName: 'Sheet1'
        );

        $this->assertBuilderProducesNotInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Extractor',
            $load
        );
    }

    public function testWithoutOption(): void
    {
        $load = new Builder\OpenDocument\Loader(
            filePath: 'vfs://destination.ods',
            sheetName: 'Sheet1'
        );

        $this->assertBuilderProducesPipelineLoadingLike(
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

    public function testWithLogger(): void
    {
        $load = new Builder\OpenDocument\Loader(
            filePath: 'vfs://destination.ods',
            sheetName: 'Sheet1'
        );

        $load->withLogger(
            (new Log\Builder\Logger())->getNode()
        );

        $this->assertBuilderProducesPipelineLoadingLike(
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
            filePath: 'vfs://destination.ods',
            sheetName: 'Sheet1'
        );

        $load->withSheet('Sheet1');

        $this->assertBuilderProducesPipelineLoadingLike(
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
}
