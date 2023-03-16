<?php

declare(strict_types=1);
namespace functional\Kiboko\Plugin\Spreadsheet\Builder\OpenDocument;

abstract class LoaderTestCase extends \PHPUnit\Framework\TestCase
{
    use \Kiboko\Component\PHPUnitExtension\Assert\LoaderBuilderAssertTrait;
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
    public function testWritingFile(): void
    {
        $load = new \Kiboko\Plugin\Spreadsheet\Builder\OpenDocument\Loader(filePath: new \PhpParser\Node\Scalar\String_('vfs://destination.ods'), sheetName: new \PhpParser\Node\Scalar\String_('Sheet1'));
        $this->assertBuildsLoaderProducesFile(__DIR__ . '/../../files/expected-to-load.ods', [['first name' => 'john', 'last name' => 'doe'], ['first name' => 'jean', 'last name' => 'dupont']], $load);
    }
    public function testIfLoaderProducedIsRight(): void
    {
        $load = new \Kiboko\Plugin\Spreadsheet\Builder\OpenDocument\Loader(filePath: new \PhpParser\Node\Scalar\String_('vfs://destination.ods'), sheetName: new \PhpParser\Node\Scalar\String_('Sheet1'));
        $this->assertBuilderProducesInstanceOf(\Kiboko\Component\Flow\Spreadsheet\Sheet\Safe\Loader::class, $load);
    }
    public function testIfLoaderIsNotAnExtractor(): void
    {
        $load = new \Kiboko\Plugin\Spreadsheet\Builder\OpenDocument\Loader(filePath: new \PhpParser\Node\Scalar\String_('vfs://destination.ods'), sheetName: new \PhpParser\Node\Scalar\String_('Sheet1'));
        $this->assertBuilderProducesNotInstanceOf(\Kiboko\Component\Flow\Spreadsheet\Sheet\Safe\Extractor::class, $load);
    }
    public function testWithoutOption(): void
    {
        $load = new \Kiboko\Plugin\Spreadsheet\Builder\OpenDocument\Loader(filePath: new \PhpParser\Node\Scalar\String_('vfs://destination.ods'), sheetName: new \PhpParser\Node\Scalar\String_('Sheet1'));
        $this->assertBuildsLoaderLoadsLike([['first name' => 'john', 'last name' => 'doe'], ['first name' => 'jean', 'last name' => 'dupont']], [['first name' => 'john', 'last name' => 'doe'], ['first name' => 'jean', 'last name' => 'dupont']], $load);
    }
    public function testWithSheet(): void
    {
        $load = new \Kiboko\Plugin\Spreadsheet\Builder\OpenDocument\Loader(filePath: new \PhpParser\Node\Scalar\String_('vfs://destination.ods'), sheetName: new \PhpParser\Node\Scalar\String_('Sheet1'));
        $load->withSheet(new \PhpParser\Node\Scalar\String_('Sheet1'));
        $this->assertBuildsLoaderLoadsLike([['first name' => 'john', 'last name' => 'doe'], ['first name' => 'jean', 'last name' => 'dupont']], [['first name' => 'john', 'last name' => 'doe'], ['first name' => 'jean', 'last name' => 'dupont']], $load);
    }
    public function pipelineRunner(): \Kiboko\Contract\Pipeline\PipelineRunnerInterface
    {
        return new \functional\Kiboko\Plugin\Spreadsheet\PipelineRunner();
    }
}
