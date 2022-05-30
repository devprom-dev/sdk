<?php

include_once SERVER_ROOT_PATH.'admin/install/Installable.php';
include_once SERVER_ROOT_PATH.'admin/install/ClearCache.php';
include "SystemTemplateIterator.php";
include "SystemTemplateRegistry.php";

class SystemTemplate extends MetaobjectCacheable
{
    const TEMPLATES_PATH = 'conf/templates/';

    public function __construct()
    {
        parent::__construct('cms_Backup', new SystemTemplateRegistry($this));

        $this->addAttribute('Status', 'VARCHAR', text(2038), true, false);
        $this->addAttribute('Content', 'WYSIWYG', translate('Содержание'), false, false);
        $this->addAttribute('Format', 'VARCHAR', translate('Формат'), false, true);
        $this->setAttributeRequired('OrderNum', false);

        foreach( array('Format') as $attribute ) {
            $this->addAttributeGroup($attribute, 'system');
        }
    }

    function createIterator()
    {
        return new SystemTemplateIterator($this);
    }

    public function getPage()
    {
        return '/admin/systemtemplates/?';
    }

    function getDisplayName()
    {
        return translate('Текст');
    }

    static public function getPath()
    {
        return DOCUMENT_ROOT.self::TEMPLATES_PATH;
    }

    function modify_parms( $id, $parms )
    {
        $it = $this->getExact($id);

        if ( !is_dir(dirname($it->getFilePath())) ) {
            mkdir(dirname($it->getFilePath()), 0777, true);
        }
        file_put_contents($it->getFilePath(), $parms['Content']);

        $command = new ClearCache();
        $command->install();

        $event = new ChangesWaitLockReleaseTrigger();
        $event->process($it, TRIGGER_ACTION_MODIFY);

        return 1;
    }

    function delete( $id, $record_version = ''  )
    {
        $it = $this->getExact($id);

        if ( file_exists($it->getFilePath()) ) {
            unlink($it->getFilePath());
        }

        $command = new ClearCache();
        $command->install();

        $event = new ChangesWaitLockReleaseTrigger();
        $event->process($it, TRIGGER_ACTION_DELETE);

        return 1;
    }
}
