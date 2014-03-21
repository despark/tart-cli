<?php

namespace App\Command\Tart;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * tart:delete to delete a section from the admin based on a model
 *
 * @link https://github.com/openbuildings/jam-tart
 * @link http://symfony.com/doc/current/components/console/introduction.html
 * @author Haralan Dobrev <hkdobrev@gmail.com>
 */
class DeleteCommand extends AbstractTartCommand
{
    protected function configure()
    {
        $this
            ->setName('tart:delete')
            ->setDescription('Delete a section from the admin based on a model')
            ->addArgument(
                'model',
                InputArgument::REQUIRED,
                'Which model section to delete?'
            )
            ->addOption(
               'controller',
               'c',
               InputOption::VALUE_REQUIRED,
               'What is the name of the controller? Defaults to pluralized model'
            )
            ->addOption(
                'module',
                'm',
                InputOption::VALUE_REQUIRED,
                'Which module to use?',
                self::MODULE_DEFAULT
            )
            ->addOption(
                'dry-run',
                'd',
                InputOption::VALUE_NONE,
                'Do not delete, only show what would be deleted'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesToDelete = $this->getFilesToDelete(
            new NameResolver(
                $input->getArgument('model'),
                $input->getOption('controller')
            ),
            new DirectoryResolver($input->getOption('module'))
        );

        $style = new OutputFormatterStyle('yellow', null, array('bold'));
        $output->getFormatter()->setStyle('dry', $style);

        $dryRun = $input->getOption('dry-run');

        $output->writeln(sprintf(
            ($dryRun ? '<dry>[DRY-RUN]</dry> ' : '')
                .'<fg=red>%d files to be deleted:</fg=red>',
            count($filesToDelete)
        ));

        foreach ($filesToDelete as $filePath) {
            $humanizedFilePath = str_replace(MODPATH, 'MODPATH/', $filePath);
            $output->writeln(sprintf('<comment>%s</comment>', $humanizedFilePath));
            if ( !$dryRun) {
                unlink($filePath);
            }
        }
    }

    private function getFilesToDelete(NameResolver $nameResolver, DirectoryResolver $directoryResolver)
    {
        $controllerFilePath = $directoryResolver->getControllersDirectory()
            .$nameResolver->getControllerPath()
            .EXT;

        $filesToDelete = array();

        if (is_file($controllerFilePath)) {
            $filesToDelete []= $controllerFilePath;
        }

        foreach (static::$viewFiles as $viewFile) {
            $viewFilePath = $directoryResolver->getViewsDirectory()
                .$nameResolver->getControllerName()
                .DIRECTORY_SEPARATOR
                .$viewFile;

            if (is_file($viewFilePath)) {
                $filesToDelete []= $viewFilePath;
            }
        }

        return $filesToDelete;
    }
}
