<?php

class InstallableDummy extends Installable
{
	// checks all required prerequisites
	function check()
	{
		return true;
	}

	// skip install actions
	function skip()
	{
		return false;
	}

	// cleans after the installation script has been executed
	function cleanup()
	{
		// unlink(__FILE__); // remove installation script
	}

	// makes install actions
	function install()
	{
		return true;
	}
}
