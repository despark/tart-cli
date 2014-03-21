<?php

namespace Despark\Command\Tart;

use InvalidArgumentException;
use Doctrine\Common\Inflector\Inflector;
use Jam;

/**
 * Get names based on model and controller name.
 *
 * @author Haralan Dobrev <hkdobrev@gmai.com>
 */
class NameResolver
{
    public static function resolveControllerName($modelName)
    {
        return Inflector::pluralize($modelName);
    }

    public static function humanize($string)
    {
        return preg_replace('/[_-]+/', ' ', trim($string));
    }

    /**
     * @var string
     */
    protected $modelName;

    /**
     * @var Jam_Meta
     */
    protected $meta;

    /**
     * @var string
     */
    protected $controllerName;

    public function __construct($modelName, $controllerName = NULL)
    {
        $this->modelName = $modelName;

        $this->meta = Jam::meta($this->modelName);

        if ( !$controllerName) {
            $controllerName = static::resolveControllerName($modelName);
        }

        $this->controllerName = $controllerName;
    }

    public function getModelName()
    {
        return $this->modelName;
    }

    public function getMeta()
    {
        if ( !$this->meta) {
            throw new InvalidArgumentException(sprintf(
                'There is no Jam model "%s"',
                $this->modelName
            ));
        }

        return $this->meta;
    }

    public function getNameKey()
    {
        return $this->getMeta()->name_key();
    }

    public function getControllerName()
    {
        return $this->controllerName;
    }

    public function getModelTitle()
    {
        return ucwords(static::humanize($this->modelName));
    }

    public function getPluralName()
    {
        return str_replace('_', ' ', $this->controllerName);
    }

    public function getControllerTitle()
    {
        return str_replace(
            ' ',
            '_',
            ucwords(str_replace('_', ' ', $this->getPluralName()))
        );
    }

    public function getControllerPath()
    {
        return str_replace(
            '_',
            DIRECTORY_SEPARATOR,
            $this->getControllerTitle()
        );
    }

    public function getControllerClass()
    {
        return 'Controller_'
            .DirectoryResolver::TART_CONTROLLERS_DIR
            .'_'
            .str_replace(' ', '_', $this->getControllerTitle());
    }
}
