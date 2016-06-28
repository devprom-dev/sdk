<?php
include_once SERVER_ROOT_PATH."plugins/integration/commands/c_integrationtask.php";
include_once SERVER_ROOT_PATH."plugins/integration/model/validators/IntegrationMappingModelValidator.php";

class IntegrationForm extends PMPageForm
{
	function extendModel()
	{
		if ( is_object($this->getObjectIt()) ) {
			$this->getObject()->setAttributeVisible('Caption', false);
		}
		parent::extendModel();
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
				$field->setRows(34);
				break;
		}
		return $field;
	}

	function getActions()
	{
		$actions = parent::getActions();
		$object_it = $this->getObjectIt();

		$method = new IntegrationTaskRunWebMethod($object_it);
		if ( $method->hasAccess() )
		{
			$method->setRedirectUrl('donothing');
			$actions[] = array();
			$actions[] = array(
				'url' => $method->getJSCall(),
				'name' => $method->getCaption()
			);
		}
        return $actions;
	}

	function buildModelValidator()
	{
		$validators = parent::buildModelValidator();
		$validators->addValidator( new IntegrationMappingModelValidator() );
		return $validators;
	}

	function persist()
	{
		$result = parent::persist();
		if ( $result && is_object($this->getObjectIt()) )
		{
			ob_start();
			$command = new IntegrationTask();
			$command->setChunk(array($this->getObjectIt()->getId()));
			$command->execute();
			ob_end_clean();
		}
		return $result;
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