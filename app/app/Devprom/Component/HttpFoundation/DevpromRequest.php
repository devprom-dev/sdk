<?php

namespace Devprom\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\Request;

class DevpromRequest extends Request
{
	protected function prepareBaseUrl()
    {
    	return parent::prepareBaseUrl();
    }
}