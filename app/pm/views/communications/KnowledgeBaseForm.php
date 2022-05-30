<?php
include_once SERVER_ROOT_PATH.'pm/methods/WikiExportOptionsWebMethod.php';
include_once SERVER_ROOT_PATH."pm/views/wiki/PMWikiForm.php";

class KnowledgeBaseForm extends PMWikiForm
{
	function getAppendActionName()
	{
		return translate('Статья');
	}

	function IsAttributeVisible( $attr )
	{
	    $object_it = $this->getObjectIt();
	
	    switch( $attr )
	    {
	        case 'Template':
	            return !is_object($object_it);
	
	        default:
	            return parent::IsAttributeVisible( $attr );
	    }
	}

	function IsAttributeEditable($attr_name)
	{
		$object_it = $this->getObjectIt();
		switch($attr_name) {
			case 'Caption':
				if ( $this->getReviewMode() && is_object($object_it) && $object_it->get('ParentPage') == '' ) {
					return false;
				}
				break;
		}
		return parent::IsAttributeEditable($attr_name);
	}

	function getNewRelatedActions()
    {
        $actions = parent::getNewRelatedActions();

        $method = new ObjectCreateNewWebMethod($this->getObject());
        if ( $method->hasAccess() ) {
            $method->setVpd($this->getObjectIt()->get('VPD'));
            $actions['import'] = array(
                'name' => translate('Импортировать'),
                'url' => $method->getJSCall(array('view' => 'importdoc', 'ParentPage' => $this->getObjectIt()->getId()), translate('Импорт'))
            );
            $actions[] = array();
        }

        return $actions;
    }

    function getFieldValue($field)
    {
        switch( $field ) {
            case 'Content':
                if ( $_REQUEST['Request'] != '' ) {
                    $value = $this->buildReleaseNotesContent($_REQUEST['Request']);
                    if ( $value != '' ) return $value;
                }
                return parent::getFieldValue($field);
            default:
                return parent::getFieldValue($field);
        }
    }

    function buildReleaseNotesContent( $ids )
    {
        $ids = TextUtils::parseIds($ids);
        if ( count($ids) < 1 ) return '';

        $request = getFactory()->getObject('Request');
        $items = array(
            '' => array(
                'name' => $request->getDisplayName(),
                'items' => array()
            )
        );
        $typeIt = getFactory()->getObject('RequestType')->getAll();
        while( !$typeIt->end() ) {
            $items[$typeIt->getId()] = array(
                'name' => $typeIt->getDisplayName(),
                'items' => array()
            );
            $typeIt->moveNext();
        }

        $requestIt = $request->getRegistry()->Query(
            array(
                new FilterInPredicate($ids)
            )
        );
        $uid = new ObjectUID();
        while( !$requestIt->end() ) {
            $items[$requestIt->get('Type')]['items'][] = $uid->getObjectUid($requestIt);
            $requestIt->moveNext();
        }

        $html = '';
        foreach( $items as $typeId => $type ) {
            if ( $type['name'] != '' ) {
                $html .= '<h4>'.$type['name'].'</h4>';
            }
            if ( count($type['items']) > 0 ) {
                $html .= '<ul><li>'.join('</li><li>', $type['items']).'</li></ul>';
            }
        }
        return $html;
    }

    protected function buildExportWebMethod() {
        return new WikiExportOptionsWebMethod();
    }
}