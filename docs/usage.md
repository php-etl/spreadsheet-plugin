---
## With a symfony command

This is an example of code to load a configuration file, build the corresponding Extractor or Builder and show them in the console.

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Kiboko\Plugin\Spreadsheet;
use PhpParser\Node;
use PhpParser\PrettyPrinter;
use Symfony\Component\Console;
use Symfony\Component\Yaml;

$input = new Console\Input\ArgvInput($argv);
$output = new Console\Output\ConsoleOutput();

class DefaultCommand extends Console\Command\Command
{
    protected static $defaultName = 'test';

    protected function configure()
    {
        $this->addArgument('file', Console\Input\InputArgument::REQUIRED);
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $factory = new Spreadsheet\Service();

        $style = new Console\Style\SymfonyStyle(
            $input,
            $output,
        );

        $config = Yaml\Yaml::parse(input: file_get_contents($input->getArgument('file')));

        $style->section('Validation');
        $style->writeln($factory->validate($config) ? '<info>ok</info>' : '<error>failed</error>');
        $style->section('Normalized Config');
        $style->writeln(\json_encode($config = $factory->normalize($config), JSON_PRETTY_PRINT));
        $style->section('Generated code');
        $style->writeln((new PrettyPrinter\Standard())->prettyPrintFile([
            new Node\Stmt\Return_($factory->compile($config)->getBuilder()->getNode()),
        ]));

        return 0;
    }
}

(new Console\Application())
    ->add(new DefaultCommand())
    ->run($input, $output)
;
```
