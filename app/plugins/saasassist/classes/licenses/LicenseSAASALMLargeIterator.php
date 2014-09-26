<?php

class LicenseSAASALMLargeIterator extends LicenseSAASALMIterator
{
	function getName()
	{
		return 'Devprom.SaaS (L)';
	}
	
	protected function getLimit()
	{
		return 100;
	}
}