<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\Spreadsheet\Configuration;

use Kiboko\Plugin\Spreadsheet\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config;

final class ConfigurationTest extends TestCase
{
    private ?Config\Definition\Processor $processor = null;

    protected function setUp(): void
    {
        $this->processor = new Config\Definition\Processor();
    }

    public function validConfigProvider()
    {
        /* Minimal config */
        yield [
            'expected' => [
                'expression_language' => [],
                'extractor' => [
                    'file_path' => 'path/to/file',
                    'excel' => [
                        'sheet' => 'Sheet1',
                        'skip_lines' => 0
                    ]
                ]
            ],
            'actual' => [
                'expression_language' => [],
                'extractor' => [
                    'file_path' => 'path/to/file',
                    'excel' => [
                        'sheet' => 'Sheet1'
                    ]
                ]
            ]
        ];

        /* With skipline option */
        yield [
            'expected' => [
                'expression_language' => [],
                'extractor' => [
                    'file_path' => 'path/to/file',
                    'excel' => [
                        'sheet' => 'Sheet1',
                        'skip_lines' => 2
                    ]
                ]
            ],
            'actual' => [
                'expression_language' => [],
                'extractor' => [
                    'file_path' => 'path/to/file',
                    'excel' => [
                        'sheet' => 'Sheet1',
                         'skip_lines' => 2
                    ]
                ]
            ]
        ];

        /* With logger */
        yield [
            'expected' => [
                'logger' => [
                    'type' => 'null',
                ],
                'expression_language' => [],
                'extractor' => [
                    'file_path' => 'path/to/file',
                    'excel' => [
                        'sheet' => 'Sheet1',
                        'skip_lines' => 0
                    ]
                ]
            ],
            'actual' => [
                'logger' => [
                    'type' => 'null'
                ],
                'expression_language' => [],
                'extractor' => [
                    'file_path' => 'path/to/file',
                    'excel' => [
                        'sheet' => 'Sheet1'
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider validConfigProvider
     */
    public function testValidConfig($expected, $actual)
    {
        $config = new Configuration();

        $this->assertEquals(
            $expected,
            $this->processor->processConfiguration(
                $config,
                [
                    $actual
                ]
            )
        );
    }

    public function testMissingFilePath()
    {
        $this->expectException(Config\Definition\Exception\InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child config "file_path" under "spreadsheet.extractor" must be configured.');

        $config = new Configuration();
        $this->processor->processConfiguration(
            $config,
            [
                [
                    'extractor' => [
                        'excel' => [
                            'sheet' => 'Sheet1',
                            'skip_lines' => 0
                        ]
                    ]
                ]
            ]
        );
    }

    public function testMissingSheetName()
    {
        $this->expectException(Config\Definition\Exception\InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child config "sheet" under "spreadsheet.extractor.excel" must be configured.');

        $config = new Configuration();
        $this->processor->processConfiguration(
            $config,
            [
                [
                    'extractor' => [
                        'file_path' => 'path/to/file',
                        'excel' => [
                        ]
                    ]
                ]
            ]
        );
    }
}
