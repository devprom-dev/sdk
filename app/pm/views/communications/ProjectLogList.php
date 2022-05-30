<?php

class ProjectLogList extends PMPageList
{
 	var $participant;
 	
 	function __construct( $object )
 	{
 		parent::__construct($object);
		$this->getObject()->setAttributeType( 'Author', 'REF_IssueAuthorId' );
 	}

    protected function getPersisters( $object, $sorts ) {
        return array();
    }

    protected function getShorten() {
         return true;
    }

    function buildItemsHash($registry, $predicates) {
        return '';
    }

	function getSorts()
	{
		$sorts = parent::getSorts();
		
		foreach( $sorts as $key => $sort ) {
			if ( !$sort instanceof SortAttributeClause ) continue;
			if ( in_array($this->getObject()->getAttributeType($sort->getAttributeName()), array('date','datetime')) ) {
				$sorts[$key] = new SortChangeLogRecentClause($sort->getSortType());
			}
		}
		
		return $sorts;
	}

	function drawRefCell( $entity_it, $object_it, $attr ) 
	{
		switch( $attr ) 
		{
			case 'SystemUser':
				if ( $entity_it->getId() == '' ) {
				    if ( $object_it->get('UserName') != '' ) {
                        parent::drawCell( $object_it, 'UserName' );
                    }
                    else {
                        parent::drawCell( $object_it, 'AuthorName' );
                    }
				}
				else {
					parent::drawRefCell( $entity_it, $object_it, $attr );
				}
				break;
				
			default:
				parent::drawRefCell( $entity_it, $object_it, $attr );
		}
	}
	
	function drawCell( $object_it, $attr ) 
	{
		switch( $attr )
		{
		    case 'UserAvatar':
		        
    	    	echo $this->getRenderView()->render('core/UserPicture.php', array (
					'id' => $object_it->get('SystemUser'), 
					'class' => 'user-avatar',
					'title' => $object_it->getRef('SystemUser')->getDisplayName()
				));

				break;
				
		    case 'Content':

                echo '<i class="'.$object_it->getIcon().' hidden-print" style="margin-right: 10px;"></i>';

                $uid = new ObjectUID;
                $anchor_it = $object_it->getObjectIt();
                if ( $anchor_it->getId() != '' && $uid->hasUid( $anchor_it ) )
                {
                    if ( strpos($object_it->get('ChangeKind'), 'deleted') !== false ) {
                        parent::drawCell( $object_it, 'Caption' );
                    }
                    else {
                        $uid->drawUidInCaption($anchor_it);
                    }
                    echo '<br/>';
                }
                else if ($object_it->get('EntityRefName') == 'pm_ChangeRequest') {
                    parent::drawCell( $object_it, 'Caption' );
                }
                else {
                    echo $anchor_it->object->getDisplayName().': ';
                    parent::drawCell( $object_it, 'Caption' );
                }

                $content = $object_it->get('Content');

                if ( $anchor_it->object instanceof WikiPage && $this->getShorten() ) {
                    if ( strpos($content, 'history?object') !== false ) {
                        $url = '\\1';
                        foreach( explode(ChangeLogAggregatePersister::CONTENT_SEPARATOR, $object_it->getHtmlDecoded('ObjectUrl')) as $data ) {
                            if ( strpos($data, 'history') !== false ) {
                                $url = $data;
                                break;
                            }
                        }
                        $content = preg_replace('/\[url=([^\]\s]+)(\s[^\]]+)?\]/i',
                            str_replace('%1', $url, text(2319)),
                            $content);
                    }
                    else {
                        // new way to display a wiki changes
                        $start = date('Y-m-j%20H:i:s',
                            strtotime('-10 seconds',
                                strtotime($object_it->getDateTimeFormat('RecordCreated'))));

                        $history_url = $anchor_it->getHistoryUrl() . "&start={$start}";
                        $lines = preg_split('/reset\s+wysiwyg/i', $content);
                        if ( count($lines) > 1 ) {
                            $historyUrl = '';
                            $content = '';

                            foreach( $lines as $line ) {
                                $parts = explode('wysiwyg-finish', $line);
                                $text = '';
                                if ( $historyUrl == '' ) {
                                    $historyUrl = str_replace('%1', $history_url, text(2319));
                                    $text = $historyUrl;
                                }
                                $content .= '<div class="">' . $text . '</div><span class="' . $parts[1];
                            }
                        }
                    }
                }

                if ( $content != '' )
                {
                    echo '<br/>';
                    $field = new FieldWYSIWYG();
                    $field->setObjectIt( $object_it );
                    $field->setValue( $content );
                    $field->drawReadonly();
                }
			    if ( strpos($object_it->get('ChangeKind'), 'commented') !== false )
			    {
                    $method = new CommentWebMethod($anchor_it);
                    if ( $method->hasAccess() ) {
                        if ( preg_match('/O\-(\d+)\s/i', $content, $matches) ) {
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
				
			case 'RecordModified':
				
			    $group = $this->getGroup();
				
				if ( $group == 'GroupDays' )
				{
					echo $object_it->getTimeFormat('RecordCreated');
				}
				else
				{
					echo $object_it->getDateTimeFormat('RecordCreated');
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

		if($attr == 'Caption') 
		{
			return "30%";
		}

		if($attr == 'UserAvatar') 
		{
			return "1%";
		}

		if($attr == 'SystemUser') 
		{
			return "160";
		}
		
		return '';
	}

	function getGroupFields() 
	{
		return array('ChangeDate', 'SystemUser', 'Project');
	}
	
	function getColumnFields()
	{
		return array('Caption', 'UserAvatar', 'Content', 'SystemUser', 'RecordModified', 'Project', 'ChangeKind');
	}

	function getItemActions($column_name, $object_it)
	{
		$actions = array();

		$method = new UndoWebMethod($object_it->get('Transaction'), $object_it->get('ProjectCodeName'));
		if ( $method->hasAccess() ) {
			$actions[] = array (
				'name' => $method->getCaption(),
				'url' => $method->getJSCall()
			);
		}

		return $actions;
	}

	function IsNeedToSelect() {
		return false;
	}
}
