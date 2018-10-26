<?php

class UserParticipanceTypeRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        $project_it = getSession()->getProjectIt();

        $data = array (
            array (
                'entityId' => 1,
                'ReferenceName' => 'participant',
                'Caption' => $project_it->IsPortfolio()
                                ? text(2533)
                                : ($project_it->IsProgram()
                                    ? text(2534)
                                    : translate('Участники проекта') )
            )
        );

        if ( $project_it->IsProgram() ) {
            $data[] = array ( 'entityId' => 2, 'ReferenceName' => 'linked', 'Caption' => text('permissions7') );
        }
        else if ( $project_it->get('LinkedProject') != '' ) {
            $parent_it = $project_it->getParentIt();
            if ( $parent_it->IsProgram() ) {
                $data[] = array ( 'entityId' => 2, 'ReferenceName' => 'linked', 'Caption' => text('permissions7') );
            }
        }
        
        $data[] = array ( 'entityId' => 3, 'ReferenceName' => 'guest', 'Caption' => text(1370) ); 
        
        return $this->createIterator($data);
    }
}