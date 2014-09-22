<?php

class AccessRightList extends PMStaticPageList
{
    var $object_access_it, $access_it;

    function AccessRightList( $object )
    {
        parent::PageList( $object );
    }

    function retrieve()
    {
        global $model_factory;
        	
        $object_access = $model_factory->getObject('AccessObject');

        $this->object_access_it = $object_access->getAll();
        	
        $this->object_access_it->buildPositionHash( array('ReferenceName', 'ProjectRole') );
        	
        $table = $this->getTable();
        	
        $access = $table->getAccessObject();
        	
        $this->access_it = $access->getAll();
        	
        $this->access_it->buildPositionHash( array('RecordKey') );
        	
        parent::retrieve();
    }

    function IsNeedToDisplay( $attr )
    {
        global $_REQUEST;

        switch( $attr )
        {
            case 'ProjectRole':

                $values = $this->getFilterValues();
                
                return in_array($values['role'], array('', 'all'));

            case 'ReferenceType':
                
                return false;
                	
            default:
                
                return parent::IsNeedToDisplay( $attr );
        }
    }

    function drawCell( $object_it, $attr )
    {
        global $model_factory;

        switch ( $attr )
        {
            case 'ReferenceName':
                switch ( $object_it->get('ReferenceType') )
                {
                    case 'Y':
                        
                        echo getFactory()->getObject( $object_it->get('ReferenceName') )->getDisplayName();
                        
                        break;

                    case 'M':

                        echo translate($object_it->get('DisplayName'));
                        
                        break;

                    case 'O':
                        
                        $this->object_access_it->setStop( 'ReferenceName', $object_it->get('ReferenceName') );

                        $uid = new ObjectUID;

                        $it = $this->object_access_it->getObjectIt();
                        	
                        $uid->drawUidInCaption( $it );
                        
                        break;

                    default:
                        
                        echo $object_it->get('DisplayName');
                        
                        break;
                }

                break;

            case 'ReferenceType':
                switch ( $object_it->get('ReferenceType') )
                {
                    case 'M':
                        echo translate('Закладки');
                        break;
                        	
                    case 'PMReport':
                        echo translate('Отчеты');
                        break;

                    case 'Y':
                        echo translate('Сущности');
                        break;

                    case 'A':
                        echo translate('Атрибуты');
                        break;

                    case 'O':
                        echo translate('Объекты');
                        break;

                    case 'PMPluginModule':
                        echo translate('Модули');
                        break;
                }
                break;

            case 'AccessType':
                switch ( $object_it->get('ReferenceType') )
                {
                    case 'O':
                        
                        $this->object_access_it->setStop( 'ReferenceName', $object_it->get('ReferenceName') );

                        while( !$this->object_access_it->end() )
                        {
                            if ( $this->object_access_it->get('ProjectRole') == $object_it->get('ProjectRole') )
                            {
                                $it = $this->object_access_it->getObjectIt();
                                $method = new ObjectAccessWebMethod;

                                if ( $method->hasAccess() )
                                {
                                    $method->drawSelect($this->object_access_it, $it);
                                    break;
                                }
                            }
                            $this->object_access_it->moveNext();
                        }

                        if ( !is_object($method) )
                        {
                        	list($class_name, $object_id) = preg_split('/\./', $object_it->get('ReferenceName'));
                        	
                        	$class_name = getFactory()->getClass($class_name);
                        	
                        	if ( !class_exists($class_name) ) break;
                        	
                            $it = getFactory()->getObject($class_name)->getRegistry()->Query(
                            		array (
                            				new FilterInPredicate($object_id)
                            		)
                            );

                            $method = new ObjectAccessWebMethod;
                            
                            if ( $method->hasAccess() )
                            {
                                $method->drawSelect($object_it, $it);
                            }
                        }

                        break;

                    default:
                        $method = new StoreAccessWebMethod;

                        if ( $method->hasAccess() )
                        {
                            $key = $this->access_it->getRecordKey(
                                    $object_it->get('ReferenceName'),
                                    $object_it->get('ReferenceType'),
                                    $object_it->get('ProjectRole')
                            );

                            $this->access_it->moveTo( 'RecordKey', $key );

                            $method->drawSelect($object_it, $this->access_it->end() ? '' : $this->access_it->get('AccessType'));
                        }
                        break;
                }
                break;

            default:
                return parent::drawCell( $object_it, $attr );
        }
    }

    function getGroupFields()
    {
        $values = $this->getFilterValues();

        return $values['object'] == '' || $values['object'] == 'all'
                ? array('ReferenceType', 'ProjectRole') : array();
    }

    function getColumnWidth( $attr )
    {
        if ( $attr == 'AccessType' )
        {
            return '200';
        }
        else
        {
            return parent::getColumnWidth( $attr );
        }
    }

    function IsNeedToDisplayNumber()
    {
        return false;
    }
    
	function getHeaderAttributes( $attribute )
	{
		$result = parent::getHeaderAttributes( $attribute );
		
		if ( $attribute == 'AccessType' )
		{
			$result['script'] = '#';
		}

		return $result;
	}
}