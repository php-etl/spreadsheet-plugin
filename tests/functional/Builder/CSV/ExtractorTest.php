<?php declare(strict_types=1);


namespace functional\Builder\CSV;

use Kiboko\Plugin\Spreadsheet\Builder;
use Kiboko\Component\PHPUnitExtension\BuilderAssertTrait;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;
use PhpParser\Node;

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
            filePath: new Node\Scalar\String_(__DIR__.'/../../files/source-to-extract.csv'),
            skipLines: new Node\Scalar\LNumber(0),
            delimiter: new Node\Scalar\String_(','),
            enclosure: new Node\Scalar\String_('"'),
            encoding: new Node\Scalar\String_('UTF-8')
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
