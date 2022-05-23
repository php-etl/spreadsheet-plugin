<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\Spreadsheet\Builder\OpenDocument;

use functional\Kiboko\Plugin\Spreadsheet\ExpressionLanguage\ExpressionLanguage;
use functional\Kiboko\Plugin\Spreadsheet\PipelineRunner;
use Kiboko\Component\PHPUnitExtension\Assert\LoaderBuilderAssertTrait;
use Kiboko\Contract\Pipeline\PipelineRunnerInterface;
use Kiboko\Plugin\Spreadsheet\Builder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\Expression;
use Vfs\FileSystem;
use PhpParser\Node;
use function Kiboko\Component\SatelliteToolbox\Configuration\compileExpression;

abstract class MultipleFilesLoaderTest extends TestCase
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
        file_put_contents('vfs://expected-1.ods', <<<ODS
            firstname,lastname
            Pierre,Dupont
            John,Doe
            Frank,O'hara
            
            ODS);

        file_put_contents('vfs://expected-2.ods', <<<ODS
            firstname,lastname
            Hiroko,Froncillo
            Marlon,Botz
            Billy,Hess
            
            ODS);

        $loader = new Builder\OpenDocument\MultipleFileLoader(
            filePath: compileExpression(new ExpressionLanguage(), new Expression('format("vfs://SKU_%06d.ods", index)'), 'index'),
            sheetName: new Node\Scalar\String_('MySheetName'),
            maxLines: new Node\Scalar\LNumber(3)
        );

        $this->assertBuildsLoaderLoadsLike(
            [
                [
                    'firstname' => 'Pierre',
                    'lastname' => 'Dupont',
                ],
                [
                    'firstname' => 'John',
                    'lastname' => 'Doe',
                ],
                [
                    'firstname' => 'Frank',
                    'lastname' => 'O\'hara',
                ],
                [
                    'firstname' => 'Hiroko',
                    'lastname' => 'Froncillo',
                ],
                [
                    'firstname' => 'Marlon',
                    'lastname' => 'Botz',
                ],
                [
                    'firstname' => 'Billy',
                    'lastname' => 'Hess',
                ],
            ],
            [
                [
                    'firstname' => 'Pierre',
                    'lastname' => 'Dupont',
                ],
                [
                    'firstname' => 'John',
                    'lastname' => 'Doe',
                ],
                [
                    'firstname' => 'Frank',
                    'lastname' => 'O\'hara',
                ],
                [
                    'firstname' => 'Hiroko',
                    'lastname' => 'Froncillo',
                ],
                [
                    'firstname' => 'Marlon',
                    'lastname' => 'Botz',
                ],
                [
                    'firstname' => 'Billy',
                    'lastname' => 'Hess',
                ],
            ],
            $loader,
        );

//        $this->assertFileEquals('vfs://expected-1.ods', file_get_contents('vfs://SKU_000000.ods'));
//        $this->assertFileEquals('vfs://expected-2.ods', 'vfs://SKU_000001.ods');
    }

    public function pipelineRunner(): PipelineRunnerInterface
    {
        return new PipelineRunner();
    }
}
