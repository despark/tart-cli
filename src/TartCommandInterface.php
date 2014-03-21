<?php

namespace Despark\Command\Tart;

interface TartCommandInterface
{
    const MODULE_DEFAULT = 'admin';

    const FILE_VIEW_BATCH_DELETE = 'batch_delete.php';
    const FILE_VIEW_BATCH_MODIFY = 'batch_modify.php';
    const FILE_VIEW_EDIT         = 'edit.php';
    const FILE_VIEW_FORM         = 'form.php';
    const FILE_VIEW_INDEX        = 'index.php';
    const FILE_VIEW_NEW          = 'new.php';
    const FILE_VIEW_SIDEBAR      = 'sidebar.php';

    const TEMPLATE_BATCH_DELETE      = 'batch_delete.txt';
    const TEMPLATE_BATCH_MODIFY      = 'batch_modify.txt';
    const TEMPLATE_BATCH_INDEX       = 'batch_index.txt';
    const TEMPLATE_CONTROLLER        = 'controller.txt';
    const TEMPLATE_VIEW_BATCH_DELETE = 'view_batch_delete.txt';
    const TEMPLATE_VIEW_BATCH_MODIFY = 'view_batch_modify.txt';
    const TEMPLATE_VIEW_EDIT         = 'view_edit.txt';
    const TEMPLATE_VIEW_FORM         = 'view_form.txt';
    const TEMPLATE_VIEW_INDEX        = 'view_index.txt';
    const TEMPLATE_VIEW_NEW          = 'view_new.txt';
    const TEMPLATE_VIEW_SIDEBAR      = 'view_sidebar.txt';
}
