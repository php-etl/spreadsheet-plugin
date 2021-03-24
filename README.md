# Spreadsheet Plugin
This package aims at integrating the Spreadsheet or the Opendocument reader and writer into the
[Pipeline](https://github.com/php-etl/pipeline) stack.

## Principles
The tools in this library will produce executable PHP sources, using an intermediate _Abstract Syntax Tree_ from
[nikic/php-parser](https://github.com/nikic/PHP-Parser). This intermediate format helps you combine
the code produced by this library with other packages from [Middleware](https://github.com/php-etl).

# Installation
```
composer require php-etl/spreadsheet-plugin
```

# Usage
Example of a config file. Reads `input.xlsx`, writes `output.xlsx`, logs error in system's [stderr](https://en.wikipedia.org/wiki/Standard_streams#Standard_error_(stderr)).
```yaml
csv:
    extractor:
      file_path: 'input.xlsx'
      excel:
        sheet: 'sheet2'
        skip_line: 1
#  loader:
#    file_path: 'output.xlsx'
#    excel:
#      sheet: 'sheet2'
    logger:
        type: stderr
```
