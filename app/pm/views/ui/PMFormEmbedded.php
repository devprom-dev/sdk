<?php

class PMFormEmbedded extends FormEmbedded
{
    var $customtypes;
    var $customkinds;

    function __construct( $object = null, $anchor_field = null, $form_field = '' )
    {
        parent::__construct( $object, $anchor_field, $form_field );
        	
        $this->customtypes = array();
        $this->customkinds = array();
        	
        if ( is_object($object) && getFactory()->getObject('CustomizableObjectSet')->checkObject($object) )
        {
            $it = getFactory()->getObject('pm_CustomAttribute')->getByEntity($object);
            
            while ( !$it->end() )
            {
                $this->customtypes[$it->get('ReferenceName')] = $it->getRef('AttributeType')->get('ReferenceName');
                	
                if ( $it->get('ObjectKind') != '' )
                    $this->customkinds[$it->get('ReferenceName')] = $it->get('ObjectKind');
                	
                $it->moveNext();
            }
        }
    }

    function createField( $attr )
    {
        switch ( $attr )
        {
            default:
                foreach ( $this->customtypes as $refname => $type )
                {
                    if ( $attr == $refname && $type == 'dictionary' )
                    {
                        return new FieldCustomDictionary( $this->getObject(), $refname );
                    }
                }
                
                if ( $this->getObject()->getAttributeType($attr) == 'wysiwyg')
                {
                    $field = new FieldWYSIWYG();

                    $object_it = $this->getObjectIt();
                    is_object($object_it) ? $field->setObjectIt($object_it)
                            : $field->setObject($this->getObject());

                    $editor = $field->getEditor();
                    $editor->setMode( WIKI_MODE_MINIMAL | WIKI_MODE_INLINE );
                    $editor->setMinRows(5);
                    $field->setHasBorder(true);

                    return $field;
                }
                
                return parent::createField( $attr );
        }
    }

    function getListItemsTitle() {
        return text(1936);
    }

    function getListItemsAttribute() {
        return '';
    }

    function drawAddButton( $view, $tabindex )
    {
        parent::drawAddButton( $view, $tabindex );

        if( $this->getIteratorRef()->count() > 0 )
        {
            $target = defined('SKIP_TARGET_BLANK') && SKIP_TARGET_BLANK ? '' : '_blank';
            $vpds = array();

            $attribute = $this->getListItemsAttribute();
            if ( $attribute == '' ) {
                $object = $this->getIteratorRef()->object;
                $ids = $this->getIteratorRef()->idsToArray();
                $vpds = $this->getIteratorRef()->fieldToArray('VPD');
            }
            else {
                $object = $this->getIteratorRef()->object->getAttributeObject($attribute);
                $ids = preg_split('/,/',join(',',$this->getIteratorRef()->fieldToArray($attribute)));

                if ( count($ids) > 0 ) {
                    $vpds = $object->getRegistryBase()->Query(array(new FilterInPredicate($ids)))->fieldToArray('VPD');
                }
            }

            $url = WidgetUrlBuilder::Instance()->buildWidgetUrlIds(get_class($object), $ids, $vpds, $this->getListUrlKey());
            if ( $url != '' ) {
                echo '<a class="dashed embedded-add-button items-list" target="'.$target.'" href="'.$url.'" tabindex="-1">';
                    echo $this->getListItemsTitle();
                echo '</a>';
            }
        }
    }

    function getListUrlKey() {
        return 'ids';
    }
}