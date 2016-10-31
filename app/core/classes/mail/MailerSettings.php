<?php
include "persisters/MailerSettingsPersister.php";
include "MailerSettingsRegistry.php";

class MailerSettings extends Metaobject
{
	public function __construct()
	{
		parent::__construct('entity', new MailerSettingsRegistry($this));
		 
		foreach( $this->getAttributes() as $attribute => $data ) {
		 	$this->setAttributeVisible($attribute, false);
		}
		 
		$this->addAttribute('MailServer', 'VARCHAR', text(1701), true, false );
		$this->addAttribute('MailServerPort', 'INTEGER', text(1704), true, false, ' ' );
		$this->addAttribute('Pop3Server', 'VARCHAR', text(2109), true, false );
        $this->addAttribute('Pop3ServerType', 'REF_co_MailboxProviderId', text(2225), true, false );
		$this->addAttribute('Pop3ServerPort', 'INTEGER', text(2111), true, false, text(2110) );
		$this->addAttribute('MailServerEncryption', 'REF_SmtpEncryptionModeId', text(1707), true, false, ' ' );
		$this->addAttribute('MailServerUser', 'VARCHAR', text(1702), true, false );
		$this->addAttribute('MailServerPassword', 'PASSWORD', text(1703), true, false, ' ' );
		 
		$settings = getFactory()->getObject('cms_SystemSettings');
		 
		$this->addAttribute('AdminEmail', 'VARCHAR', $settings->getAttributeUserName('AdminEmail'), true, false, $settings->getAttributeDescription('AdminEmail') );
		$this->addAttribute('EmailSender', 'VARCHAR', text(1223), true, false, text(1224));
		$this->addAttribute('MailTestEmail', 'VARCHAR', text(1521), true, false, text(1522) );

		$this->addPersister( new MailerSettingsPersister() );
	}

	function getVpds() {
		return array();
	}
}