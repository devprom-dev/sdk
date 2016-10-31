<?php

class LicenseIterator extends OrderedIterator
{
	function valid()
	{
		if ($this->checkV1()) return false;
		return openssl_verify(
			trim($this->getHtmlDecoded('LicenseValue')) . INSTALLATION_UID,
			base64_decode(trim($this->get('LicenseKey'))),
			file_get_contents(SERVER_ROOT_PATH . 'templates/config/license.pub'),
			OPENSSL_ALGO_SHA512) == 1;
	}

	function allowCreate(& $object)
	{
		return true;
	}

	function getName()
	{
		return '';
	}

	function restrictionMessage($license_key = '')
	{
		return '';
	}

	function getOptions()
	{
		if ($this->checkV1()) {
			return array('users' => $this->get('LicenseValue'));
		}
		return json_decode($this->getHtmlDecoded('LicenseValue'), true);
	}

	function getTimestamp()
	{
		$options = $this->getOptions();
		return $options['timestamp'];
	}

	function getLeftDays()
	{
		if ( $this->getTimestamp() == '' ) return '';
		$dt1 = new DateTime($this->getTimestamp());
		$dt2 = new DateTime();
		$interval = $dt2->diff($dt1);
		return ($interval->invert ? -1 : 1) * $interval->days;
	}

	function getUsers()
	{
		$options = $this->getOptions();
		return $options['users'] > 0 ? $options['users'] : 0;
	}

    function getDays()
    {
        $options = $this->getOptions();
        return $options['days'] > 0 ? $options['days'] : 0;
    }

	function checkV1()
	{
		return is_numeric($this->get('LicenseValue')) || $this->get('LicenseValue') == '';
	}

	function getScheme()
	{
		return 2;
	}

	function getSupportIncluded()
	{
		return true;
	}
}
