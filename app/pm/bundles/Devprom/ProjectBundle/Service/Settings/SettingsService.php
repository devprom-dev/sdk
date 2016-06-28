<?php

namespace Devprom\ProjectBundle\Service\Settings;

interface SettingsService
{
	function reset();
	function resetToDefault();
	function makeDefault();
}