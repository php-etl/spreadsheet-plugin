<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\Spreadsheet\Builder\OpenDocument;

use functional\Kiboko\Plugin\Spreadsheet\ExpressionLanguage\ExpressionLanguage;
use Kiboko\Component\PHPUnitExtension\BuilderAssertTrait;
use Kiboko\Plugin\Spreadsheet\Builder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\Expression;
use Vfs\FileSystem;
use PhpParser\Node;
use function Kiboko\Component\SatelliteToolbox\Configuration\compileExpression;

final class MultipleFilesLoaderTest extends TestCase
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
        $loader = new Builder\OpenDocument\MultipleFileLoader(
            filePath: compileExpression(new ExpressionLanguage(), new Expression('format("tests/functional/SKU_%06d.ods", index)'), 'index'),
            sheetName: new Node\Scalar\String_('MySheetName'),
            maxLines: new Node\Scalar\LNumber(3)
        );

        $this->assertBuilderProducesPipelineLoadingLike(
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

        $this->assertFileEquals('vfs://expected-1.ods', 'vfs://SKU_000000.ods');
        $this->assertFileEquals('vfs://expected-2.ods', 'vfs://SKU_000001.ods');
    }
}
