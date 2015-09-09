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
	
 	function apply( $template_it, $project_it, $sections = array(), $except_sections = array() )
 	{
 		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );
		
 		// store initial values for iteration dates
 		$context = new \CloneContext();
 		
 		$context->setResetState($this->reset_state);
 		
 		$state_objects = array();

        $rowsets = $this->getRowsets(file_get_contents($template_it->object->getTemplatePath($template_it->get('FileName'))));
 		$objects = count($sections) > 0
 				? $this->getSectionObjects($sections, $except_sections) 
 				: $this->getAllObjects($except_sections);

		foreach ( $objects as $object )
		{
            $class_name = get_class($object);
            if ( $class_name == 'Metaobject' ) {
                $class_name = get_class(getFactory()->getObject($object->getEntityRefName()));
            }
            $rowset = $rowsets[$class_name];

            if ( !is_array($rowset) || count($rowset) < 1 ) continue;

            $iterator = $object->createCachedIterator($rowset);
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
				case 'pm_StateAttribute':
				case 'pm_AccessRight':
				case 'pm_Workspace':
					
					$object->deleteAll();
						
					break;
					
				case 'pm_CustomReport':

					if ( $iterator->count() > 0 )
					{
						// remove common (glogal) reports
						$report_it = $object->getByRefArray(
								array (
										'Author' => -1
								)
						);
						
						while( !$report_it->end() )
						{
							$report_it->delete();
							$report_it->moveNext();
						}
					}
					
					break;

				case 'pm_UserSetting':

					if ( $iterator->count() > 0 )
					{
						// remove common (glogal) settings for reports/modules
						$it = $object->getByRefArray(
								array (
										'Participant' => -1
								)
						);
						
						while( !$it->end() )
						{
							$it->delete();
							$it->moveNext();
						}
					}
					
					break;
					
				case 'pm_State':
					
					$state_objects[] = $object;
					
					break;
			}

			\CloneLogic::Run( $context, $object, $iterator, $project_it ); 
		} 

		// remove unnecessary data
		foreach( $state_objects as $state )
		{
			$state_it = $state->getRegistry()->Query(
					array (
							new \FilterBaseVpdPredicate(),
							new \StateHasNoTransitionsPredicate(),
							new \StateHasNoObjectsPredicate()
					)
			);
			
			while( !$state_it->end() )
			{
				$state_it->delete();
				$state_it->moveNext();
			}
		}
		
		$metrics_service = new StoreMetricsService();
		$metrics_service->execute($project_it);
 		
 		getSession()->truncate();
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

	protected function getRowsets( $xml )
	{
        $xml_array = new \xml2Array;
        $xml_data = $xml_array->xmlParse($xml);

        $entity = $xml_data;
        if ( strtolower($xml_data['name']) != 'entities' )
        {
            $data[0] = $xml_data;
        }
        else
        {
            $data = $xml_data['children'];
        }

        $result = array();

        foreach ( $data as $entity )
        {
            $class_name = getFactory()->getClass($entity['attrs']['CLASS']);

            if ( $class_name == '' || !class_exists($class_name, false ) ) continue;
            if ( !is_array($entity['children']) ) continue;

            $object = getFactory()->getObject($class_name);
            $class_name = get_class($object);

            foreach ( $entity['children'] as $object_tag )
            {
                $record[$object->getEntityRefName().'Id'] = $object_tag['attrs']['ID'];
                foreach ( $object_tag['children'] as $attr_tag )
                {
                    if ( $attr_tag['attrs']['ENCODING'] != '' ) {
                        $attr_tag['tagData'] = base64_decode($attr_tag['tagData']);
                    }
                    if ( in_array($entity['attrs']['ENCODING'], array('','windows-1251')) ) {
                        $attr_tag['tagData'] = $this->wintoutf8($attr_tag['tagData']);
                    }
                    $record[$attr_tag['attrs']['NAME']] = $attr_tag['tagData'];
                }
                $result[$class_name][] = $record;
            }
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
}