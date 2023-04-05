<?php

declare(strict_types=1);
namespace functional\Kiboko\Plugin\Spreadsheet\Builder\OpenDocument;

abstract class ExtractorTestCase extends \PHPUnit\Framework\TestCase
{
    use \Kiboko\Component\PHPUnitExtension\Assert\ExtractorBuilderAssertTrait;
    use \Kiboko\Component\PHPUnitExtension\Assert\PipelineBuilderAssertTrait;
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
    public function testWithFilePath(): void
    {
        $extractor = new \Kiboko\Plugin\Spreadsheet\Builder\OpenDocument\Extractor(filePath: new \PhpParser\Node\Scalar\String_(__DIR__ . '/../../files/source-to-extract.ods'), sheetName: new \PhpParser\Node\Scalar\String_('Sheet1'), skipLines: new \PhpParser\Node\Scalar\LNumber(0));
        $this->assertBuilderProducesInstanceOf(\Kiboko\Component\Flow\Spreadsheet\Sheet\Safe\Extractor::class, $extractor);
        $this->assertBuildsExtractorExtractsLike([['first name' => 'john', 'last name' => 'doe'], ['first name' => 'jean', 'last name' => 'dupont']], $extractor);
    }
    public function pipelineRunner(): \Kiboko\Contract\Pipeline\PipelineRunnerInterface
    {
        return new \functional\Kiboko\Plugin\Spreadsheet\PipelineRunner();
    }
}
