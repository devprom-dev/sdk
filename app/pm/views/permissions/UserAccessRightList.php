<?php

class UserAccessRightList extends AccessRightList
{
    var $roles, $part_it;
    
    private $access_policy = null;
    
    function __construct( $object, $participant_it )
    {
        parent::__construct( $object );
        
        $this->part_it = $participant_it;
        
        $this->roles = $this->part_it->getRoles();
        
        $policy = getFactory()->getAccessPolicy();
        
        foreach( $this->roles as $key => $role )
        {
            if ( $role < 1 ) $this->roles[$key] = $policy->getRoleByBase( $role );
        }
        
        $class_name = get_class($policy);
        
        $this->access_policy = new $class_name(getFactory()->getCacheService());
        
        $this->access_policy->setRoles($this->roles);
    }

    function IsNeedToDisplay( $attr )
    {
        global $_REQUEST;

        switch( $attr )
        {
            case 'ProjectRole':
            case 'ReferenceType':
                return false;
                	
            default:
                return parent::IsNeedToDisplay( $attr );
        }
    }

    function drawCell( $object_it, $attr )
    {
        switch ( $attr )
        {
            case 'AccessType':
                	
                switch ( $object_it->get('ReferenceType') )
                {
                    case 'Y':

                        $object = getFactory()->getObject($object_it->get('ReferenceName'));
                        	
                        $read_access = $this->access_policy->getEntityAccess(ACCESS_READ, $object);

                        if ( $read_access )
                        {
                            $write_access = $this->access_policy->getEntityAccess(ACCESS_MODIFY, $object);

                            if ( $write_access )
                            {
                                echo translate('Изменение');
                            }
                            else
                            {
                                echo translate('Просмотр');
                            }
                        }
                        else
                        {
                            echo translate('Нет');
                        }

                        break;

                    case 'A':

                        $parts = preg_split('/\./', $object_it->get('ReferenceName'));
                        
                        $object = getFactory()->getObject($parts[0]);
                        	
                        $read_access = $this->access_policy->getAttributeAccess(ACCESS_READ, $object, $parts[1]);

                        if ( $read_access )
                        {
                            $write_access = $this->access_policy->getAttributeAccess(ACCESS_MODIFY, $object, $parts[1]);

                            if ( $write_access )
                            {
                                echo translate('Изменение');
                            }
                            else
                            {
                                echo translate('Просмотр');
                            }
                        }
                        else
                        {
                            echo translate('Нет');
                        }

                        break;

                    case 'O':

                        $this->object_access_it->moveTo( 'ReferenceName', $object_it->get('ReferenceName') );

                        $it = $this->object_access_it->getObjectIt();

                        $read_access = $this->access_policy->getObjectAccess(ACCESS_READ, $it);

                        if ( $read_access )
                        {
                            echo translate('Есть');
                        }
                        else
                        {
                            echo translate('Нет');
                        }

                        break;

                    case 'PMPluginModule':
                        
                        $page = getFactory()->getObject('Module');

                        $page_it = $page->getExact( $object_it->get('ReferenceName') );
                        	
                        $read_access = $this->access_policy->getObjectAccess(ACCESS_READ, $page_it);

                        if ( $read_access )
                        {
                            echo translate('Есть');
                        }
                        else
                        {
                            echo translate('Нет');
                        }

                        break;
                        
                   default:
                        $page = getFactory()->getObject($object_it->get('ReferenceType'));
                        
                        $page_it = $page->getExact( $object_it->get('ReferenceName') );
                        	
                        $read_access = $this->access_policy->getObjectAccess(ACCESS_READ, $page_it);

                        if ( $read_access )
                        {
                            echo translate('Есть');
                        }
                        else
                        {
                            echo translate('Нет');
                        }

                        break;
                }
                	
                break;

            default:
                return parent::drawCell( $object_it, $attr );
        }
    }
    
    function getMaxOnPage()
    {
    	return 999;
    }
}