<?php
include_once SERVER_ROOT_PATH."pm/methods/MarkChangesAsReadWebMethod.php";

class WhatsNewPageList extends PMPageList
{
    function extendModel()
    {
        parent::extendModel();
        $this->getObject()->setAttributeCaption('Content', text(2460));
    }

    protected function getPersisters( $object, $sorts ) {
        return array();
    }

    function getSorts()
    {
        $sorts = parent::getSorts();

        foreach( $sorts as $key => $sort ) {
            if ( $key == '_group' ) {
                $sorts[$key] = new SortChangeLogRecentProjectClause();
                continue;
            }
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
                    $uid->drawUidInCaption($anchor_it);
                }
                else {
                    if ($object_it->get('EntityRefName') == 'pm_ChangeRequest') {
                    }
                    else {
                        echo $anchor_it->object->getDisplayName().': ';
                    }
                    parent::drawCell( $object_it, 'Caption' );
                }

                if ( strpos($object_it->get('ChangeKind'), 'commented') !== false ) {
                    if ( $object_it->get('Content') != '' ) {
                        echo '<br/>'.$object_it->getHtmlDecoded('Content');
                    }

                    $method = new CommentWebMethod($anchor_it);
                    if ( $method->hasAccess() ) {
                        if ( preg_match('/O\-(\d+)\s/i', $object_it->get('Content'), $matches) ) {
                            $commentId = $matches[1];
                        }
                        if ( $commentId > 0 ) {
                            echo $this->getRenderView()->render('core/CommentsReplyIcon.php', array (
                                'objectIt' => $anchor_it,
                                'commentId' => $commentId
                            ));
                        }
                        else {
                            echo $this->getRenderView()->render('core/CommentsIcon.php', array (
                                'object_it' => $anchor_it,
                                'redirect' => 'donothing'
                            ));
                        }
                    }

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
            return array('Project', 'SystemUser');
        }
        else {
            return array('SystemUser');
        }
    }

    function getColumnFields() {
		return array('Content', 'RecordModified', 'Project', 'SystemUser');
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
        $actions[] = array();
		$actions[] = array(
		    'name' => text(2463),
            'url' => $method->getJSCall(array(
                'objects' => $object_it->getId()
            ))
        );

		return $actions;
	}

    function getCollapseAttributes() {
        return array('Content');
    }
}
