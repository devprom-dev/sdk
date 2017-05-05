<?php

class MethodologyIterator extends OrderedIterator
{
    function __wakeup()
    {
        parent::__wakeup();
        $this->setObject( new Methodology() );
    }

    function get( $attr )
 	{
 		global $project_it;
 	
 		if ( is_object($project_it) && $attr == 'Caption' )
 		{
	 		return $project_it->getDisplayName();
 		}
 		
 		return parent::get( $attr );
 	}

 	function HasTasks()
 	{
 		return $this->get('IsTasks') == 'Y';
 	}
 	
 	function HasPlanning()
 	{
 		return $this->get('IsPlanningUsed') == 'Y';
 	}
 	
	function UserInProject() {
		return $this->get('IsUserInProject') == 'Y';
	}
	function HasFixedRelease() {
		return $this->get('IsFixedRelease') == 'Y';
	}
	function getReleaseDuration() {
		return $this->get('ReleaseDuration');
	}
	function getAvgDurationOfTaskVerification() {
		return $this->get('VerificationTime') == '' ? 1 : $this->get('VerificationTime');
	}
	
	function getMeasureUnitName() {
		return translate ('ч.');
	}
	
	function HasMeasureUnitDays() {
		return false;
	}
	
	function HasMilestones() {
		return true;
	}
	
	function IsParticipantsTakesTasks() {
		return $this->get('IsParticipantsTakeTasks') == 'Y';
	}

	function HasFeatures() {
		return $this->get('UseFunctionalDecomposition') == 'Y';
	}
	
	function IsUsedDeadlines()
	{
		return $this->get('IsDeadlineUsed') == 'Y';
	}

	function IsReportsRequiredOnActivities()
	{
		return $this->get('IsReportsOnActivities') == 'Y';
	}

	function CustomerAcceptsIssues()
	{
		return $this->get('CustomerAcceptsIssues') == 'Y';
	}
	
	function HasStatistics()
	{
		return true;
	}
	
	function HasReleases()
	{
		return in_array($this->get('IsReleasesUsed'), array('Y', 'I'));
	}
	
	function IsIssueTracking()
	{
		return !$this->HasPlanning();
	}
	
	function IsTimeTracking()
	{
		return $this->IsReportsRequiredOnActivities();
	}

	function HasVelocity()
	{
		return $this->IsAgile() && ($this->HasPlanning() || $this->HasReleases());
	}
	
	function getEstimationStrategy()
	{
	    $builders = getSession()->getBuilders('EstimationStrategyBuilder');
	    
	    if ( is_array($builders) )
	    {
    	    foreach( $builders  as $builder )
            {
                foreach( $builder->getStrategies() as $strategy )
                {
                    if ( is_a($strategy, $this->get('RequestEstimationRequired')) )
                    {
                        return $strategy;
                    }
                }
            }
	    }
	    
		return new EstimationNoneStrategy();
	}
	
	function RequestEstimationUsed()
	{
		return !$this->getEstimationStrategy() instanceof EstimationNoneStrategy;
	}
	
	function TaskEstimationUsed()
	{
		return $this->HasTasks() && $this->get('TaskEstimationUsed') == 'Y';
	}

	function IsAgile() {
        return $this->get('MetricsType') == 'A';
    }
}