<?php declare(strict_types=1);


namespace functional\Kiboko\Plugin\Spreadsheet\Builder\CSV;

use Kiboko\Plugin\Spreadsheet\Builder;
use Kiboko\Component\PHPUnitExtension\BuilderAssertTrait;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;
use PhpParser\Node;

final class LoaderTest extends TestCase
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

    public function testWithFilePath(): void
    {
        $load = new Builder\CSV\Loader(
            filePath: new Node\Scalar\String_('vfs://destination.csv'),
            delimiter: new Node\Scalar\String_(','),
            enclosure: new Node\Scalar\String_('"')
        );

        $this->assertBuilderProducesPipelineLoadingLike(
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
}
