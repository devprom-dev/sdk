<?php

class ObjectsListWidgetRegistry extends ObjectRegistrySQL
{
	private $data = array();
	
	public function addReport( $class_name, $report_uid, $title = '' )
	{
		$this->data[] = array (
				'ReferenceName' => 'PMReport',
				'Caption' => $class_name,
				'entityId' => $report_uid,
				'WidgetTitle' => $title
		);
	}
	
	public function addModule( $class_name, $module_uid, $title = '' )
	{
		$this->data[] = array (
				'ReferenceName' => 'Module',
				'Caption' => $class_name,
				'entityId' => $module_uid,
				'WidgetTitle' => $title
		);
	}
	
 	function createSQLIterator( $sql )
 	{
 		foreach( getSession()->getBuilders('ObjectsListWidgetBuilder') as $builder ) {
 			$builder->build($this);
 		}

        $report = getFactory()->getObject('PMReport');
        $report_it = $report->getAll();
        $module = getFactory()->getObject('Module');
        $module_it = $module->getAll();

        foreach( $this->data as $key => $item )
        {
            switch( $item['ReferenceName'] ) {
                case 'PMReport':
                    $widget_it = $report_it->moveToId($item['entityId']);
                    break;
                default:
                    $widget_it = $module_it->moveToId($item['entityId']);
            }
            $this->data[$key]['data'] = $widget_it->getData();
        }

        return $this->createIterator( $this->data );
 	}
}