<?php
include_once SERVER_ROOT_PATH . "pm/views/ui/PMDetailsList.php";

class ProjectLogDetailsList extends PMDetailsList
{
	function setupColumns()
	{
		foreach( $this->getObject()->getAttributes() as $attribute => $info ) {
			if ( $attribute == 'Content' ) continue;
			$this->getObject()->setAttributeVisible($attribute, false);
		}
		parent::setupColumns();
	}

	function drawCell( $object_it, $attr )
	{
		echo '<ul class="nav">';
			echo '<li class="nav-cell nav-left">';
				echo $this->getTable()->getView()->render('core/UserPictureMini.php', array (
					'id' => $object_it->get('SystemUser'),
					'image' => 'userpics-mini',
					'class' => 'user-mini',
					'title' => $object_it->getRef('SystemUser')->getDisplayName()
				));
			echo '</li>';
			echo '<li class="nav-cell">';
				echo '<div class="nav-date">';
					if ( $_REQUEST['action'] != 'commented' ) {
						echo '<i class="'.$object_it->getIcon().' hidden-print" style="margin-right: 10px;"></i>';
					}
					echo $object_it->getDateFormatShort('RecordCreated').', '.$object_it->getTimeFormat('RecordCreated');
				echo '</div>';
				echo '<div>';
					$anchor_it = $object_it->getObjectIt();
					if ( strpos($object_it->get('ChangeKind'), 'commented') !== false ) {

                        if ( $this->getUidService()->hasUid($anchor_it) ) {
                            $this->getUidService()->drawUidIcon($anchor_it);
                        }

						drawMore($object_it, 'Content', 20);
						if ( strpos($object_it->get('ChangeKind'), 'commented') !== false ) {
							echo $this->getTable()->getView()->render('core/CommentsIcon.php', array (
								'object_it' => $anchor_it,
								'redirect' => 'donothing'
							));
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
						drawMore($object_it, 'Caption', 20);
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
}
