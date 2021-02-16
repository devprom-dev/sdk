<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class ProjectIterator extends OrderedIterator
{
    protected $methodology_it_cache = null;

    public function __wakeup()
    {
        parent::__wakeup();
        $this->methodology_it_cache = null;
        $this->setObject( new Project );
    }

 	function IsPortfolio()
	{
		return $this->get('IsTender') == 'F' && $this->get('LinkedProject') != '';
	}

  	function IsProgram()
	{
		return $this->get('IsTender') == 'Y' && $this->get('LinkedProject') != '';
	}

	function getParentIt()
    {
        if ( !class_exists('ProjectLinksPersister') ) {
            return $this->object->getEmptyIterator();
        }

        $projectIt = $this->object->getRegistryBase()->Query(
            array (
                new FilterInPredicate($this->getId()),
                new ProjectLinksPersister(),
                new ProjectGroupPersister()
            )
        );

        if ( $projectIt->get('Programs') != '' ) {
            return $this->object->getExact(\TextUtils::parseIds($projectIt->get('Programs')));
        }

        $portfolio = getFactory()->getObject('Portfolio');
        if ( $projectIt->get('GroupId') != '' ) {
            $ids = array_map( function($item) {
                            return 10000000 + $item;
                        }, \TextUtils::parseIds($projectIt->get('GroupId')));
            return $portfolio->getExact($ids);
        }

        $portfolioIt = $portfolio->getAll();

        $portfolioIt->moveTo('CodeName', 'my');
        if ( $portfolioIt->getId() != '' ) return $portfolioIt;

        $portfolioIt->moveTo('CodeName', 'all');
        return $portfolioIt;
    }

 	function IsPublic()
 	{
 		return $this->get('IsProjectInfo') == 'Y';
 	}
 	 
 	function IsActive()
 	{
 		return $this->get('IsClosed') != 'Y';
 	}
 	
 	function IsInRussian()
 	{
 		return $this->get('Language') == 1;
 	}
 	
 	function IsInEnglish()
 	{
 		return $this->get('Language') == 2;
 	}

 	function getLeadIt()
 	{
 		return getFactory()->getObject('pm_Participant')->getLeadTeam($this->getId());
 	}
 	
 	function getVotedIt()
 	{
 		return getFactory()->getObject('co_Rating')->getVotedIt($this);
 	}

 	function getBlogId()
 	{
 		return $this->get('Blog');
 	}
 	
 	function getBlogIt()
 	{
 		return $this->getRef('Blog');
 	}

 	function getMethodologyIt()
 	{
 		if ( isset($this->methodology_it_cache[$this->getId()]) ) return $this->methodology_it_cache[$this->getId()];
		return $this->methodology_it_cache[$this->getId()] = $this->buildMethodologyIt();
 	}

 	function buildMethodologyIt()
    {
        $methodology = getFactory()->getObject('Methodology');
        if ( $this->getId() < 1 ) {
            $data = array();
            foreach( $methodology->getAttributes() as $attribute => $info ) {
                if ( $methodology->getAttributeType($attribute)  == 'char' ) {
                    $data[$attribute] = 'Y';
                }
            }
            return $methodology->createCachedIterator(array($data));
        }
        return $methodology->getRegistry()->Query(
            array( new FilterAttributePredicate('Project', $this->getId()) )
        );
    }

    function setMethodologyIt( $object_it ) {
        $this->methodology_it_cache[$this->getId()] = $object_it;
    }

 	function invalidateCache()
 	{
 		$this->methodology_it_cache = array();
 	}
 	
	function getTeamVelocity()
	{
		if ( $this->getMethodologyIt()->HasPlanning() )
		{
			$sql = " SELECT IFNULL(AVG(m2.MetricValue), 0) Velocity " .
			 	   "   FROM pm_IterationMetric m2, pm_Release r " .
			 	   "  WHERE m2.Iteration = r.pm_ReleaseId " .
			 	   "	AND m2.Metric = 'Velocity' ".
			 	   "    AND r.Project = ".$this->getId().
			 	   "    AND m2.MetricValue > 0 ".
			 	   "  ORDER BY r.RecordCreated DESC LIMIT 3";
		}
		else
		{
			$sql = " SELECT IFNULL(AVG(m2.MetricValue), 0) Velocity " .
			 	   "   FROM pm_VersionMetric m2, pm_Version r " .
			 	   "  WHERE m2.Version = r.pm_VersionId " .
			 	   "	AND m2.Metric = 'Velocity' ".
			 	   "    AND r.Project = ".$this->getId().
			 	   "    AND m2.MetricValue > 0 ".
			 	   "  ORDER BY r.RecordCreated DESC LIMIT 3";
		}

		$it = $this->object->createSQLIterator( $sql );
		if ( $it->get('Velocity') == 0 )
		{
			return $this->getPlannedTeamVelocity();
		}
		else
		{
			return round($it->get('Velocity'), 1);
		}
	}
	
	function getVelocityDevider()
	{
	    $velocity = $this->getTeamVelocity();
	    
		$methodology_it = $this->getMethodologyIt(); 
		
		if ( $methodology_it->HasFixedRelease() )
		{
		    $devider = $methodology_it->getReleaseDuration() * $this->getDaysInWeek();
		    
		    if ( $devider > 0 )
		    {
		        $velocity /= $devider;
		    }
		    else
		    {
		        $velocity = 0;
		    }
		}
	    
		return $velocity;
	}
	
	function getPlannedTeamVelocity()
	{
		$sql = 'SELECT ROUND(SUM(r.Capacity)) Capacity '.
			   '  FROM pm_Participant p, pm_ParticipantRole r, pm_ProjectRole l '.
			   ' WHERE r.Project = '.$this->getId().
			   '   AND r.Participant = p.pm_ParticipantId '.
			   "   AND p.IsActive = 'Y' " .
			   "   AND r.ProjectRole = l.pm_ProjectRoleId" .
			   "   AND l.ReferenceName NOT IN ('lead', 'client') ";
			   
		$it = $this->object->createSQLIterator( $sql );
		
		return round($it->get('Capacity'), 1);
	}
	
	function getTotalWorkload()
	{
		$request = getFactory()->getObject('pm_ChangeRequest');
		$request->addFilter( new FilterAttributePredicate('Project', $this->getId()) );
		
		return array_shift(
				$this->getMethodologyIt()->getEstimationStrategy()->getEstimation( $request, 'Estimation')
		); 
	}
	
	function getRemoveKey()
	{
		return md5(INSTALLATION_UID.$this->get('CodeName').date('%Y-%m-%d-%H').$this->getId().INSTALLATION_UID);
	}
	
	function getPublicKey()
	{
		return md5(INSTALLATION_UID.$this->getId().'{project-key-salt}');
	}

	function getRating()
	{
		return $this->get('Rating');
	}
	
	function getDaysInWeek()
	{
		if ( $this->get('DaysInWeek') < 1 )
		{
			return 5;
		}
		else
		{
			return $this->get('DaysInWeek');
		}
	}
}
