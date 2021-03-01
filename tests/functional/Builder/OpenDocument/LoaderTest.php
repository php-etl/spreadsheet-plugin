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

    public function testWithFilePath(): void
    {
        $load = new Builder\OpenDocument\Loader(
            filePath: 'vfs://destination.ods',
            sheetName: 'Sheet1'
        );

        $this->assertBuilderProducesAnInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Loader',
            $load
        );

        $this->assertLoaderProducesFile(
            'tests/functional/files/expected-to-load.ods',
            'vfs://destination.ods',
            $load,
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont'],
            ]
        );
    }

    public function testWithFilePathAndLogger(): void
    {
        $load = new Builder\OpenDocument\Loader(
            filePath: 'vfs://destination.ods',
            sheetName: 'Sheet1'
        );

        $load->withLogger(
            (new Log\Builder\Logger())->getNode()
        );

        $this->assertBuilderHasLogger(
            '\\Psr\\Log\\NullLogger',
            $load
        );

        $this->assertBuilderProducesAnInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Loader',
            $load
        );

        $this->assertLoaderProducesFile(
            'tests/functional/files/expected-to-load.xslx',
            'vfs://destination.ods',
            $load,
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont'],
            ]
        );
    }
}