<?php
include_once SERVER_ROOT_PATH."core/classes/sprites/UserPicSpritesGenerator.php";

class ProfileManage extends CommandForm
{
 	function validate()
 	{
		// check authorization was successfull
		if ( getSession()->getUserIt()->getId() != $_REQUEST['object_id'] ) {
			return false;
		}

		// proceeds with validation
		$this->checkRequired( array('Caption', 'Login') );
		
		return true;
 	}
 	
	function modify( $user_id )
	{
        getFactory()->getEventsManager()->delayNotifications();
		$userIt = getFactory()->getObject('cms_User')->getExact($user_id);

		if ( $_REQUEST['Email'] != '' ) {
            $this->checkUniqueExcept( $userIt, $this->Utf8ToWin('Email') );
        }
		$this->checkUniqueExcept( $userIt, $this->Utf8ToWin('Login') );

		$_REQUEST['Caption'] = $userIt->utf8towin($_REQUEST['Caption']);

        $userIt->object->modify_parms($userIt->getId(),
             array( 'Caption' => $_REQUEST['Caption'],
				    'Email' => $_REQUEST['Email'],
				    'Login' => $_REQUEST['Login'],
				    'ICQ' => $_REQUEST['ICQ'],
				    'Skype' => $_REQUEST['Skype'],
				    'Phone' => $_REQUEST['Phone'],
				    'Language' => $_REQUEST['Language'],
				    'Skills' => $_REQUEST['Skills'],
				    'Tools' => $_REQUEST['Tools'],
                    'NotificationTrackingType' => $_REQUEST['NotificationTrackingType'],
                    'NotificationEmailType' => $_REQUEST['NotificationEmailType'],
                    'SendDeadlinesReport' => $_REQUEST['SendDeadlinesReport'] == 'on' ? 'Y' : 'N'
				 )
			);

		$participantParms = array();
		if ( $userIt->get('NotificationTrackingType') != $_REQUEST['NotificationTrackingType'] ) {
            $participantParms['NotificationTrackingType'] = $_REQUEST['NotificationTrackingType'];
        }
        if ( $userIt->get('NotificationEmailType') != $_REQUEST['NotificationEmailType'] ) {
            $participantParms['NotificationEmailType'] = $_REQUEST['NotificationEmailType'];
        }

        if ( count($participantParms) > 0 ) {
            $participant = getFactory()->getObject('pm_Participant');
            $participantIt = $participant->getByRef('SystemUser', $userIt->getId());
            while( !$participantIt->end() ) {
                $participant->getRegistry()->Store($participantIt, $participantParms);
                $participantIt->moveNext();
            }
        }

        getFactory()->getEventsManager()->releaseNotifications();

        $generator = new UserPicSpritesGenerator();
        $generator->storeSprites();
        getFactory()->getCacheService()->invalidate();

		$this->replySuccess( 
			$this->getResultDescription( 1001 ) );
	}

	function getResultDescription( $result )
	{
		switch($result)
		{
			case 1001:
				return text(187);

			default:
				return parent::getResultDescription( $result );
		}
	}
}
 