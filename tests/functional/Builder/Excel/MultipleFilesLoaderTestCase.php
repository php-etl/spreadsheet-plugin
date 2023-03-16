<?php

declare(strict_types=1);
namespace functional\Kiboko\Plugin\Spreadsheet\Builder\Excel;

abstract class MultipleFilesLoaderTestCase extends \PHPUnit\Framework\TestCase
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
        $loader = new \Kiboko\Plugin\Spreadsheet\Builder\OpenDocument\MultipleFileLoader(filePath: \Kiboko\Component\SatelliteToolbox\Configuration\compileExpression(new \functional\Kiboko\Plugin\Spreadsheet\ExpressionLanguage\ExpressionLanguage(), new \Symfony\Component\ExpressionLanguage\Expression('format("vfs://SKU_%06d.xlsx", index)'), 'index'), sheetName: new \PhpParser\Node\Scalar\String_('MySheetName'), maxLines: new \PhpParser\Node\Scalar\LNumber(3));
        $this->assertBuildsLoaderLoadsLike([['firstname' => 'Pierre', 'lastname' => 'Dupont'], ['firstname' => 'John', 'lastname' => 'Doe'], ['firstname' => 'Frank', 'lastname' => 'O\'hara'], ['firstname' => 'Hiroko', 'lastname' => 'Froncillo'], ['firstname' => 'Marlon', 'lastname' => 'Botz'], ['firstname' => 'Billy', 'lastname' => 'Hess']], [['firstname' => 'Pierre', 'lastname' => 'Dupont'], ['firstname' => 'John', 'lastname' => 'Doe'], ['firstname' => 'Frank', 'lastname' => 'O\'hara'], ['firstname' => 'Hiroko', 'lastname' => 'Froncillo'], ['firstname' => 'Marlon', 'lastname' => 'Botz'], ['firstname' => 'Billy', 'lastname' => 'Hess']], $loader);
    }
    public function pipelineRunner(): \Kiboko\Contract\Pipeline\PipelineRunnerInterface
    {
        return new \functional\Kiboko\Plugin\Spreadsheet\PipelineRunner();
    }
}
