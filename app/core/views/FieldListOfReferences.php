<?php

class FieldListOfReferences extends Field
{
	private $object_it = null;
	private $widgets = array();

	function __construct( $object_it, $widgets = array() ) {
		$this->object_it = $object_it;
		$this->widgets = $widgets;
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
            $url = WidgetUrlBuilder::Instance()->buildWidgetUrlIt($this->object_it);
        }

        $html = join('<br/>', $uids);

        $html .= '<div class="embedded_form">';
        if ( $url != '' && count($uids) > 1 ) {
            $text = $this->object_it->count() > count($uids)
                ? str_replace('%1', $this->object_it->count() - count($uids), text(2028))
                : text(2034);
            $html .= '<a class="dashed embedded-add-button" target="_blank" href="'.$url.'">'.$text.'</a>';
        }
        foreach( $this->widgets as $widgetName => $widgetUrl ) {
            if ( $widgetUrl == '' ) continue;
            $html .= '<a class="dashed embedded-add-button" target="_blank" href="'.$widgetUrl.'">'.mb_strtolower($widgetName).'</a>';
        }
        $html .= '</div>';

        return $html;
    }

	function render( $view )
	{
        echo $this->getText();
	}
}