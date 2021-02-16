<?php
use Devprom\ProjectBundle\Service\Model\ModelService;

class FieldComputed extends Field
{
	private $object_it = null;
	private $attribute = '';

	function __construct( $object_it, $attribute ) {
		$this->object_it = $object_it;
		$this->attribute = $attribute;
	}

	function render( $view )
	{
        $result = ModelService::computeFormula(
            $this->object_it,
            $this->object_it->object->getDefaultAttributeValue($this->attribute)
        );

        $uid = new ObjectUID();
        $lines = array();
        $uids = array();
        $ids = array();
        $vpds = array();
        $className = '';
        $limit = 20;

        foreach( $result as $computedItem ) {
            if ( !is_object($computedItem) ) {
                $lines[] = $computedItem;
            }
            else {
                $className = get_class($computedItem->object);
                if ( count($uids) < $limit ) {
                    $uids[] = $uid->getUidWithCaption(
                        $computedItem, 15, '',
                        $computedItem->get('VPD') != getSession()->getProjectIt()->get('VPD')
                    );
                }
                $ids[] = $computedItem->getId();
                $vpds[] = $computedItem->get('VPD');
            }
        }
		ksort($uids);

		if ( count($ids) > 0 ) {
            $url = WidgetUrlBuilder::Instance()->buildWidgetUrlIds($className, $ids, $vpds);
		}

        $html = '';
        if ( count($lines) > 0 ) {
            $html .= join('<br/>', $lines);
        }
        else {
            $html .= join('<br/>', $uids);
            if ( $url != '' ) {
                $text = count($result) > count($uids)
                    ? str_replace('%1', count($result) - count($uids), text(2028))
                    : text(2034);
                $html .= '<br/><a class="dashed" target="_blank" href="'.$url.'">'.$text.'</a>';
            }
        }

        if ( $this->readOnly() )
        {
            echo '<span class="input-block-level well well-text" style="width:100%;height:auto;word-break: break-all;">';
                echo $html;
            echo '</span>';
        }
        else {
            echo $html;
        }
	}
}