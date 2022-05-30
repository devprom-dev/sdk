<?php
include_once SERVER_ROOT_PATH."plugins/integration/commands/c_integrationtask.php";
include_once SERVER_ROOT_PATH."plugins/integration/model/validators/IntegrationMappingModelValidator.php";

class IntegrationForm extends PMPageForm
{
	function extendModel()
	{
		parent::extendModel();

		$object = $this->getObject();
		$object->setAttributeVisible('Caption', false);
		$object->setAttributeEditable('Log', false);

		$app_it = getFactory()->getObject('IntegrationApplication')->getAll();
		$app_it->moveToId($this->getFieldValue('Caption'));
		if ( $app_it->get('ModelBuilder') != '' ) {
			$builderClassName = $app_it->get('ModelBuilder');
			if ( class_exists($builderClassName) ) {
				$builder = new $builderClassName;
				$builder->build($object);
			}
		}

		if ( is_object($this->getObjectIt()) ) {
            $object->addAttribute('QueueItemsCount', 'INTEGER', text('integration28'), true, false, '', 10);
            $object->addAttributeGroup('QueueItemsCount', 'additional');
            $object->setAttributeEditable('QueueItemsCount', false);
            $object->addAttribute('QueueDate', 'DATETIME', text('integration29'), true, false, '', 20);
            $object->addAttributeGroup('QueueDate', 'additional');
        }
	}

	function createFieldObject( $name )
	{
		switch ( $name )
		{
			case 'Log':
				return new FieldText();
			default:
				return parent::createFieldObject( $name );
		}
	}

	function createField($name)
	{
		$field = parent::createField($name);
		switch ($name) {
			case 'Log':
			case 'MappingSettings':
				$field->setRows(26);
				break;
		}
		return $field;
	}

	function getActions()
	{
		$actions = parent::getActions();
		$object_it = $this->getObjectIt();

        $job_it = getFactory()->getObject('co_ScheduledJob')->getByRef('ClassName', 'integration/integrationtask');
        $url = '/tasks/command.php?class=runjobs&job='.$job_it->getId().'&chunk='.$object_it->getId().'&redirect='.urlencode($_SERVER['REQUEST_URI']);

        $actions[] = array();
        $actions[] = array(
            'url' => $url,
            'name' => translate('integration7')
        );

        return $actions;
	}

	function getValidators()
    {
        return array_merge(
            parent::getValidators(),
            array(
                new IntegrationMappingModelValidator()
            )
        );
    }

    function getDefaultValue( $field ) {
		switch( $field ) {
			case 'MappingSettings':
				$appId = parent::getFieldValue('Caption');
				if ( $appId == '' ) return '';
				$app_it = getFactory()->getObject('IntegrationApplication')->getAll();
				$app_it->moveToId($appId);
				return file_get_contents(SERVER_ROOT_PATH.$app_it->get('ReferenceName'));
			default:
				return parent::getDefaultValue($field);
		}
	}

	function getFieldValue($field)
    {
        switch( $field ) {
            case 'QueueItemsCount':
                $data = $this->getQueueData();
                return count($data['items']);

            case 'QueueDate':
                $data = $this->getQueueData();
                return $data['remote_timestamp'];

            default:
                return parent::getFieldValue($field);
        }
    }

    function getQueueData() {
	    if ( !is_object($this->getObjectIt()) ) return array();
	    return \JsonWrapper::decode(
            $this->getObjectIt()->getHtmlDecoded('ItemsQueue')
        );
    }

    function drawScripts()
	{
		parent::drawScripts();

		$items = array();
		foreach( getFactory()->getObject('IntegrationApplication')->getAll()->getRowset() as $row ) {
			$items[$row['entityId']] = $row['ReferenceName'];
		}
		?>
		<script type="text/javascript">
			var mappingSettings = <?=json_encode($items)?>;
			$().ready( function() {
				$('#pm_IntegrationCaption').change( function() {
					$.get(mappingSettings[$(this).val()], function(data) {
						$('#pm_IntegrationMappingSettings').val(data);
					}, "html");
				});
			});
		</script>
		<?php
	}
}