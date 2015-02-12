<?php

class ServicePayedPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         return array (
             " t.VPD IID " 
         );
     }
}