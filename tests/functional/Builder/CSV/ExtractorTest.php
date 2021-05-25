<?php declare(strict_types=1);


namespace functional\Builder\CSV;

use Kiboko\Plugin\Spreadsheet\Builder;
use Kiboko\Component\PHPUnitExtension\BuilderAssertTrait;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;
use Kiboko\Plugin\Log;

class ExtractorTest extends TestCase
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
        $extractor = new Builder\CSV\Extractor(
            filePath: __DIR__.'/../../files/source-to-extract.csv',
            skipLines: 0,
            delimiter: ',',
            enclosure: '"',
            encoding: 'UTF-8'
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
