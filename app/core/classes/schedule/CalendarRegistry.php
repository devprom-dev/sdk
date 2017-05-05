<?php

class CalendarRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
    	$sql = " SELECT IFNULL(MAX(IntervalYear) + 1,YEAR(NOW())-5) cnt FROM pm_CalendarInterval ";
			   
		$it = parent::createSQLIterator( $sql );
        if ( $it->get('cnt') < 1 ) return $this->createIterator(array());

   		for( $i = max($it->get('cnt'), date('Y')-5); $i < date('Y') + 4; $i++ ) {
			$this->_createIntervals( $i );
		}
		
		return $this->createIterator(array());
    }
    
	private function _createIntervals( $year )
	{
	    try {
            DAL::Instance()->Query("LOCK TABLES pm_CalendarInterval WRITE");
            // calculates quarters
            $beginning_dates = array (
                $year.'-01-01', $year.'-04-01', $year.'-07-01', $year.'-10-01'
                );

            $sqls = array();
            foreach ( $beginning_dates as $beginning_date )
            {
                $sql = " INSERT INTO pm_CalendarInterval ( Caption, Kind, StartDate, StartDateOnly, StartDateWeekday, FinishDate, IntervalYear )" .
                       " VALUES ( QUARTER('".$beginning_date."'), 'quarter', '".$beginning_date."', DATE('".$beginning_date."'), DAYOFWEEK('".$beginning_date."'),".
                       "          DATE_SUB(DATE_ADD('".$beginning_date."', INTERVAL 3 MONTH), INTERVAL 1 DAY), ".$year." ) ";

                DAL::Instance()->Query($sql);
            }

            // calculates monthes
            $sqls = array();
            for ( $i = 0; $i < 12; $i++ )
            {
                $beginning_date = $year.'-'.($i+1).'-01';

                $sql = " INSERT INTO pm_CalendarInterval ( Caption, Kind, StartDate, StartDateOnly, StartDateWeekday, FinishDate, IntervalYear, IntervalMonth, IntervalQuarter )" .
                       " VALUES ( MONTH('".$beginning_date."'), 'month', '".$beginning_date."', DATE('".$beginning_date."'), DAYOFWEEK('".$beginning_date."'),".
                       "        DATE_SUB(DATE_ADD('".$beginning_date."', INTERVAL 1 MONTH), INTERVAL 1 DAY), ".$year.",".
                       "        MONTH('".$beginning_date."'), QUARTER('".$beginning_date."') )";

                DAL::Instance()->Query($sql);
            }

            // calculates weeks
            $sqls = array();
            for ( $i = 0; $i < 52; $i++ )
            {
                $beginning_date = $year.'-01-01';

                $sql = " INSERT INTO pm_CalendarInterval ( Caption, Kind, StartDate, StartDateOnly, StartDateWeekday, FinishDate, " .
                       "								   IntervalYear, IntervalMonth, IntervalQuarter, IntervalWeek )" .
                       " VALUES ( ".$i.", 'week', ".
                       "		DATE_SUB(DATE_ADD('".$beginning_date."', INTERVAL ".($i)." WEEK), INTERVAL 1 DAY), ".
                       "		DATE(DATE_SUB(DATE_ADD('".$beginning_date."', INTERVAL ".($i)." WEEK), INTERVAL 1 DAY)), ".
                       "		DAYOFWEEK(DATE_SUB(DATE_ADD('".$beginning_date."', INTERVAL ".($i)." WEEK), INTERVAL 1 DAY)), ".
                       "        DATE_SUB(DATE_ADD('".$beginning_date."', INTERVAL ".($i+1)." WEEK), INTERVAL 1 DAY), ".$year."," .
                       "        MONTH(DATE_ADD('".$beginning_date."', INTERVAL ".$i." WEEK)), " .
                       "        QUARTER(DATE_ADD('".$beginning_date."', INTERVAL ".$i." WEEK))," .
                       "        WEEK(DATE_ADD('".$beginning_date."', INTERVAL ".$i." WEEK)) )";

                DAL::Instance()->Query($sql);
            }

            // calculates days
            $sqls = array();
            for ( $i = 0; $i < 365; $i++ )
            {
                $beginning_date = $year.'-01-01';

                $sql = " INSERT INTO pm_CalendarInterval ( Caption, Kind, StartDate, StartDateOnly, StartDateWeekday, FinishDate, " .
                       "								   IntervalYear, IntervalMonth, IntervalQuarter, IntervalWeek, MinDaysInWeek )" .
                       " VALUES ( ".$i.", 'day', ".
                       "		DATE_ADD('".$beginning_date."', INTERVAL ".$i." DAY), ".
                       "		DATE(DATE_ADD('".$beginning_date."', INTERVAL ".$i." DAY)), ".
                       "		DAYOFWEEK(DATE_ADD('".$beginning_date."', INTERVAL ".$i." DAY)), ".
                       "        DATE_ADD('".$beginning_date."', INTERVAL ".$i." DAY), ".$year."," .
                       "        MONTH(DATE_ADD('".$beginning_date."', INTERVAL ".$i." DAY)), " .
                       "        QUARTER(DATE_ADD('".$beginning_date."', INTERVAL ".$i." DAY))," .
                       "        WEEK(DATE_ADD('".$beginning_date."', INTERVAL ".$i." DAY)), ".
                       "		IF(DAYOFWEEK(DATE_ADD('".$beginning_date."', INTERVAL ".$i." DAY))=1,7,DAYOFWEEK(DATE_ADD('".$beginning_date."', INTERVAL ".$i." DAY))-1) )";

                DAL::Instance()->Query($sql);
            }
            DAL::Instance()->Query("UNLOCK TABLES");
        }
        catch( Exception $e ) {
            DAL::Instance()->Query("UNLOCK TABLES");
        }
	}
}
