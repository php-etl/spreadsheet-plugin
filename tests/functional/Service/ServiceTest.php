<?php


namespace Test\Kiboko\Plugin\Spreadsheet\functional\Service;

use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Plugin\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    public function configProvider()
    {
        yield [
            'expected' => [
                'extractor' => [
                    'file_path' => 'path/to/file',
                    'excel' => [
                        'sheet' => 'Sheet1',
                        'skip_line' => 0
                    ]
                ],
                'logger' => [
                    'type' => 'stderr',
                    'destinations' => []
                ]
            ],
            'expected_class' => 'Kiboko\\Plugin\\Spreadsheet\\Factory\\Repository\\Extractor',
            'actual' => [
                'spreadsheet' => [
                    'extractor' => [
                        'file_path' => 'path/to/file',
                        'excel' => [
                            'sheet' => 'Sheet1'
                        ]
                    ],
                    'logger' => [
                        'type' => 'stderr'
                    ]
                ]
            ]
        ];

        yield [
            'expected' => [
                'loader' => [
                    'file_path' => 'output.xlsx',
                    'excel' => [
                        'sheet' => 'Sheet1'
                    ]
                ],
                'logger' => [
                    'type' => 'stderr',
                    'destinations' => []
                ]
            ],
            'expected_class' => 'Kiboko\\Plugin\\Spreadsheet\\Factory\\Repository\\Loader',
            'actual' => [
                'spreadsheet' => [
                    'loader' => [
                        'file_path' => 'output.xlsx',
                        'excel' => [
                            'sheet' => 'Sheet1'
                        ]
                    ],
                    'logger' => [
                        'type' => 'stderr'
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider configProvider
     */
    public function testWithConfigurationAndProcessor(array $expected, string $expectedClass, array $actual): void
    {
        $service = new Spreadsheet\Service();
        $normalizedConfig = $service->normalize($actual);

        $this->assertEquals(
            new Spreadsheet\Configuration(),
            $service->configuration()
        );

        $this->assertEquals(
            $expected,
            $normalizedConfig
        );

        $this->assertTrue($service->validate($actual));
        $this->assertFalse($service->validate(['logger' => []]));

        $this->assertInstanceOf(
            $expectedClass,
            $service->compile($normalizedConfig)
        );
    }

    public function testWithBothExtractAndLoad(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Your configuration should either contain the "extractor" or the "loader" key, not both.');

        $wrongConfig = [
            'spreadsheet' => [
                'extractor' => [
                    'file_path' => 'input.xlsx',
                    'excel' => [
                        'sheet' => 'Sheet1'
                    ]
                ],
                'loader' => [
                    'file_path' => 'output.xlsx',
                    'excel' => [
                        'sheet' => 'Sheet1'
                    ]
                ]
            ]
        ];

        $service = new Spreadsheet\Service();
        $service->normalize($wrongConfig);
    }

    public function testWrongConfiguration(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Could not determine if the factory should build an extractor or a loader.');

        $service = new Spreadsheet\Service();
        $service->compile([
            'spreadsheet' => []
        ]);
    }
}
