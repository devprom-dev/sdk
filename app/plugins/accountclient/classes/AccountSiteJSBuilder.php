<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ScriptBuilder.php";

class AccountSiteJSBuilder extends ScriptBuilder
{
	private $session = null;
	
	public function __construct( $session )
	{
		$this->session = $session;
	}

    public function build( ScriptRegistry & $object )
    {
 		$language = strtolower($this->session->getLanguage()->getLanguage());
    	$object->addScriptFile(SERVER_ROOT_PATH."plugins/accountclient/resources/js/underi18n.js");
    	$object->addScriptFile(SERVER_ROOT_PATH."plugins/accountclient/resources/js/".$language."/resource.js");
 		$object->addScriptFile(SERVER_ROOT_PATH."plugins/accountclient/resources/js/account-form.js");
    }

    public static function getScriptToBuy()
    {
 		$license_it = getFactory()->getObject('LicenseInstalled')->getAll();

 		$buy_url = '/module/accountclient/proxy?'.http_build_query(
 				array (
 						'LicenseType' => $license_it->get('LicenseType'),
 						'WasLicenseValue' => $license_it->getHtmlDecoded('LicenseValue'),
 						'WasLicenseKey' => $license_it->get('LicenseKey'),
						'LicenseScheme' => $license_it->getScheme(),
 						'InstallationUID' => INSTALLATION_UID,
 						'Email' => getSession()->getUserIt()->get('Email'),
 						'UserName' => IteratorBase::wintoutf8(getSession()->getUserIt()->get('Caption')),
 						'Language' => getSession()->getUserIt()->get('Language'),
 						"Redirect" => urlencode(EnvironmentSettings::getServerUrl().$_SERVER['REQUEST_URI'])
 				)
 			);

 		return "showAccountForm('".$buy_url."')";
    }
}