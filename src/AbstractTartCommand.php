<?php

namespace Despark\Command\Tart;

use Symfony\Component\Console\Command\Command;

abstract class AbstractTartCommand extends Command implements TartCommandInterface
{
    protected static $viewFiles = array(
        self::TEMPLATE_VIEW_BATCH_DELETE  => self::FILE_VIEW_BATCH_DELETE,
        self::TEMPLATE_VIEW_BATCH_MODIFY  => self::FILE_VIEW_BATCH_MODIFY,
        self::TEMPLATE_VIEW_EDIT    => self::FILE_VIEW_EDIT,
        self::TEMPLATE_VIEW_FORM    => self::FILE_VIEW_FORM,
        self::TEMPLATE_VIEW_INDEX   => self::FILE_VIEW_INDEX,
        self::TEMPLATE_VIEW_NEW     => self::FILE_VIEW_NEW,
        self::TEMPLATE_VIEW_SIDEBAR => self::FILE_VIEW_SIDEBAR,
    );
}
