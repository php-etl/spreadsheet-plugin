<?php

declare(strict_types=1);
namespace functional\Kiboko\Plugin\Spreadsheet\Builder\CSV;

abstract class ExtractorTestCase extends \PHPUnit\Framework\TestCase
{
    use \Kiboko\Component\PHPUnitExtension\Assert\ExtractorBuilderAssertTrait;
    private ?\Vfs\FileSystem $fs = null;
    protected function setUp(): void
    {
        $this->fs = \Vfs\FileSystem::factory('vfs://');
        $this->fs->mount();
    }
    protected function tearDown(): void
    {
        $this->fs->unmount();
        $this->fs = null;
    }
    public function testWithoutOption(): void
    {
        $extractor = new \Kiboko\Plugin\Spreadsheet\Builder\CSV\Extractor(filePath: new \PhpParser\Node\Scalar\String_(__DIR__ . '/../../files/source-to-extract.csv'), skipLines: new \PhpParser\Node\Scalar\LNumber(0), delimiter: new \PhpParser\Node\Scalar\String_(','), enclosure: new \PhpParser\Node\Scalar\String_('"'), encoding: new \PhpParser\Node\Scalar\String_('UTF-8'));
        $this->assertBuildsExtractorExtractsLike([['first name' => 'john', 'last name' => 'doe'], ['first name' => 'jean', 'last name' => 'dupont']], $extractor);
    }
    public function pipelineRunner(): \Kiboko\Contract\Pipeline\PipelineRunnerInterface
    {
        return new \functional\Kiboko\Plugin\Spreadsheet\PipelineRunner();
    }
}
