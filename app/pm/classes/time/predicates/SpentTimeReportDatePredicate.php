<?php
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class SpentTimeReportDatePredicate extends FilterPredicate
{
    const MAX_AFTER = '1970-01-01';
    const MAX_BEFORE = '2037-01-01';

    private $after = '';
    private $before = '';

    function __construct($after, $before)
    {
        $mapper = new ModelDataTypeMappingDate();

        $this->after = $mapper->map(DAL::Instance()->Escape($after));
        if ( $this->after == '' ) $this->after = self::MAX_AFTER;

        $this->before = $mapper->map(DAL::Instance()->Escape($before));
        if ( $this->before == '' ) $this->before = self::MAX_BEFORE;

        parent::__construct('dummy');
    }

    function _predicate( $filter )
 	{
        if ( $this->after == self::MAX_AFTER && $this->before == self::MAX_BEFORE ) {
            return " AND 1 = 1 ";
        }

        switch( $this->getObject()->getEntityRefName() )
        {
            case 'pm_ChangeRequest':
                return " AND ( EXISTS (SELECT 1 FROM pm_Activity a 
                                        WHERE a.ReportDate BETWEEN '{$this->after}' AND '{$this->before}'
                                          AND a.Issue = {$this->getPK('t')} )
                              OR EXISTS (SELECT 1 FROM pm_Activity a, pm_Task s 
                                          WHERE a.ReportDate BETWEEN '{$this->after}' AND '{$this->before}'
                                            AND a.Task = s.pm_TaskId
                                            AND s.ChangeRequest = {$this->getPK('t')} ) )";
            case 'pm_Task':
                return " AND EXISTS (SELECT 1 FROM pm_Activity a 
                                      WHERE a.ReportDate BETWEEN '{$this->after}' AND '{$this->before}'
                                        AND a.Task = {$this->getPK('t')} ) ";
        }
        return " AND 1 = 2 ";
 	}
}
