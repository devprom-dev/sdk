<?php

class ParticipantRoleIterator extends OrderedIterator
{
     function getDisplayName()
     {
         $role_it = $this->getRef('ProjectRole');
         
         return $role_it->getDisplayName();
     }
}
