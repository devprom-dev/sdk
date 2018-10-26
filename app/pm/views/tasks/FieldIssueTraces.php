<?php

class FieldIssueTraces extends Field
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
                $widget_it = getFactory()->getObject('ObjectsListWidget')->getByRef('Caption', $class);
                if ( $widget_it->getId() != '' ) {
                    $widget_it = getFactory()->getObject($widget_it->get('ReferenceName'))->getExact($widget_it->getId());
                    $uids[$class][] = '<a href="'.$widget_it->getUrl($class.'='.\TextUtils::buildIds(array_keys($names))).'">'.translate('список').'</a>';
                }
            }
        }

		echo '<div class="input-block-level well well-text">';
		    foreach( $uids as $items ) {
                echo join('<br/>', $items).'<br/><br/>';
            }
		echo '</div>';
	}
}