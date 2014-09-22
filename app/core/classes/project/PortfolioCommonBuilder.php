<?php

include_once SERVER_ROOT_PATH."core/classes/project/PortfolioBuilder.php";

class PortfolioCommonBuilder extends PortfolioBuilder
{
    public function build( PortfolioRegistry & $object )
    {
    	$project_ids = join(',', getFactory()->getObject('Project')->getRegistry()->Query(
								     		array (
								     				new ProjectStatePredicate('active'),
								     				new ProjectParticipatePredicate()
								     		)
								     )->idsToArray()
						   );
    	
        $object->addPortfolio(
            array (
                'pm_ProjectId' => 1000000 + getSession()->getUserIt()->getId(),
                'BaseId' => 1000000,
                'Caption' => translate('Мои проекты'),
                'CodeName' => 'my',
            	'IsKnowledgeUsed' => 'Y',
                'LinkedProject' => $project_ids,
            	'RelatedProject' => $project_ids 
            ), 
            function( $portfolio_it )
            {
                return new SessionPortfolioMyProjects($portfolio_it);
            }
        );
    }
}