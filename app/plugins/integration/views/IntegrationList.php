<?php

class IntegrationList extends PMPageList
{
    function getRowColor( $object_it, $attr_it )
    {
        if ( $object_it->get('IsActive') == 'Y' ) {
            return 'black';
        }
        else {
            return 'silver';
        }
    }

    function extendModel()
    {
        foreach( array('Log','ItemsQueue','MappingSettings') as $attribute ) {
            $this->getObject()->setAttributeVisible($attribute, false);
        }
        parent::extendModel();
    }

    function getGroupFields() {
        return array();
    }

    function drawCell($object_it, $attr)
    {
        switch( $attr ) {
            case 'StatusText':
                if ( $object_it->get('IsActive') == 'N' ) {
                    echo '<span class="label">'.translate('Отключено').'</span>';
                    break;
                }

                if ( $object_it->get($attr) == '' ) {
                    echo '<span class="label label-success">Ok</span>';
                }
                else {
                    echo '<span class="label label-important">'.nl2br($object_it->getHtmlDecoded($attr)).'</span>';
                }

                $method = new ObjectModifyWebMethod($object_it);
                echo '<span style="margin-left:26px;">';
                    echo '<i class="icon-file"></i> ';
                    echo '<a class="dashed" onclick="'.$method->getJSCall(array('tab'=>'additional')).'">'.text('integration15').'</a>';
                echo '</span>';
                break;

            default:
                return parent::drawCell($object_it, $attr);
        }
    }
}
