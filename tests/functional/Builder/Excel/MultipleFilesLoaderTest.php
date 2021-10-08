<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\Spreadsheet\Builder\Excel;

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

final class MultipleFilesLoaderTest extends TestCase
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
        $loader = new Builder\OpenDocument\MultipleFileLoader(
            filePath: compileExpression(new ExpressionLanguage(), new Expression('format("vfs://SKU_%06d.xlsx", index)'), 'index'),
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
    }

    public function pipelineRunner(): PipelineRunnerInterface
    {
        return new PipelineRunner();
    }
}
