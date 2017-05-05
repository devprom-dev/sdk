<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
require_once SERVER_ROOT_PATH.'ext/xml/xml2Array.php';

class UndoWebMethod extends WebMethod
{
	private $transaction = '';
    private $projectCode = '';

 	function __construct( $transaction = '', $projectCode = '' ) {
		$this->transaction = $transaction;
        $this->projectCode = $projectCode;
 		parent::__construct();
 	}
 	
 	function hasAccess() {
 		return file_exists(UndoLog::Instance()->getPath($this->transaction));
 	}

    function getModule() {
        return getSession()->getApplicationUrl($this->projectCode).'methods.php';
    }

 	function getCaption() {
 		return text(2160);
 	}

 	function getJSCall( $parms = array() ) {
		return parent::getJSCall(
			array (
				'transaction' => $this->transaction
			)
		);
 	}
 	
	function setCookie() {
		$this->setRedirectUrl('donothing');
		setcookie(
			'last-action-message',
			preg_replace('/%1/', $this->getJSCall(), text(2211)),
			null,
			'/'
		);
	}
	
 	function execute_request()
 	{
		if ( $_REQUEST['transaction'] != '' ) {
			$this->transaction = $_REQUEST['transaction'];
		}
		if ( !$this->hasAccess() ) throw new Exception('Unknown transaction given: '.$this->transaction);

		\Logger::getLogger('System')->info('Start undone transaction '.$this->transaction);

		$this->context = new CloneContext();
		$this->context->setUseExistingReferences(true);
		$this->context->setResetState(false);
		$this->context->setResetDates(false);
		$this->context->setResetAssignments(false);

		$this->project_it = getSession()->getProjectIt();
		$this->processXml(
			file_get_contents(UndoLog::Instance()->getPath($this->transaction))
		);

		\Logger::getLogger('System')->info('Transaction '.$this->transaction.' has been undone');

		$log_it = getFactory()->getObject('ChangeLog')->getRegistry()->Query(
			array (
				new FilterAttributePredicate('Transaction', $this->transaction)
			)
		);
        $redirect_url = '';

        $log = new Metaobject('ObjectChangeLog');
		while( !$log_it->end() ) {
            $object_it = $log_it->getObjectIt();
            if ( $object_it->getId() != '' && $redirect_url == '' ) {
                $redirect_url = $object_it->getViewUrl();
            }
			$log->delete($log_it->getId());
			$log_it->moveNext();
		}

		if ( $redirect_url != '' ) {
            echo json_encode(
                array (
                    'url' => $redirect_url
                )
            );
        }
 	}

	function processEntity( $tag_name, $entity )
	{
		if ( strtolower($tag_name) != 'entity' ) return false;

		$class_name = getFactory()->getClass($entity['attrs']['CLASS']);
		if ( !class_exists($class_name, false) ) return true;

		$object = getFactory()->getObject($class_name);
		$registry = new ObjectRegistrySQL($object);
		$iterator = $object->createXMLIterator($entity);

		$ids = $iterator->idsToArray();

		// exclude items exist already
		$object_it = $registry->Query(array(new FilterInPredicate($ids)));
		$foundIds = $object_it->idsToArray();
		$ids = array_diff($ids, $foundIds);
		if ( count($ids) < 1 ) return true;

		$idsMap = $this->context->getIdsMap();
		foreach( $ids as $id ) {
			$idsMap[$object->getEntityRefName()][$id] = $id;
		}
		$this->context->setIdsMap($idsMap);
		\Logger::getLogger('System')->info('UNDO: '.$class_name.' ['.join(',',$idsMap[$object->getEntityRefName()]).']');

		$object->removeNotificator('ChangeLogNotificator');
		$object->removeNotificator('EmailNotificator');

		$iterator->moveFirst();
		CloneLogic::Run( $this->context, $object, $iterator, $this->project_it);

		return true;
	}

	function processXml( $xml )
	{
		$this->resParser = xml_parser_create ();
		xml_set_object($this->resParser,$this);
		xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
		xml_set_character_data_handler($this->resParser, "tagData");

		$delimiter = 'entity';
		$strings = explode($delimiter, $xml);
		$lastKey = array_pop(array_keys($strings));

		foreach( $strings as $key => $string ) {
			if ( $key != $lastKey ) $string .= $delimiter;
			$result = xml_parse($this->resParser,$string,$key == $lastKey);
			if(!$result) {
				throw new Exception(
					sprintf("XML error: %s at line %d at column %d",
						xml_error_string(xml_get_error_code($this->resParser)),
						xml_get_current_line_number($this->resParser),
						xml_get_current_column_number($this->resParser)
					)
				);
			}
		}
		xml_parser_free($this->resParser);
	}

	protected function tagOpen($parser, $name, $attrs)
	{
		$tag=array("name"=>$name,"attrs"=>$attrs);
		if ( strtolower($name) == 'entity' ) {
			$this->accumulateData = true;
		}
		if ( $this->accumulateData ) {
			array_push($this->nodeData,$tag);
		}
		else {
			$this->nodeData = array();
		}
	}

	protected function tagData($parser, $tagData) {
		if(trim($tagData)) {
			if(isset($this->nodeData[count($this->nodeData)-1]['tagData'])) {
				$this->nodeData[count($this->nodeData)-1]['tagData'] .= $tagData;
			} else {
				$this->nodeData[count($this->nodeData)-1]['tagData'] = $tagData;
			}
		}
		elseif ( $tagData == chr(10) )
		{
			if(isset($this->nodeData[count($this->nodeData)-1]['tagData'])) {
				$this->nodeData[count($this->nodeData)-1]['tagData'] .= $tagData;
			}
		}
	}

	protected function tagClosed($parser, $name) {
		$node = $this->nodeData[count($this->nodeData)-1];
        try {
            if ( $this->processEntity($name, $node) ) {
                $this->accumulateData = false;
            }
        }
        catch (Exception $e) {
            \Logger::getLogger('System')->error(
                $e->getMessage().$e->getTraceAsString()
            );
        }
		if ( $this->accumulateData ) {
			$this->nodeData[count($this->nodeData)-2]['children'][] = $node;
			array_pop($this->nodeData);
		}
		else {
			$this->nodeData = array();
		}
	}

	private $project_it = null;
	private $context = null;
	private $accumulateData = false;
	private $nodeData = array();
	private $resParser = null;
}