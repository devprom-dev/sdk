<?php

class ServicePayedPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         return array (
             " t.VPD IID ",
             " CEIL((TO_DAYS(t.RecordModified) - TO_DAYS(t.RecordCreated)) / 30) LTV ",
             " IF(TO_DAYS(NOW()) - TO_DAYS(t.RecordModified) > 14, 0, 1) IsActive "
         );
     }
}