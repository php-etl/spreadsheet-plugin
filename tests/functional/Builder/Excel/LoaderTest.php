<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\Spreadsheet\Builder\Excel;

use Kiboko\Component\PHPUnitExtension\BuilderAssertTrait;
use Kiboko\Plugin\Spreadsheet\Builder;
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
        $load = new Builder\Excel\Loader(
            filePath: new Node\Scalar\String_('vfs://destination.xlsx'),
            sheetName: new Node\Scalar\String_('Sheet1')
        );

        $this->assertBuilderProducesInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Loader',
            $load
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
