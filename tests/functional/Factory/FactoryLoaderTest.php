<?php declare(strict_types=1);


namespace functional\Kiboko\Plugin\Spreadsheet\Factory;

use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Plugin\Spreadsheet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class FactoryLoaderTest extends TestCase
{
    public static function configProvider()
    {
        yield [
            'expected' => [
                'file_path' => 'input.xlsx',
                'excel' => [
                    'sheet' => 'Sheet1'
                ]
            ],
            'actual' => [
                'loader' => [
                    'file_path' => 'input.xlsx',
                    'excel' => [
                        'sheet' => 'Sheet1'
                    ]
                ]
            ]
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('configProvider')]
    public function testWithConfiguration(array $expected, array $actual): void
    {
        $factory = new Spreadsheet\Factory\Loader(new ExpressionLanguage());
        $normalizedConfig = $factory->normalize($actual);

        $this->assertEquals(
            new Spreadsheet\Configuration\Loader(),
            $factory->configuration()
        );

        $this->assertEquals(
            $expected,
            $normalizedConfig
        );

        $this->assertTrue(
            $factory->validate($actual)
        );
    }

    public function testFailToNormalize(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('The child config "file_path" under "loader" must be configured.');

        $wrongConfig = [
            'loader' => []
        ];

        $factory = new Spreadsheet\Factory\Loader(new ExpressionLanguage());
        $factory->normalize($wrongConfig);
    }
}
