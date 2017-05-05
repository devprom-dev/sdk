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
            }
        }

		ksort($uids);
		if ( count($ids) > 0 ) {
			$widget_it = getFactory()->getObject('ObjectsListWidget')->getByRef('Caption', $className);
			if ( $widget_it->getId() != '' ) {
				$url = getFactory()->getObject($widget_it->get('ReferenceName'))->getExact($widget_it->getId())->getUrl(
					strtolower($className).'='.join(',',$ids)
				);
			}
		}

		echo '<div class="input-block-level well well-text">';
            if ( count($lines) > 0 ) {
                echo join('<br/>', $lines);
            }
            else {
                echo join('<br/>', $uids);
                if ( $url != '' ) {
                    $text = count($result) > count($uids)
                        ? str_replace('%1', count($result) - count($uids), text(2028))
                        : text(2034);
                    echo '<br/><a class="dashed" target="_blank" href="'.$url.'">'.$text.'</a>';
                }
            }
		echo '</div>';
	}
}