<?php

class LicenseSAASALMMiddleIterator extends LicenseSAASALMIterator
{
	function getName()
	{
		return 'Devprom.SaaS (M)';
	}

	protected function getLimit()
	{
		return 30;
	}
}