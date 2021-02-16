<?php
include_once SERVER_ROOT_PATH."core/views/PageSectionLastChanges.php";
include_once SERVER_ROOT_PATH."pm/views/ui/FieldWYSIWYG.php";

class PMLastChangesSection extends LastChangesSection
{
    function __construct( $object )
    {
        parent::__construct($object);
        $this->setItems(12);
    }

    function getTemplate() {
        return 'core/PageSectionLazy.php';
    }

    function getBodyTemplate() {
        return parent::getTemplate();
    }

    function getContent( $objectIt )
    {
        $field = new FieldWYSIWYG();
        $field->setObjectIt($objectIt);
        $field->setValue($objectIt->get('Content'));
        return $field->getText();
    }

    function getRenderParms()
    {
        return array (
            'id' => md5(microtime().get_class($this)),
            'class' => strtolower(get_class($this)),
            'url' => getSession()->getApplicationUrl().'section/audit?class='.get_class($this->object->object).'&id='.$this->object->getId()
        );
    }

    function getBodyRenderParms()
    {
        return parent::getRenderParms();
    }

    function renderBody( $view, $parms = array() )
    {
        echo $view->render(
            $this->getBodyTemplate(),
            array_merge($parms, $this->getBodyRenderParms())
        );
    }

    function hasAccess() {
        return getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Module')->getExact('project-log'));
    }
}