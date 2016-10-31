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

            $attribute = $this->getListItemsAttribute();
            if ( $attribute == '' ) {
                $object = $this->getIteratorRef()->object;
                $ids = $this->getIteratorRef()->idsToArray();
            }
            else {
                $object = $this->getIteratorRef()->object->getAttributeObject($attribute);
                $ids = preg_split('/,/',join(',',$this->getIteratorRef()->fieldToArray($attribute)));
            }

            $it = getFactory()->getObject('ObjectsListWidget')->getByRef('Caption', get_class($object));
            if ( $it->getId() != '' ) {
                $widget_it = getFactory()->getObject($it->get('ReferenceName'))->getExact($it->getId());
                if ( $widget_it->getId() != '' ) {
                    $url = $widget_it->getUrl(strtolower(get_class($object)).'='.join(',',$ids).'&clickedonform');
                    echo '<a class="dashed embedded-add-button" style="margin-left:20px;" target="'.$target.'" href="'.$url.'" tabindex="-1">';
                        echo $this->getListItemsTitle();
                    echo '</a>';
                }
            }

            if ( $object instanceof WikiPage ) {
                $url = $object->getPageVersions().'page='.join(',',$ids);
                echo '<a class="dashed embedded-add-button" style="margin-left:20px;" target="'.$target.'" href="'.$url.'" tabindex="-1">';
                    echo text(2242);
                echo '</a>';
            }
        }
    }
}