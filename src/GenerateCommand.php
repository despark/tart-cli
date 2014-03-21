<?php

namespace Despark\Command\Tart;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;

/**
 * tart:generate - Generate a new section in the admin based on a model.
 *
 * @link https://github.com/openbuildings/jam-tart
 * @link http://symfony.com/doc/current/components/console/introduction.html
 * @author Haralan Dobrev <hkdobrev@gmail.com>
 */
class GenerateCommand extends AbstractTartCommand
{
    const AUTHOR_DEFAULT = 'Ivan Kerin';

    // Templates

    const TEMPLATES_DIR = 'templates';

    protected function configure()
    {
        $this
            ->setName('tart:generate')
            ->setDescription('Generate a new jam-tart admin module based on a model')
            ->addArgument(
                'model',
                InputArgument::REQUIRED,
                'Which model to use?'
            )
            ->addOption(
               'controller',
               'c',
               InputOption::VALUE_REQUIRED,
               'What name to use for the Controller? Defaults to pluralized model'
            )
            ->addOption(
               'author',
               'a',
               InputOption::VALUE_REQUIRED,
               'Set the @author annotations',
               self::AUTHOR_DEFAULT
            )
            ->addOption(
               'module',
               'm',
               InputOption::VALUE_REQUIRED,
               'In which Kohana module to place the files?',
               self::MODULE_DEFAULT
            )
            ->addOption(
               'force',
               'f',
               InputOption::VALUE_NONE,
               'Should overwrite existing files?'
            )
            ->addOption(
                'dry-run',
                'd',
                InputOption::VALUE_NONE,
                'Do not delete, only show what would be deleted'
            )
            ->addOption(
               'unlink',
               'u',
               InputOption::VALUE_NONE,
               'Should delete existing files?'
            )
            ->addOption(
               'batch-modify',
               null,
               InputOption::VALUE_NONE,
               'Include batch modify code?'
            )
            ->addOption(
               'batch-delete',
               null,
               InputOption::VALUE_NONE,
               'Include batch delete code?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nameResolver = new NameResolver(
            $input->getArgument('model'),
            $input->getOption('controller')
        );

        $directoryResolver = new DirectoryResolver($input->getOption('module'));

        $data = $this->getInitialData($nameResolver, $directoryResolver);
        $data = $this->preProcessData(
            $data,
            $input->getOption('batch-delete'),
            $input->getOption('batch-modify')
        );

        $data['{{author}}'] = $input->getOption('author');

        if ($input->getOption('controller')) {
            $data['{{controllerOption}}'] = sprintf(
                "                ->controller('%s')",
                $nameResolver->getControllerName()
            );

            $data['{{extraController}}'] = sprintf(
                ", array('controller' => '%s')",
                $nameResolver->getControllerName()
            );

            $data['{{extraControllerDelete}}'] = sprintf(
                "array('controller' => '%s', 'action' => 'delete')",
                $nameResolver->getControllerName()
            );
        }

        $dryRun = $input->getOption('dry-run');

        $files = $this->getFiles($nameResolver, $directoryResolver);

        $viewsDir = $directoryResolver->getViewsDirectory()
            .$nameResolver->getControllerName();

        if ( !$dryRun and !file_exists($viewsDir)) {
            mkdir($viewsDir, 0777, true);
        }

        $output->writeln(sprintf(
            ($dryRun ? '<dry>[DRY-RUN]</dry> ' : '')
                .'<info>%d files to be created:</info>',
            count($files)
        ));

        foreach ($files as $filePath => $templateFile) {
            $humanizedFilePath = str_replace(MODPATH, 'MODPATH/', $filePath);
            $output->writeln($humanizedFilePath);
            file_put_contents($filePath, $this->parse($templateFile, $data));
        }
    }

    public function getInitialData(
        NameResolver $nameResolver,
        DirectoryResolver $directoryResolver
    ) {
        return array(
            '{{controller}}' => $nameResolver->getControllerName(),
            '{{nameKey}}' => $nameResolver->getNameKey(),
            '{{model}}' => $nameResolver->getModelName(),
            '{{moduleName}}' => $directoryResolver->getModule(),
            '{{modelTitle}}' => $nameResolver->getModelTitle(),
            '{{viewsPath}}' => $directoryResolver->getViewsPath()
                .DIRECTORY_SEPARATOR
                .$nameResolver->getControllerName(),
            '{{pluralName}}' => $nameResolver->getPluralName(),
            '{{controllerTitle}}' => $nameResolver->getControllerTitle(),
            '{{controllerClass}}' => $nameResolver->getControllerClass(),
            '{{batchIndex}}' => '',
            '{{batchDelete}}' => '',
            '{{batchModify}}' => '',
            '{{controllerOption}}' => '',
            '{{extraController}}' => '',
            '{{extraControllerDelete}}' => "'delete'",
        );
    }

    public function preProcessData(
        array $data,
        $batchModify = false,
        $batchDelete = false
    ) {
        $actions = '';

        if ($batchDelete) {
            $actions .= "\n                'delete' => 'Delete',";
            $data['{{batchDelete}}'] = $this->parse(
                static::TEMPLATE_BATCH_DELETE,
                $data
            );
        }

        if ($batchModify) {
            $actions .= "\n                'modify' => 'Modify',";

            $data['{{batchModify}}'] = $this->parse(
                static::TEMPLATE_BATCH_MODIFY,
                $data
            );
        }

        if ($batchDelete or $batchModify) {
            $data['{{batchIndex}}'] = $this->parse(
                static::TEMPLATE_BATCH_INDEX,
                $data + array('{{actions}}' => $actions)
            );
        }

        return $data;
    }

    private function parse($templateFile, $data)
    {
        $templateFilePath = __DIR__
            .DIRECTORY_SEPARATOR
            .static::TEMPLATES_DIR
            .DIRECTORY_SEPARATOR
            .$templateFile;

        if ( !is_file($templateFilePath)) {
            throw new InvalidArgumentException(sprintf(
                'There is no template "%s"',
                $templateFile
            ));
        }

        $templateString = file_get_contents($templateFilePath);

        return strtr($templateString, $data);
    }

    public function getFiles(
        NameResolver $nameResolver,
        DirectoryResolver $directoryResolver
    ) {
        $files = array();

        foreach (static::$viewFiles as $templateName => $viewFile) {
            $filePath = $directoryResolver->getViewsDirectory()
                .$nameResolver->getControllerName()
                .DIRECTORY_SEPARATOR
                .$viewFile;
            $files[$filePath] = $templateName;
        }

        $controllerFilePath = $directoryResolver->getControllersDirectory()
            .$nameResolver->getControllerPath()
            .EXT;

        $files[$controllerFilePath] = static::TEMPLATE_CONTROLLER;

        return $files;
    }
}
