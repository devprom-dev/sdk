<?php
include_once SERVER_ROOT_PATH . "pm/views/ui/PMDetailsList.php";

class ProjectLogDetailsList extends PMDetailsList
{
    function extendModel()
    {
        foreach( $this->getObject()->getAttributes() as $attribute => $info ) {
            if ( $attribute == 'Content' ) continue;
            $this->getObject()->setAttributeVisible($attribute, false);
        }
        parent::extendModel();
    }

    protected function getPersisters( $object, $sorts ) {
        return array();
    }

    function drawCell( $object_it, $attr )
	{
		echo '<ul class="nav">';
			echo '<li class="nav-cell nav-left">';
				echo $this->getRenderView()->render('core/UserPictureMini.php', array (
					'id' => $object_it->get('SystemUser'),
					'image' => 'userpics-mini',
					'class' => 'user-mini',
					'title' => $object_it->getRef('SystemUser')->getDisplayName()
				));
			echo '</li>';
			echo '<li class="nav-cell" style="width:100%;">';
				echo '<div class="nav-date">';
					if ( $_REQUEST['action'] != 'commented' ) {
						echo '<i class="'.$object_it->getIcon().' hidden-print" style="margin-right: 10px;"></i>';
					}
					echo $object_it->getDateFormattedShort('RecordCreated').', '.$object_it->getTimeFormat('RecordCreated');

                    $method = new UndoWebMethod($object_it->get('Transaction'), $object_it->get('ProjectCodeName'));
                    if ( $method->hasAccess() && $this->last_transaction != $object_it->get('Transaction') ) {
                        echo '<a class="btn btn-info btn-xs pull-right" href="javascript: '.$method->getJSCall().'">'.$method->getCaption().'</a>';
                        $this->last_transaction = $object_it->get('Transaction');
                    }
                echo '</div>';

                $field = new FieldWYSIWYG();
                $field->setObjectIt( $object_it );
                $field->setValue( $object_it->get('Content') );
				echo '<div>';
					$anchor_it = $object_it->getObjectIt();
					if ( strpos($object_it->get('ChangeKind'), 'commented') !== false ) {

                        if ( $this->getUidService()->hasUid($anchor_it) ) {
                            $this->getUidService()->drawUidIcon($anchor_it);
                        }

                        $field->drawReadonly();

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
                        }
					}
					else {
						if ( $anchor_it->getId() != '' )
						{
							$uid = new ObjectUID;
							if ( strpos($object_it->get('Caption'), $uid->getObjectUid($anchor_it)) === false ) {
								if ( $uid->hasUid( $anchor_it ) ) {
									$uid->drawUidIcon( $anchor_it );
									echo ' ';
								}
								else {
									echo $anchor_it->object->getDisplayName().': ';
								}
							}
						}
						else {
							echo $anchor_it->object->getDisplayName().': ';
						}
						parent::drawCell( $object_it, 'Caption' );
                        $field->drawReadonly();
					}
				echo '</div>';
			echo '</li>';
		echo '</ul>';
	}

	function render($view, $parms)
    {
        parent::render($view, $parms);

        $report_it = getFactory()->getObject('PMReport')->getExact(
            $_REQUEST['action'] != 'commented' ? 'project-log' : 'discussions'
        );
        if ( $report_it->getId() != '' ) {
            echo '<div class="details-more">';
                echo '<a href="'.$report_it->getUrl().'">'.text(2323).'</a>';
            echo '</div>';
        }
    }

    function getNoItemsMessage() {
        return '';
    }
}
