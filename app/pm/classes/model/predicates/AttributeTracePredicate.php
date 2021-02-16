<?php

class AttributeTracePredicate extends FilterPredicate
{
    function _predicate( $filter )
    {
        $sqls = array();

        foreach( \TextUtils::parseItems($filter) as $item ) {
            list($attribute, $searchType) = explode(':', $item);
            foreach( $this->getObject()->getPersisters() as $persister ) {
                if ( in_array($attribute, $persister->getAttributes()) ) {
                    foreach( $persister->getSelectColumns($this->getAlias()) as $column ) {
                        $columnPlaceholder = ') ' . $attribute . ' ';
                        $emptyCondition = stripos($column, 'concat_ws') !== false
                            ? " = '' " : ' IS NULL ';
                        $notEmptyCondition = stripos($column, 'concat_ws') !== false
                            ? " <> '' " : ' IS NOT NULL ';
                        if ( strpos($column, $columnPlaceholder) !== false ) {
                            $sqls[] = $searchType == 'none'
                                ? str_replace($columnPlaceholder, ')', $column ) . $emptyCondition
                                : str_replace($columnPlaceholder, ')', $column ) . $notEmptyCondition;
                        }
                    }
                }
            }
        }

        if ( count($sqls) > 0 ) {
            return " AND (" . join(' OR ', $sqls) . ") ";
        }
        else {
            return " AND 1 = 2 ";
        }

        switch ( $filter )
        {
            case 'noissues':
                $states = \WorkflowScheme::Instance()->getTerminalStates($this->getObject());
                if ( count($states) < 1 ) $states = array('');

                $traceClass = getFactory()->getObject('RequestTraceRequirement')->getObjectClass();
                $sql = " AND ( NOT EXISTS( SELECT 1 FROM pm_ChangeRequestTrace l " .
                    "    		        WHERE l.ObjectId = t.WikiPageId ".
                    "      			      AND l.ObjectClass = '".$traceClass."'".
                    "				      AND l.Type = '".REQUEST_TRACE_PRODUCT."' ) AND t.State NOT IN ('".join("','", $states)."') ".
                    "       OR EXISTS ( SELECT 1 FROM WikiPageChange ch
                                            WHERE ch.WikiPage = t.WikiPageId 
                                              AND ch.RecordCreated >
                                                  (SELECT MAX(r.FinishDate) FROM pm_ChangeRequestTrace l, pm_ChangeRequest r
                                                      WHERE l.ObjectClass = '".$traceClass."'
                                                        AND l.Type = '".REQUEST_TRACE_PRODUCT."'
                                                        AND l.ObjectId = ch.WikiPage
                                                        AND l.ChangeRequest = r.pm_ChangeRequestId) ) 
                             )";
                if ( getFactory()->getObject('RequirementType')->getRecordCount() > 0 ) {
                    $sql .= " AND EXISTS (SELECT 1 FROM WikiPageType pt WHERE pt.WikiPageTypeId = t.PageType AND pt.IsImplementing = 'Y') ";
                    $sql .= " AND (t.ParentPage IS NULL OR EXISTS (SELECT 1 FROM WikiPage pp LEFT OUTER JOIN WikiPageType pt ON pt.WikiPageTypeId = pp.PageType 
                                                                    WHERE t.ParentPage = pp.WikiPageId AND IFNULL(pt.IsImplementing,'N') = 'N')) ";
                }
                return $sql;
            case 'nosourceissues':
                return " AND ( NOT EXISTS( SELECT 1 FROM pm_ChangeRequestTrace l " .
                    "    		        WHERE l.ObjectId = t.WikiPageId ".
                    "      			      AND l.ObjectClass = '".getFactory()->getObject('RequestTraceRequirement')->getObjectClass()."'".
                    "				      AND l.Type = '".REQUEST_TRACE_REQUEST."' ) )";
            case 'nofeatures':
                return " AND NOT EXISTS( SELECT 1 FROM pm_FunctionTrace l " .
                    "    		      WHERE l.ObjectId = t.WikiPageId ".
                    "      			    AND l.ObjectClass = '".getFactory()->getObject('FunctionTraceRequirement')->getObjectClass()."') ";
            case 'nosourcereqs':
                return " AND NOT EXISTS (SELECT 1 FROM WikiPageTrace l, WikiPage p " .
                    "				  WHERE l.TargetPage = t.WikiPageId )";

            case 'notesting':
                return
                    " AND (EXISTS (SELECT 1 FROM WikiPageType pt WHERE pt.WikiPageTypeId = t.PageType AND pt.IsTesting = 'Y') OR t.PageType IS NULL)".
                    " AND NOT EXISTS (SELECT 1 FROM WikiPageTrace l, WikiPage p " .
                    "				  WHERE l.SourcePage = t.WikiPageId" .
                    "                   AND l.TargetPage = p.WikiPageId" .
                    "				    AND p.ReferenceName = '".WikiTypeRegistry::TestScenario."' )";

            default:
                $coverage_it = getFactory()->getObject('RequirementCoverage')->getExact( $filter );
                return " AND NOT EXISTS (SELECT 1 FROM WikiPageTrace l, WikiPage p " .
                    "				  WHERE l.SourcePage = t.WikiPageId" .
                    "                    AND l.TargetPage = p.WikiPageId" .
                    "				    AND p.ReferenceName = '".$coverage_it->get('ReferenceName')."' )";
        }
    }
} 
