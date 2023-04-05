<?php

declare(strict_types=1);
namespace functional\Kiboko\Plugin\Spreadsheet\Builder\Excel;

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
    public function testWithFilePath(): void
    {
        $load = new \Kiboko\Plugin\Spreadsheet\Builder\Excel\Loader(filePath: new \PhpParser\Node\Scalar\String_('vfs://destination.xlsx'), sheetName: new \PhpParser\Node\Scalar\String_('Sheet1'));
        $this->assertBuilderProducesInstanceOf(\Kiboko\Component\Flow\Spreadsheet\Sheet\Safe\Loader::class, $load);
        $this->assertBuildsLoaderLoadsLike([['first name' => 'john', 'last name' => 'doe'], ['first name' => 'jean', 'last name' => 'dupont']], [['first name' => 'john', 'last name' => 'doe'], ['first name' => 'jean', 'last name' => 'dupont']], $load);
    }
    public function pipelineRunner(): \Kiboko\Contract\Pipeline\PipelineRunnerInterface
    {
        return new \functional\Kiboko\Plugin\Spreadsheet\PipelineRunner();
    }
}
