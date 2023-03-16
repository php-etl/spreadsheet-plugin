<?php

declare(strict_types=1);
namespace functional\Kiboko\Plugin\Spreadsheet\Builder\OpenDocument;

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
        file_put_contents('vfs://expected-1.ods', <<<ODS
firstname,lastname
Pierre,Dupont
John,Doe
Frank,O'hara

ODS
);
        file_put_contents('vfs://expected-2.ods', <<<ODS
firstname,lastname
Hiroko,Froncillo
Marlon,Botz
Billy,Hess

ODS
);
        $loader = new \Kiboko\Plugin\Spreadsheet\Builder\OpenDocument\MultipleFileLoader(filePath: \Kiboko\Component\SatelliteToolbox\Configuration\compileExpression(new \functional\Kiboko\Plugin\Spreadsheet\ExpressionLanguage\ExpressionLanguage(), new \Symfony\Component\ExpressionLanguage\Expression('format("vfs://SKU_%06d.ods", index)'), 'index'), sheetName: new \PhpParser\Node\Scalar\String_('MySheetName'), maxLines: new \PhpParser\Node\Scalar\LNumber(3));
        $this->assertBuildsLoaderLoadsLike([['firstname' => 'Pierre', 'lastname' => 'Dupont'], ['firstname' => 'John', 'lastname' => 'Doe'], ['firstname' => 'Frank', 'lastname' => 'O\'hara'], ['firstname' => 'Hiroko', 'lastname' => 'Froncillo'], ['firstname' => 'Marlon', 'lastname' => 'Botz'], ['firstname' => 'Billy', 'lastname' => 'Hess']], [['firstname' => 'Pierre', 'lastname' => 'Dupont'], ['firstname' => 'John', 'lastname' => 'Doe'], ['firstname' => 'Frank', 'lastname' => 'O\'hara'], ['firstname' => 'Hiroko', 'lastname' => 'Froncillo'], ['firstname' => 'Marlon', 'lastname' => 'Botz'], ['firstname' => 'Billy', 'lastname' => 'Hess']], $loader);
        //        $this->assertFileEquals('vfs://expected-1.ods', file_get_contents('vfs://SKU_000000.ods'));
        //        $this->assertFileEquals('vfs://expected-2.ods', 'vfs://SKU_000001.ods');
    }
    public function pipelineRunner(): \Kiboko\Contract\Pipeline\PipelineRunnerInterface
    {
        return new \functional\Kiboko\Plugin\Spreadsheet\PipelineRunner();
    }
}
