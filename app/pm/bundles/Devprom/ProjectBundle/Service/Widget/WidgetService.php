<?php
namespace Devprom\ProjectBundle\Service\Widget;

class WidgetService
{
    static public function getTraceDisplayName( $object_it, $page_it )
    {
        $title = '';

        if ( $page_it->get('Suspected') > 0 ) {
            $title .= self::getHtmlBrokenIcon($page_it->getId(), getSession()->getApplicationUrl($page_it));
        }

        $uid = new \ObjectUID($object_it->get($object_it->object->getBaselineReference()));
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

    static public function getRequestTraceDisplayName( $object_it, $page_it )
    {
        $title = '';

        if ( $page_it->get('Suspected') > 0 ) {
            $title .= self::getHtmlBrokenIcon($page_it->getId(), getSession()->getApplicationUrl($page_it));
        }

        $uid = new \ObjectUID($object_it->get('Baseline'));
        $title .= ' ' . $uid->getUidIcon( $page_it );
        $title .= $page_it->getStateTag();

        if ( $object_it->get('Baseline') != '' ) {
            $title .= ' ['.$object_it->getRef('Baseline')->getDisplayName().'] ';
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

    static public function getHtmlBrokenIcon($id, $url)
    {
        $method = "javascript: runMethod('".$url."methods.php?method=OpenBrokenTraceWebMethod', {'object' : '".$id."'}, '', '');";
        $tooltip_url = $url.'tooltip/explain/'.$id;

        return '<a class="with-tooltip" tabindex="-1" data-placement="right" data-original-title="" data-content="" info="'.$tooltip_url.'" href="'.$method.'">
            <img class="trace-state" src="/images/exclamation.png"></a>';
    }
}