<?php
include "fields/AutoActionConditionsField.php";

class AutoActionForm extends PMPageForm
{
    private $subject = null;

    function __construct( $object )
    {
        parent::__construct($object);
        $this->subject = getFactory()->getObject($object->getSubjectClassName());
    }

    function extendModel()
	{
		parent::extendModel();

        $object = $this->getObject();
		$object->setAttributeVisible('Actions', false);
        $object->setAttributeType('Actions', 'AutoActions');
        $object->setAttributeVisible('Recurring', true);
        $object->setAttributeOrderNum('Conditions', 5);
    }

	function createFieldObject( $name )
	{
		switch ( $name )
		{
		    case 'Conditions':
		    	return new AutoActionConditionsField($this->getObject());

            case 'Project':
                return new FieldAutoCompleteObject(getFactory()->getObject('ProjectActive'));

            case 'EventType':
                return new FieldDictionary($this->getObject()->getAttributeObject($name));

            case 'State':
                $field = new FieldState(getFactory()->getObject($this->subject->getStateClassName()));
                $field->setNullOption(true);
                return $field;

            case 'Type':
                $object = $this->getObject()->getAttributeObject($name);
                $object->setRegistry(new ObjectRegistrySQL($object));
                return new FieldDictionary($object);

            case 'ResetAttributes':
                $attributes = array_diff(
                    array_keys($this->subject->getAttributes()),
                    $this->subject->getAttributesByGroup('system'),
                    array(
                        'State', 'Fact', 'Author', 'RecordCreated', 'RecordModified'
                    )
                );
                $rowset = array();
                foreach( $attributes as $attribute ) {
                    if ( $this->subject->IsAttributeRequired($attribute) ) continue;
                    if ( !$this->subject->getAttributeEditable($attribute) ) continue;
                    if ( !$this->subject->IsAttributePersisted($attribute) ) continue;
                    $rowset[] = array(
                        'entityId' => $attribute,
                        'Caption' => $this->subject->getAttributeUserName($attribute)
                    );
                }
                usort($rowset, function( $left, $right ) {
                    return $left['Caption'] > $right['Caption'];
                });

                $object = new Metaobject('entity');
                $field = new FieldDictionary($object->createCachedIterator($rowset));
                $field->setMultiple(true);
                return $field;

			default:
			    $groups = $this->getObject()->getAttributeGroups($name);
                if ( in_array('dictionary', $groups) && in_array('actions', $groups) ) {
                    return new FieldCustomDictionary(getFactory()->getObject('Request'), $name);
                }
				return parent::createFieldObject( $name );
		}
	}

	function createField($name)
    {
        $field = parent::createField($name);
        switch( $name ) {
            case 'WebhookURL':
                $field->setRows(1);
                return $field;
            default:
                return $field;
        }
    }

    function IsAttributeVisible( $attr )
    {
        switch ($attr) {
            case 'State':
                return true;
            default:
                return parent::IsAttributeVisible($attr);
        }
    }

    function getDefaultValue( $field )
    {
        switch( $field ) {
            case 'Project':
                return;
            case 'WebhookPayload':
                return \JsonWrapper::encode(
                    array(
                        'timestamp' => '{{timestamp}}',
                        'entity' => '{{entity}}',
                        'text' => '{{text}}',
                        'user' => '{{user}}',
                        'changelog' => array(
                            'items' => '{{item}}'
                        ),
                        'comment' => '{{comment}}',
                        'webhookEvent' => '{{event}}'
                    )
                );
            default:
                return parent::getDefaultValue( $field );
        }
    }

    function getFieldDescription($field_name)
    {
        switch ($field_name) {
            case 'Recurring':
                $moduleIt = getFactory()->getObject('Module')->getExact('dicts-recurring');
                return sprintf(text(3104), $moduleIt->getUrl());
        }
        return parent::getFieldDescription($field_name);
    }

    function drawScripts()
    {
        parent::drawScripts();

        ?>
        <script type="text/javascript">
            $(document).ready( function() {
                showRecurring($('#pm_AutoActionEventType').val() == '<?=AutoActionEventRegistry::Schedule?>');

                $('#pm_AutoActionEventType').change( function() {
                    showRecurring($(this).val() == '<?=AutoActionEventRegistry::Schedule?>');
                });
                function showRecurring(visible) {
                    if ( visible ) {
                        $('#RecurringText').show();
                        $('#RecurringText').parent().prev('label').show();
                    }
                    else {
                        $('#RecurringText').hide();
                        $('#RecurringText').parent().prev('label').hide();
                    }
                }
            });
        </script>
        <?php
    }
}