<?php
include_once SERVER_ROOT_PATH."pm/methods/MarkChangesAsReadWebMethod.php";

class WhatsNewPageList extends PMPageList
{
    function extendModel()
    {
        parent::extendModel();
        $this->getObject()->setAttributeCaption('Content', text(2460));
    }

    function getSorts()
    {
        $sorts = parent::getSorts();

        foreach( $sorts as $key => $sort ) {
            $sorts[$key] = new SortChangeLogRecentClause();
        }

        return $sorts;
    }

	function drawCell( $object_it, $attr )
	{
		switch( $attr )
		{
		    case 'Content':
                echo '<i class="'.$object_it->getIcon().' hidden-print" style="margin-right: 10px;"></i>';

                $anchor_it = $object_it->getObjectIt();
                if ( $anchor_it->getId() != '' )
                {
                    $uid = new ObjectUID;
                    if ( strpos($object_it->get('Caption'), $uid->getObjectUid($anchor_it)) === false ) {
                        if ( $uid->hasUid( $anchor_it ) ) {
                        }
                        else {
                            echo $anchor_it->object->getDisplayName().': ';
                        }
                    }
                }
                else if ($object_it->get('EntityRefName') == 'cms_ExternalUser') {
                    echo text(1360) . ': ';
                }
                else if ($object_it->get('EntityRefName') == 'pm_ChangeRequest') {
                }
                else {
                    echo $anchor_it->object->getDisplayName().': ';
                }

                parent::drawCell( $object_it, 'Caption' );

                if ( strpos($object_it->get('ChangeKind'), 'commented') !== false ) {
                    if ( $object_it->get('Content') != '' ) {
                        echo '<br/>'.$object_it->getHtmlDecoded('Content');
                    }
                    echo $this->getRenderView()->render('core/CommentsIcon.php', array (
                        'object_it' => $anchor_it,
                        'redirect' => 'donothing'
                    ));
                }

                break;
                echo '<br/>';

                if ( strpos($object_it->get('Content'), '[url') !== false && $anchor_it->object instanceof WikiPage) {
                    echo '<br/>'.str_replace('%1', $anchor_it->getHistoryUrl().'&start='.$object_it->getDateTimeFormat('RecordModified'), text(2319));
                }
                else if ( $object_it->get('Content') != '' ) {
                    echo '<br/>'.$object_it->getHtmlDecoded('Content');
                }
			    if ( strpos($object_it->get('ChangeKind'), 'commented') !== false ) {
				    echo $this->getRenderView()->render('core/CommentsIcon.php', array (
							'object_it' => $anchor_it,
							'redirect' => 'donothing'
					));
			    }			    
			    
			    break;
				
			default:
				parent::drawCell( $object_it, $attr );			
		}
	}

	function getColumnWidth( $attr ) 
	{
		if ( $attr == 'RecordModified' ) 
		{
			return "120";
		}

		return '';
	}

	function getGroupFields() {
        if ( getSession()->getProjectIt()->get('LinkedProject') != '' ) {
            return array('Project');
        }
        else {
            return array();
        }
    }
	
	function getColumnFields() {
		return array('Content', 'RecordModified', 'Project');
	}

	function getItemActions($column_name, $object_it)
    {
		$actions = array();

		$anchor_it = $object_it->getObjectIt();
		if ( $anchor_it->getId() != '' )
		{
            $actions[] = array(
                'name' => translate('Открыть'),
                'url' => $anchor_it->getViewUrl()
            );

		    $method = new ObjectModifyWebMethod($anchor_it);
		    if ( $method->hasAccess() ) {
                $actions[] = array();
		        $actions[] = array(
		            'name' => translate('Изменения'),
                    'url' => $method->getJSCall(array(
                        'tab' => 'pmlastchangessection'
                    ))
                );
            }
        }

        $method = new MarkChangesAsReadWebMethod();
        $method->setRedirectUrl('function(){window.location.reload();}');
        $actions[] = array();
		$actions[] = array(
		    'name' => text(2463),
            'url' => $method->getJSCall(array(
                'objects' => $object_it->getId()
            ))
        );

		return $actions;
	}
}
