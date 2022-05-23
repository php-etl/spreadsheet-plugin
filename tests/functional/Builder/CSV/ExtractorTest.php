<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\Spreadsheet\Builder\CSV;

use functional\Kiboko\Plugin\Spreadsheet\PipelineRunner;
use Kiboko\Component\PHPUnitExtension\Assert\ExtractorBuilderAssertTrait;
use Kiboko\Contract\Pipeline\PipelineRunnerInterface;
use Kiboko\Plugin\Spreadsheet\Builder;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;
use PhpParser\Node;

abstract class ExtractorTest extends TestCase
{
    use ExtractorBuilderAssertTrait;

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

        $this->assertBuildsExtractorExtractsLike(
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont']
            ],
            $extractor
        );
    }

    public function pipelineRunner(): PipelineRunnerInterface
    {
        return new PipelineRunner();
    }
}
