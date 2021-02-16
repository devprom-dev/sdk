<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorTaskTraces extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
	    if ( $parms['Task'] == '' && $parms['ObjectId'] == '' ) return "";

        $ids = array(
            $parms['Task'],
            $parms['ObjectId']
        );

        $it = $object->getByRef('Task', $parms['ObjectId']);
        while( count($ids) <= count(array_unique($ids)) && $it->count() > 0 ) {
            $it = $object->getByRef('Task', $it->fieldToArray('ObjectId'));
            $ids[] = $it->fieldToArray('ObjectId');
        }

        $it = $object->getByRef('ObjectId', $parms['Task']);
        while( count($ids) <= count(array_unique($ids)) && $it->count() > 0 ) {
            $it = $object->getByRef('ObjectId', $it->fieldToArray('Task'));
            $ids[] = $it->fieldToArray('Task');
        }

        if ( count($ids) > count(array_unique($ids)) ) {
            return text(2916);
        }

        return "";
	}
}