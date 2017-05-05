<?php
include 'IntegrationIterator.php';

class Integration extends Metaobject
{
    public function __construct()
    {
        parent::__construct('pm_Integration');
        $this->setAttributeCaption('Caption', text('integration14'));
        $this->setAttributeDescription('HttpUserPassword', text('integration9'));
        $this->setAttributeType('Caption', 'REF_IntegrationApplicationId');
        $this->setAttributeType('Type', 'REF_IntegrationTypeId');
        $this->addAttributeGroup('MappingSettings', 'mapping');
        $this->addAttributeGroup('Log', 'additional');
        $this->setAttributeDescription('HttpHeaders', text('integration10'));
        $this->setAttributeDescription('URL', text('integration11'));
        $this->setAttributeDescription('ProjectKey', text('integration12'));
        foreach( array('Log','ItemsQueue','HttpHeaders','MappingSettings') as $attribute ) {
            $this->addAttributeGroup($attribute, 'system');
        }
    }

    public function createIterator() {
        return new IntegrationIterator($this);
    }
}