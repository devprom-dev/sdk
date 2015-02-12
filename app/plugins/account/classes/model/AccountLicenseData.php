<?php

class AccountLicenseData extends Metaobject
{
    function __construct()
    { 
        parent::Metaobject('cms_User');
    }
    
    function getAll()
    {
        $licenses = array();
        
        $other_it = parent::getByRefArray( array (
                'ICQ' => "NOT NULL"
        ));
        
        while ( !$other_it->end() )
        {
            $license_data = json_decode( $other_it->getHtmlDecoded('ICQ'), true );
        
            $license_data = !is_null($license_data) 
                ? (is_array($license_data) ? $license_data : array()) : array();
        
            foreach( $license_data as $license )
            {
                $license['cms_UserId'] = $other_it->getId();
                
                $licenses[] = $license;
            }
            	
            $other_it->moveNext();
        }
        
        return $this->createCachedIterator($licenses);
    }
    
    function getByUser( $id )
    {
        $other_it = parent::getExact($id);
        
        $license_data = json_decode( $other_it->getHtmlDecoded('ICQ'), true );
        
        $license_data = !is_null($license_data) 
            ? (is_array($license_data) ? $license_data : array()) : array();
        
        foreach( $license_data as $key => $value )
        {
            $license_data[$key]['cms_UserId'] = $id;
        }
        
        if ( count($license_data) < 1 )
        {
            $license_data[] = array( 'cms_UserId' => $id );
        }
        
        return $this->createCachedIterator($license_data);
    }
    
    function modify_parms( $id, $parms )
    {
    	$this->setNotificationEnabled(false);
    	
        $it = $this->getByUser( $id );
        
        while ( !$it->end() )
        {
            $data[] = $it->getData();
            
            if ( $it->get('uid') == $parms['uid'] && $it->get('type') == $parms['type'] )
            {
                $data[count($data)-1]['key'] = $parms['key'];
                $data[count($data)-1]['value'] = $parms['value'];
                
                break;
            }
            
            $it->moveNext();
        }
        
        if ( $it->end() )
        {
            $data[] = $parms;
        }

        return parent::modify_parms( $id, array( 
            'ICQ' => JsonWrapper::encode($data) 
        ));
    }
}