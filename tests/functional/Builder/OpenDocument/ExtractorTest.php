<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\Spreadsheet\Builder\OpenDocument;

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
        $extractor = new Builder\OpenDocument\Extractor(
            filePath: 'tests/functional/files/source-to-extract.ods',
            sheet: 'Sheet1',
            skipLine: 0
        );

        $this->assertBuilderProducesAnInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Extractor',
            $extractor
        );

        $this->assertExtractorIteratesAs(
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont'],
            ],
            $extractor
        );
    }

    public function testWithFilePathAndLogger(): void
    {
        $extract = new Builder\OpenDocument\Extractor(
            filePath: 'tests/functional/files/source-to-extract.ods',
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
