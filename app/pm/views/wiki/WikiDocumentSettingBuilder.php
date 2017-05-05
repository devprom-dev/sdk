<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class WikiDocumentSettingBuilder extends PageSettingBuilder
{
    private $object = null;

    public function __construct( $object ) {
        $this->object = $object;
    }

    public function build( PageSettingSet & $settings )
    {
        $object = $this->object;

        $setting = new PageListSetting('PMWikiDocumentList');
        $setting->setGroup( 'none' );
        $columns = array('Content');
        $columns[] = 'SectionNumber';
        $columns[] = 'Tags';
        $setting->setVisibleColumns(
            array_merge(
                $columns,
                array_diff(
                    array_filter($object->getAttributesByGroup('trace'), function($value) use($object) {
                        return !in_array($value, array('Dependency','UsedBy','Feature'));
                    }),
                    $object->getAttributesByGroup('source-attribute')
                )
            )
        );
        $settings->add( $setting );
        
        $setting = new PageTableSetting('PMWikiDocument');
	    $setting->setSorts( array('none') );
	    $settings->add( $setting );
    }
}