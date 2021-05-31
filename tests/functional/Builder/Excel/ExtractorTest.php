<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\Spreadsheet\Builder\Excel;

use Kiboko\Component\PHPUnitExtension\BuilderAssertTrait;
use Kiboko\Plugin\Spreadsheet\Builder;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;
use PhpParser\Node;

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

    public function testWithoutOption(): void
    {
        $extractor = new Builder\Excel\Extractor(
            filePath: new Node\Scalar\String_(__DIR__.'/../../files/source-to-extract.xlsx'),
            sheetName: new Node\Scalar\String_('Sheet1'),
            skipLines: new Node\Scalar\LNumber(0)
        );

        $this->assertBuilderProducesInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Extractor',
            $extractor
        );

        $this->assertBuilderProducesExtractorIteratesAs(
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont']
            ],
            $extractor
        );
    }
}
