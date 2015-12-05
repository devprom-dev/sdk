<?php

class LicenseSAASALM20Iterator extends LicenseSAASALMIterator
{
	function getName()
	{
		return 'Devprom.SaaS (20)';
	}

	protected function getLimit()
	{
		return 20;
	}
}