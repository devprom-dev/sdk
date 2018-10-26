<?php

class WikiPageDisplayRules
{
	static public function getTraceDisplayName( $object_it, $page_it )
	{
        $title = '';

		if ( $object_it->object->IsReference('Type') && $object_it->get('Type') != '' ) {
	 		$title .= $object_it->getRef('Type')->getDisplayName().": ";
		}

        if ( $page_it->get('BrokenTraces') != '' ) {
            $title .= '<img class="trace-state" id="trace-state-'.$page_it->getId().'" src="/images/exclamation.png" title="'.text(770).'"> ';
        }

        $uid = new ObjectUID($object_it->get($object_it->object->getBaselineReference()));
        $title .= ' ' . $uid->getUidIcon( $page_it );
        $title .= $page_it->getStateTag();

        if ( $object_it->get('SourceBaseline') != '' && $object_it->get('SourcePage') == $page_it->getId() ) {
            $title .= ' ['.$object_it->getRef('SourceBaseline')->getDisplayName().'] ';
        }
        else {
            if ( $page_it->get('DocumentVersion') != '' ) {
                $title .= ' ['.$page_it->get('DocumentVersion').'] ';
            }
        }
        if ( $page_it->get('DocumentName') != '' && $page_it->get('ParentPage') != '' ) {
            $title .= $page_it->get('DocumentName') . ' / ' ;
        }

        return $title . $page_it->getDisplayName();
	}
}