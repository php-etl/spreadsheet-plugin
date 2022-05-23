<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\Spreadsheet\Builder\CSV;

use functional\Kiboko\Plugin\Spreadsheet\PipelineRunner;
use Kiboko\Component\PHPUnitExtension\Assert\LoaderBuilderAssertTrait;
use Kiboko\Contract\Pipeline\PipelineRunnerInterface;
use Kiboko\Plugin\Spreadsheet\Builder;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;
use PhpParser\Node;

abstract class LoaderTest extends TestCase
{
    use LoaderBuilderAssertTrait;

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
        $load = new Builder\CSV\Loader(
            filePath: new Node\Scalar\String_('vfs://destination.csv'),
            delimiter: new Node\Scalar\String_(','),
            enclosure: new Node\Scalar\String_('"')
        );

        $this->assertBuildsLoaderLoadsLike(
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont'],
            ],
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont'],
            ],
            $load
        );
    }

    public function pipelineRunner(): PipelineRunnerInterface
    {
        return new PipelineRunner();
    }
}
