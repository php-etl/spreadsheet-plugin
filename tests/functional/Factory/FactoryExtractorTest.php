<?php declare(strict_types=1);


namespace functional\Kiboko\Plugin\Spreadsheet\Factory;

use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Plugin\Spreadsheet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class FactoryExtractorTest extends TestCase
{
    public function configProvider()
    {
        yield [
            'expected' => [
                'file_path' => 'input.xlsx',
                'excel' => [
                    'sheet' => 'Sheet1',
                    'skip_lines' => 0
                ]
            ],
            'actual' => [
                'extractor' => [
                    'file_path' => 'input.xlsx',
                    'excel' => [
                        'sheet' => 'Sheet1'
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider configProvider
     */
    public function testWithConfiguration(array $expected, array $actual): void
    {
        $factory = new Spreadsheet\Factory\Extractor(new ExpressionLanguage());
        $normalizedConfig = $factory->normalize($actual);

        $this->assertEquals(
            new Spreadsheet\Configuration\Extractor(),
            $factory->configuration()
        );

        $this->assertEquals(
            $expected,
            $normalizedConfig
        );

        $this->assertTrue($factory->validate($actual));
    }

    public function testFailToNormalize(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('The child config "file_path" under "extractor" must be configured.');

        $wrongConfig = [
            'extractor' => []
        ];

        $factory = new Spreadsheet\Factory\Extractor(new ExpressionLanguage());
        $factory->normalize($wrongConfig);
    }
}
