<?php

declare(strict_types=1);
namespace functional\Kiboko\Plugin\Spreadsheet\Builder\CSV;

abstract class LoaderTestCase extends \PHPUnit\Framework\TestCase
{
    use \Kiboko\Component\PHPUnitExtension\Assert\LoaderBuilderAssertTrait;
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
        $load = new \Kiboko\Plugin\Spreadsheet\Builder\CSV\Loader(filePath: new \PhpParser\Node\Scalar\String_('vfs://destination.csv'), delimiter: new \PhpParser\Node\Scalar\String_(','), enclosure: new \PhpParser\Node\Scalar\String_('"'));
        $this->assertBuildsLoaderLoadsLike([['first name' => 'john', 'last name' => 'doe'], ['first name' => 'jean', 'last name' => 'dupont']], [['first name' => 'john', 'last name' => 'doe'], ['first name' => 'jean', 'last name' => 'dupont']], $load);
    }
    public function pipelineRunner(): \Kiboko\Contract\Pipeline\PipelineRunnerInterface
    {
        return new \functional\Kiboko\Plugin\Spreadsheet\PipelineRunner();
    }
}
