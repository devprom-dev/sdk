<?php

namespace Devprom\ProjectBundle\Service\Project;
use Devprom\ProjectBundle\Service\Project\StoreMetricsService;

include_once SERVER_ROOT_PATH."ext/xml/xml2Array.php";
include_once SERVER_ROOT_PATH."pm/classes/project/CloneLogic.php";

class ApplyTemplateService
{
	private $reset_state = true;

	public function setResetState( $flag )
	{
		$this->reset_state = $flag;
	}
	
 	function apply( $xml, $project_it, $sections = array(), $except_sections = array() )
 	{
 		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );
		
 		// store initial values for iteration dates
 		$context = new \CloneContext();
 		
 		$context->setResetState($this->reset_state);
 		
        $this->processXml($xml);

 		$objects = count($sections) > 0
 				? $this->getSectionObjects($sections, $except_sections) 
 				: $this->getAllObjects($except_sections);

		foreach ( $objects as $object )
		{
            $class_name = get_class($object);
            if ( $class_name == 'Metaobject' ) {
                $class_name = get_class(getFactory()->getObject($object->getEntityRefName()));
            }
            $class_name = strtolower($class_name);

            if ( $this->entities[$class_name] == '' ) {
                \Logger::getLogger('System')->info('Entity '.$class_name.' is missed in project template file');
                continue;
            }

            $iterator = $object->createXmlIterator($this->entities[$class_name]);
            if ( $iterator->count() < 1 ) continue;

			if ( $object instanceof \ProjectPage ) {
			    $number = $object->getRegistry()->Count(array(new \FilterBaseVpdPredicate()));
                if ( $number > 0 ) continue;
			}

			$object->resetFilters();
			$object->addFilter( new \FilterBaseVpdPredicate() );

			switch ( $object->getEntityRefName() )
			{
				case 'cms_Resource':
				case 'cms_Snapshot':
				case 'pm_Transition':
				case 'pm_TransitionAttribute':
				case 'pm_TransitionResetField':
				case 'pm_TransitionRole':
				case 'pm_TransitionPredicate':
				case 'pm_TaskTypeStage':
				case 'pm_Predicate':
				case 'pm_StateAction':
                case 'pm_AutoAction':
				case 'pm_StateAttribute':
                case 'pm_TaskTypeState':
				case 'pm_AccessRight':
				case 'pm_Workspace':
                case 'pm_TextTemplate':
                    $object->deleteAll();
                    break;

                case 'pm_State':
                    $newStates = array();
                    while( !$iterator->end() ) {
                        $newStates[] = $iterator->get('ObjectClass').':'.$iterator->get('ReferenceName');
                        $iterator->moveNext();
                    }
                    $iterator->moveFirst();

                    $oldStates = array();
                    $oldIt = $object->getAll();
                    while( !$oldIt->end() ) {
                        $oldStates[] = $oldIt->get('ObjectClass').':'.$oldIt->get('ReferenceName');
                        $oldIt->moveNext();
                    }

                    $removeStates = array_diff($oldStates, $newStates);
                    if ( count($removeStates) > 0 ) {
                        foreach( $removeStates as $removeState ) {
                            list($objectClass, $referenceName) = preg_split('/:/', $removeState);
                            $stateIt = $object->getRegistry()->Query(
                                array(
                                    new \FilterVpdPredicate(),
                                    new \FilterAttributePredicate('ReferenceName', $referenceName),
                                    new \FilterAttributePredicate('ObjectClass', $objectClass)
                                )
                            );
                            $stateIt->delete();
                        }
                    }
					break;

				case 'pm_UserSetting':
					if ( $iterator->count() > 0 ) {
						// remove common (glogal) settings for reports/modules
						$it = $object->getByRefArray(
                            array (
                                'Participant' => -1
                            )
						);
						while( !$it->end() ) {
							$it->delete();
							$it->moveNext();
						}
					}
					
					break;
			}

			\CloneLogic::Run( $context, $object, $iterator, $project_it ); 
		} 

		$stateableEntities = array(
		    'Request',
            'Task',
            'Requirement',
            'TestScenario',
            'HelpPage',
            'Question'
        );
		foreach( $stateableEntities as $className ) {
            if ( !class_exists(getFactory()->getClass($className)) ) continue;

            $object = getFactory()->getObject($className);
            $stateObject = getFactory()->getObject($object->getStateClassName());
            if ( !is_object($stateObject) ) continue;

            $states = $stateObject->getAll()->fieldToArray('ReferenceName');
            $firstState = array_shift(array_values($states));

            $registry = $object->getRegistry();
            $registry->setPersisters(array());

            $object_it = $registry->Query(
                array (
                    new \FilterBaseVpdPredicate(),
                    new \FilterHasNoAttributePredicate('State', $states)
                )
            );
            if ( $object_it->count() > 0 ) {
                \DAL::Instance()->Query(
                    "UPDATE ".$object->getEntityRefName()." SET State = '".$firstState."' WHERE ".$object->getIdAttribute()." IN (".join(',',$object_it->idsToArray()).")"
                );
            }
        }

		$metrics_service = new StoreMetricsService();
		$metrics_service->execute($project_it, true);

        getFactory()->getCacheService()->invalidate('sessions');
        getFactory()->getCacheService()->invalidate(
 		    array_shift(preg_split('/\//',getSession()->getCacheKey()))
        );
 	}
 	
 	static protected function getSectionObjects( $sections, $except_sections = array() )
 	{
 		$objects = array(
 				getFactory()->getObject('ProjectRole')
 		);
 		
		$section_it = getFactory()->getObject('ProjectTemplateSections')->getAll();
		
 		while ( !$section_it->end() )
 		{
 			if ( in_array($section_it->get('ReferenceName'), $except_sections) )
 			{
 				$section_it->moveNext();
 				continue;
 			}
 			
 			if ( !in_array($section_it->get('ReferenceName'), $sections) )
 			{
 				$section_it->moveNext();
 				continue;
 			}
 			
 			$objects = array_merge($objects, $section_it->get('items'));
 			
 			$section_it->moveNext();
 		}
 		
 		return $objects;
 	}
 	
 	static public function getAllObjects( $except_sections = array() )
 	{
 		$objects = array();
 		
		$section_it = getFactory()->getObject('ProjectTemplateSections')->getAll();

 	 	while ( !$section_it->end() )
 		{
 		 	if ( in_array($section_it->get('ReferenceName'), $except_sections) )
 			{
 				$section_it->moveNext();
 				continue;
 			}
 			
 			$objects = array_merge($objects, $section_it->get('items'));

 			if ( $section_it->get('ReferenceName') == 'pm_Project')
 			{
 				$objects = array_merge($objects, array (
 						getFactory()->getObject('Participant'),
		 				getFactory()->getObject('ParticipantRole'),
			 			getFactory()->getObject('Release'),
			 			getFactory()->getObject('Iteration')
				));
 			}
 			
 			$section_it->moveNext();
 		}

 		$result = array();
 		
 		foreach( $objects as $object )
 		{
 			$hash = get_class($object).$object->getEntityRefName();
 			
 			if ( !array_key_exists($hash, $result) ) $result[$hash] = $object;
 		}
 		
 		return $result;
 	}

    protected static function wintoutf8($s)
    {
        if ( function_exists('mb_convert_encoding') ) return mb_convert_encoding($s, "utf-8", "cp1251");
        if ( function_exists('iconv') ) return iconv("cp1251", "utf-8//IGNORE", $s);
        $t = '';
        for ($i = 0, $m = strlen($s); $i < $m; $i++) {
            $c = ord($s[$i]);
            if ($c <= 127) { $t .= chr($c); continue; }
            if ($c >= 192 && $c <= 207) { $t .= chr(208) . chr($c - 48); continue; }
            if ($c >= 208 && $c <= 239) { $t .= chr(208) . chr($c - 48); continue; }
            if ($c >= 240 && $c <= 255) { $t .= chr(209) . chr($c - 112); continue; }
            if ($c == 184) { $t .= chr(209) . chr(209); continue; };
            if ($c == 168) { $t .= chr(208) . chr(129); continue; };
        }
        return $t;
    }

    function processEntity( $tag_name, $entity )
    {
        if ( strtolower($tag_name) != 'entity' ) return false;

        $class_name = getFactory()->getClass($entity['attrs']['CLASS']);
        if ( !class_exists($class_name, false) ) return true;

        $class_name = strtolower($class_name);
        $this->entities[$class_name] = $entity;

        return true;
    }

    function processXml( $xml )
    {
        $xml = preg_replace('/<\?xml[^\?]+\?>/', '', $xml);
        $xml = preg_replace('/<\/?entities>/', '', $xml);
        $delimiter = '<entity';
        $strings = explode($delimiter, $xml);
        $lastKey = array_pop(array_keys($strings));

        foreach( $strings as $key => $string ) {
            $string = $delimiter.$string;

            $parser = xml_parser_create();
            xml_set_object($parser,$this);
            xml_set_element_handler($parser, "tagOpen", "tagClosed");
            xml_set_character_data_handler($parser, "tagData");

            $result = xml_parse($parser,$string,$key == $lastKey);
            if(!$result) {
                \Logger::getLogger('System')->error(
                    sprintf("XML error: %s at line %d at column %d with text %s",
                        xml_error_string(xml_get_error_code($parser)),
                        xml_get_current_line_number($parser),
                        xml_get_current_column_number($parser),
                        $string
                    )
                );
            }
            xml_parser_free($parser);
        }
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
        if ( $this->processEntity($name, $node) ) {
            $this->accumulateData = false;
        }
        if ( $this->accumulateData ) {
            $this->nodeData[count($this->nodeData)-2]['children'][] = $node;
            array_pop($this->nodeData);
        }
        else {
            $this->nodeData = array();
        }
    }

    private $accumulateData = false;
    private $nodeData = array();
    private $resParser = null;
    private $entities = array();
}