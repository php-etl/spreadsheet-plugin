<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\Spreadsheet\Builder;

use Kiboko\Component\PHPUnitExtension\BuilderAssertTrait;
use Kiboko\Plugin\Spreadsheet\Builder;
use Kiboko\Plugin\Log;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;

final class ExtractorTest extends TestCase
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
        $extract = new Builder\XLSX\Extractor(
            filePath: 'tests/functional/files/source-to-extract.xlsx',
            sheet: 'Sheet1',
            skipLine: 0
        );

        $this->assertBuilderProducesAnInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Extractor',
            $extract
        );

        $this->assertExtractorIteratesAs(
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont'],
            ],
            $extract
        );
    }

    public function testWithFilePathAndLogger(): void
    {
        $extract = new Builder\XLSX\Extractor(
            filePath: 'tests/functional/files/source-to-extract.xlsx',
            sheet: 'Sheet1',
            skipLine: 0
        );

        $extract->withLogger(
            (new Log\Builder\Logger())->getNode()
        );

        $this->assertBuilderHasLogger(
            '\\Psr\\Log\\NullLogger',
            $extract
        );

        $this->assertBuilderProducesAnInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Extractor',
            $extract
        );

        $this->assertExtractorIteratesAs(
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont']
            ],
            $extract
        );
    }
}
