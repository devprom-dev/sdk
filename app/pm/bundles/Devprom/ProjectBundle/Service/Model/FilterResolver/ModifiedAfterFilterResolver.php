<?php

namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class ModifiedAfterFilterResolver
{
	public function __construct( $filter = '' ) {
		$this->filter = $filter;
	}

	public function resolve() {
		\EnvironmentSettings::setClientTimeZone('UTC');
		return array(
			new \FilterModifiedAfterPredicate($this->filter)
		);
	}

	private $filter = '';
}