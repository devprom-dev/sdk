<?php

class FieldListOfReferences extends Field
{
	private $object_it = null;

	function __construct( $object_it ) {
		$this->object_it = $object_it;
	}

	function getText()
    {
        $uid = new ObjectUID();
        $uids = array();
        $ids = array();
        $limit = 20;
        $items = 0;

        $this->object_it->moveFirst();
        while( !$this->object_it->end() && $items++ < $limit )
        {
            $uids[] = $uid->getUidWithCaption(
                $this->object_it, 15, '',
                $this->object_it->get('VPD') != getSession()->getProjectIt()->get('VPD')
            );
            $ids[] = $this->object_it->getId();
            $this->object_it->moveNext();
        }
        ksort($uids);

        if ( count($ids) > 0 ) {
            $widget_it = getFactory()->getObject('ObjectsListWidget')->getByRef('Caption', get_class($this->object_it->object));
            if ( $widget_it->getId() != '' ) {
                $url = getFactory()->getObject($widget_it->get('ReferenceName'))->getExact($widget_it->getId())->getUrl(
                    strtolower(get_class($this->object_it->object)).'='.join(',',$ids)
                );
            }
        }

        $html = join('<br/>', $uids);
        if ( $url != '' ) {
            $text = $this->object_it->count() > count($uids)
                ? str_replace('%1', $this->object_it->count() - count($uids), text(2028))
                : text(2034);
            $html .= '<br/><a class="dashed" target="_blank" href="'.$url.'">'.$text.'</a>';
        }
        return $html;
    }

	function render( $view )
	{
		echo '<div class="input-block-level well well-text">';
            echo $this->getText();
		echo '</div>';
	}
}