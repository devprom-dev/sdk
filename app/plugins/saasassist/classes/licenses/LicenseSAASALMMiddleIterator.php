<?php

class LicenseSAASALMMiddleIterator extends LicenseSAASALMIterator
{
	function getName()
	{
		return 'Devprom.SaaS (M)';
	}

	protected function getLimitDefault()
	{
		return 30;
	}
}