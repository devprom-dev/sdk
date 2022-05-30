<?php

class MentionedRegistry extends ObjectRegistrySQL
{
 	function getQueryClause(array $parms)
 	{
        $vpd = getFactory()->getObject('ProjectRole')->getVpdValue();

		if ( defined('PERMISSIONS_ENABLED') ) {
			$sql = "
				SELECT LCASE(SUBSTRING_INDEX(t.Caption,' ',1)) entityId,
					   t.Caption,
					   t.pm_ProjectRoleId + 100 OrderNum,
					   (SELECT GROUP_CONCAT(CAST(u.cms_UserId AS CHAR)) FROM cms_User u, pm_Participant p, pm_ParticipantRole pr
						 WHERE p.SystemUser = u.cms_UserId
						   AND pr.Participant = p.pm_ParticipantId
						   AND pr.ProjectRole = t.pm_ProjectRoleId) User,
					   t.VPD,
					   -1 PhotoRow,
					   -1 PhotoColumn
				  FROM pm_ProjectRole t
				 WHERE t.ReferenceName NOT IN ('guest','linkedguest')
				   AND EXISTS (SELECT 1 FROM pm_ParticipantRole r WHERE r.ProjectRole = t.pm_ProjectRoleId)
				   ".getFactory()->getObject('ProjectRole')->getVpdPredicate('t');

            if ( getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Participant')) ) {
                $sql .= " UNION
                        SELECT REPLACE(u.Caption,' ',''),
                               u.Caption,
                               u.cms_UserId + 1000000,
                               u.cms_UserId,
                               t.VPD,
                               FLOOR(u.cms_UserId / 1820) PhotoRow,
                               u.cms_UserId - FLOOR(u.cms_UserId / 1820) * 1820 - 1 PhotoColumn
                          FROM pm_Participant t, cms_User u
                         WHERE t.SystemUser = u.cms_UserId " . getFactory()->getObject('pm_Participant')->getVpdPredicate('t');
            }
		}
		else {
			$sql = "
				SELECT '".translate('все')."' entityId,
					   '".translate('Все')."' Caption,
					   0 OrderNum,
					   (SELECT GROUP_CONCAT(CAST(u.cms_UserId AS CHAR))  FROM cms_User u
					     WHERE NOT EXISTS (SELECT 1 FROM cms_BlackList i WHERE i.SystemUser = u.cms_UserId)) User,
					   '{$vpd}' VPD,
					   -1 PhotoRow,
					   -1 PhotoColumn
				  FROM co_UserGroup t";

			$sql .= " UNION
				SELECT LCASE(SUBSTRING_INDEX(t.Caption,' ',1)),
					   t.Caption,
					   t.co_UserGroupId + 100,
					   (SELECT GROUP_CONCAT(CAST(l.SystemUser AS CHAR)) FROM co_UserGroupLink l
						 WHERE NOT EXISTS (SELECT 1 FROM cms_BlackList i WHERE i.SystemUser = l.SystemUser)
						   AND l.UserGroup = t.co_UserGroupId) User,
					   '{$vpd}' VPD,
					   -1 PhotoRow,
					   -1 PhotoColumn
				  FROM co_UserGroup t";

			$sql .= " UNION
				SELECT REPLACE(u.Caption,' ',''),
					   u.Caption,
					   u.cms_UserId + 1000000,
					   u.cms_UserId,
					   '{$vpd}',
					   FLOOR(u.cms_UserId / 1820) PhotoRow,
					   u.cms_UserId - FLOOR(u.cms_UserId / 1820) * 1820 - 1 PhotoColumn
				  FROM cms_User u
				 WHERE NOT EXISTS (SELECT 1 FROM cms_BlackList i WHERE i.SystemUser = u.cms_UserId)";
		}

        $attributesObject = $this->getObject()->getAttributesObject();
        if ( is_object($attributesObject) ) {
            $attributeIndex = 0;
            foreach( $attributesObject->getAttributesByEntity('cms_User') as $attribute ) {
                $attributeIndex++;
                $attributeTitle = $attributesObject->getAttributeUserName($attribute);
                $sql .= " UNION
                    SELECT REPLACE(LCASE('{$attributeTitle}'),' ',''), 
                           '{$attributeTitle}',
                           {$attributeIndex},
                           NULL,
                           '{$vpd}',
                           -1,
                           -1 ";
            }
        }

 	    return "(".$sql.")";
 	}
}