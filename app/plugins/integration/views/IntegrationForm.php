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

		$app_it = getFactory()->getObject('IntegrationApplication')->getAll();
		$app_it->moveToId($this->getFieldValue('Caption'));
		if ( $app_it->get('ModelBuilder') != '' ) {
			$builderClassName = $app_it->get('ModelBuilder');
			if ( class_exists($builderClassName) ) {
				$builder = new $builderClassName;
				$builder->build($object);
			}
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