<?php

namespace App\Command\Tart;

use Kohana;
use InvalidArgumentException;

class DirectoryResolver
{
    const CLASSES_DIR = 'classes';

    const CONTROLLERS_DIR = 'Controller';

    const VIEWS_DIR = 'views';

    const TART_CONTROLLERS_DIR = 'Tart';

    const TART_VIEWS_DIR = 'tart';

    protected $module;

    protected $moduleDirectory;

    public function __construct($module = NULL)
    {
        if ($module) {
            $this->setModule($module);
        }
    }

    public function setModule($module)
    {
        if ( !$this->validateModule($module)) {
            throw new InvalidArgumentException(sprintf(
                'There is no Kohana module "%s" or it is not enabled',
                $module
            ));
        }

        $this->module = $module;
        $modules = Kohana::modules();
        $this->setModuleDirectory($modules[$this->module]);

        return $this;
    }

    /**
     * Check if a module exists.
     *
     * @param  string $module module name
     * @return boolean true if it exists; false otherwise
     */
    public function validateModule($module)
    {
        return in_array($module, array_keys(Kohana::modules()));
    }

    public function setModuleDirectory($moduleDirectory)
    {
        if ( !is_dir($moduleDirectory)) {
            throw new InvalidArgumentException(sprintf(
                'MODPATH/%s does not exist or is not a directory',
                $moduleDirectory
            ));
        }

        $this->moduleDirectory = $moduleDirectory;

        return $this;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getModuleDirectory()
    {
        return $this->moduleDirectory;
    }

    public function getViewsPath()
    {
        return static::TART_VIEWS_DIR;
    }

    public function getControllersPath()
    {
        return static::CLASSES_DIR
            .DIRECTORY_SEPARATOR
            .static::CONTROLLERS_DIR
            .DIRECTORY_SEPARATOR
            .static::TART_CONTROLLERS_DIR;
    }

    public function getControllersDirectory()
    {
        return $this->getModuleDirectory()
            .$this->getControllersPath()
            .DIRECTORY_SEPARATOR;
    }

    public function getViewsDirectory()
    {
        return $this->getModuleDirectory()
            .static::VIEWS_DIR
            .DIRECTORY_SEPARATOR
            .$this->getViewsPath()
            .DIRECTORY_SEPARATOR;
    }
}
