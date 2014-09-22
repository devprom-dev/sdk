<?php

class WikiTypeRegistry extends ObjectRegistrySQL
{
	const KnowledgeBase = 1;
	const Requirement = 2;
	const TestScenario = 3;
	const HelpPage = 4;
	
 	function createSQLIterator( $sql )
 	{
 		return $this->createIterator(array(
 				array (
 						'entityId' => WikiTypeRegistry::KnowledgeBase,
 						'ReferenceName' => 'KnowledgeBase',
 						'ClassName' => 'ProjectPage'
 				),
 				array (
 						'entityId' => WikiTypeRegistry::Requirement,
 						'ReferenceName' => 'Requirements',
 						'ClassName' => 'Requirement'
 				),
 				array (
 						'entityId' => WikiTypeRegistry::TestScenario,
 						'ReferenceName' => 'TestScenario',
 						'ClassName' => 'TestScenario'
 				),
 				array (
 						'entityId' => WikiTypeRegistry::HelpPage,
 						'ReferenceName' => 'HelpPage',
 						'ClassName' => 'HelpPage'
 				)
 		));  
 	}
}