<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class IntegrationQueueModelValidator extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		if ( !array_key_exists('QueueDate', $parms) ) return "";
		if ( $parms['pm_IntegrationId'] == '' ) return "";

		$objectIt = $object->getExact($parms['pm_IntegrationId']);
		$data = \JsonWrapper::decode(
            $objectIt->getHtmlDecoded('ItemsQueue')
        );

		if ( $data['remote_timestamp'] != '' ) {
            $data['remote_timestamp'] = $parms['QueueDate'];
        }

        $parms['ItemsQueue'] = \JsonWrapper::encode($data);
		return "";
	}
}