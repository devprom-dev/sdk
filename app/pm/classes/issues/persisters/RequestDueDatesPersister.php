<?php
class RequestDueDatesPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         $columns = array();

		 $columns[] =
			 "  t.DeliveryDate DueDate ";
         $columns[] =
         	 "  GREATEST(0, TO_DAYS(t.DeliveryDate) - TO_DAYS(NOW())) DueDays ";
         $columns[] =
         	 "  LEAST(5, GREATEST(-1, YEARWEEK(t.DeliveryDate) - YEARWEEK(NOW()))) + 2 DueWeeks ";
         
         return $columns;
     }
}
