<?php

namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class ModifiedAfterFilterResolver
{
	public function __construct( $modifiedFrom = '', $modifiedTo = '', $createdFrom = '', $createdTo = '' )
	{
        $this->modifiedFrom = $modifiedFrom;
		$this->modifiedTo = $modifiedTo;
		$this->createdFrom = $createdFrom;
		$this->createdTo = $createdTo;
	}

	public function resolve()
	{
		$filters = array();
		if ( $this->modifiedFrom != '' ) {
			$filters[] = new \FilterModifiedAfterPredicate(
                    (new \DateTime($this->modifiedFrom,
                        new \DateTimeZone(\EnvironmentSettings::getUTCOffset().':00')))
                            ->format("Y-m-d H:i:s")
                    );
		}
		if ( $this->modifiedTo != '' ) {
			$filters[] = new \FilterModifiedBeforePredicate($this->modifiedTo);
		}
		if ( $this->createdFrom != '' ) {
			$filters[] = new \FilterSubmittedAfterPredicate($this->createdFrom);
		}
		if ( $this->createdTo != '' ) {
			$filters[] = new \FilterSubmittedBeforePredicate($this->createdTo);
		}
		return $filters;
	}

	private $modifiedFrom = '';
	private $modifiedTo = '';
	private $createdFrom = '';
	private $createdTo = '';
}