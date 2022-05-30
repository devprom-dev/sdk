<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelCustomAttributesValidator extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array $parms )
	{
        $id = $parms[$object->getIdAttribute()];

        $attribute = getFactory()->getObject('pm_CustomAttribute');
        $it = $attribute->getByEntity($object);

        $unique_attrs = array();
        while (!$it->end())
        {
            $value = $parms[$it->get('ReferenceName')];

            if ($it->get('IsUnique') == 'Y' && $value != '') {
                $unique_attrs[] = array(
                    'ReferenceName' => $it->get('ReferenceName'),
                    'AttributeType' => $it->get('AttributeType'),
                    'Id' => $it->getId(),
                    'DisplayName' => $it->getDisplayName(),
                    'AttributeValue' => strtolower($value)
                );
            }

            $it->moveNext();
        }

        $value_object = getFactory()->getObject('pm_AttributeValue');
        foreach ($unique_attrs as $key => $attr)
        {
            $field = $attribute->getAttributeObject('AttributeType')->getExact($attr['AttributeType'])->getValueColumn();

            $attr_it = $value_object->getByRefArray( array(
                'CustomAttribute' => $attr['Id'],
                'LCASE(' . $field . ')' => $attr['AttributeValue']
            ));

            if ($attr_it->count() > 0 && $attr_it->get('ObjectId') != $id) {
                return str_replace('%1', $attr['DisplayName'], text(1176));
            }
        }

        return "";
	}
}