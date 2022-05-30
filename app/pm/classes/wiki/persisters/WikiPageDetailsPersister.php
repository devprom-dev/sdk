<?php

class WikiPageDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$objectPK = $this->getPK($alias);

		$columns = array(
		    " CONCAT((SELECT IFNULL(CONCAT(MAX(t2.Caption), ' / '),'') 
		       FROM WikiPage t2 
		      WHERE ".$alias.".ParentPage = t2.WikiPageId 
		        AND t2.ParentPage IS NOT NULL), ".$alias.".Caption) CaptionLong ",

            " IFNULL(( SELECT 1 FROM WikiPage t2 WHERE t2.ParentPage = ".$objectPK." LIMIT 1), 0) TotalCount ",

            " (SELECT COUNT(1) 
                 FROM WikiPageTrace tr 
                WHERE tr.TargetPage = ".$objectPK." AND tr.IsActual = 'N' 
                  AND IFNULL(tr.Baseline, 0) < 1) + 
                  (SELECT COUNT(1) FROM pm_FunctionTrace tr WHERE tr.ObjectId = ".$objectPK." AND tr.IsActual = 'N') Suspected ",

            " (SELECT i.UID FROM WikiPage i WHERE i.WikiPageId = t.Includes) IncludesUID ",

            " (SELECT i.State FROM WikiPage i WHERE i.WikiPageId = t.Includes) IncludesState "
		);

        if ( in_array('PageType', array_keys($this->getObject()->getAttributes())) ) {
            $columns[] = " (SELECT i.IsNoIdentity FROM WikiPageType i WHERE i.WikiPageTypeId = t.PageType) IsNoIdentity ";
        }

 		return $columns;
 	}

 	function map( & $parms )
    {
        if ( $parms['ParentPage'] != '' ) {
            if ( is_numeric($parms['ParentPage']) ) {
                $parms['IsDocument'] = 0;
                return;
            }

            if ( preg_match('/\[([^\]]+)\]/', $parms['ParentPage'], $matches) ) {
                $uid = new ObjectUID;
                $objectIt = $uid->getObjectIt($matches[1]);
                if ( $objectIt->getId() != '' ) {
                    $parms['ParentPage'] = $objectIt->getId();
                    $parms['IsDocument'] = 0;
                    return;
                }
            }

            $objectIt = $this->getObject()->getByRef('Caption', $parms['ParentPage']);
            if ( $objectIt->getId() == '' ) {
                $parentParms = array (
                    'Caption' => $parms['ParentPage'],
                    'IsDocument' => 1
                );
                if ( $parms['Project'] != '' ) {
                    $projectIt = getFactory()->getObject('Project')->getExact($parms['Project']);
                    $parentParms['Project'] = $projectIt->getId();
                    $parentParms['VPD'] = $projectIt->get('VPD');
                }
                $parms['ParentPage'] = getFactory()->createEntity($this->getObject(), $parentParms)->getId();
            }
            else {
                $parms['ParentPage'] = $objectIt->getId();
            }
            $parms['IsDocument'] = 0;
        }
    }

 	function IsPersisterImportant() {
        return true;
    }
}
