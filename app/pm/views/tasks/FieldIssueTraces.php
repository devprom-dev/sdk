<?php

class FieldIssueTraces extends FieldForm
{
	private $traces = '';

	function __construct( $traces )
	{
		parent::__construct();
		$this->traces = $traces;
	}

	function render( $view )
	{
		$uid = new ObjectUID();

		$objects = preg_split('/,/', $this->traces);
		$items = array();
		foreach( $objects as $object_info ) {
			list($class, $id) = preg_split('/:/',$object_info);
            $items[$class][] = $id;
		}

        $uids = array();
		foreach( $items as $class => $ids ) {
		    if ( !class_exists($class) ) continue;
            $ids = array_filter($ids, function($value) {
		        return $value > 0;
            });
            if ( count($ids) < 1 ) continue;
            $ref_it = getFactory()->getObject($class)->getRegistry()->Query(
                array(
                    new FilterInPredicate($ids)
                )
            );
            $names = array();
            while( !$ref_it->end() ) {
                $names[$ref_it->getId()] = $uid->getUidWithCaption($ref_it);
                $ref_it->moveNext();
            }

            $uids[$class] = array_slice(array_values($names), 0, 5);
            if ( count($names) > 5 ) {
                $url = WidgetUrlBuilder::Instance()->buildWidgetUrlIt($ref_it);
                if ( $url != '' ) {
                    $uids[$class][] = '<a href="'. $url . '" class="dashed embedded-add-button" target="_blank">'.translate('список').'</a>';
                }
            }
        }

        foreach( $uids as $items ) {
            echo join('<br/>', $items).'<br/><br/>';
        }
	}
}