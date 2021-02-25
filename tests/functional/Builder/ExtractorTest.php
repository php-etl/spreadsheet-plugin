<?php

namespace Test\Kiboko\Plugin\Spreadsheet\functional\Builder;

use Kiboko\Plugin\Spreadsheet\Builder;
use Kiboko\Plugin\Log;

class ExtractorTest extends BuilderTestCase
{
    public function testWithFilePath(): void
    {
        $extract = new Builder\XLSX\Extractor(
            filePath: 'tests/functional/files/source-to-extract.xlsx',
            sheet: 'Sheet1',
            skipLine: 0
        );

        $this->assertBuilderProducesAnInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Extractor',
            $extract
        );

        $this->assertExtractorIteratesAs(
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont'],
            ],
            $extract
        );
    }

    public function testWithFilePathAndLogger(): void
    {
        $extract = new Builder\XLSX\Extractor(
            filePath: 'tests/functional/files/source-to-extract.xlsx',
            sheet: 'Sheet1',
            skipLine: 0
        );

        $extract->withLogger(
            (new Log\Builder\Logger())->getNode()
        );

        $this->assertBuilderHasLogger(
            '\\Psr\\Log\\NullLogger',
            $extract
        );

        $this->assertBuilderProducesAnInstanceOf(
            'Kiboko\\Component\\Flow\\Spreadsheet\\Sheet\\Safe\\Extractor',
            $extract
        );

        $this->assertExtractorIteratesAs(
            [
                ['first name' => 'john', 'last name' => 'doe'],
                ['first name' => 'jean', 'last name' => 'dupont']
            ],
            $extract
        );
    }
}
