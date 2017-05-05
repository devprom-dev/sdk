<?php

class CloneContext
{
    var $ids_map = array();
    var $default_parms = array();
    var $use_existing_refs = false;
    var $broken_references = array();
    private $reset_state_value = true;
    private $reset_dates = true;
    private $resetAssignments = false;
    private $resetUids = false;
    
    function getIdsMap()
    {
        return $this->ids_map;
    }

    function setIdsMap( $map )
    {
        $this->ids_map = $map;
    }
    
    function addBrokenReference( $class_name, $object_id, $attribute, $value )
    {
        $this->broken_references[$class_name][$object_id][$attribute] = $value;
    }
    
    function getBrokenReferences()
    {
        return $this->broken_references;
    }
    
    function getDefaultParms()
    {
        return $this->default_parms;
    }
    
    function setDefaultParms( $parms )
    {
        $this->default_parms = $parms;
    }
    
    function getUseExistingReferences()
    {
        return $this->use_existing_refs;
    }

    function setUseExistingReferences( $flag )
    {
        $this->use_existing_refs = $flag;
    }
    
    function setResetState( $flag )
    {
    	$this->reset_state_value = $flag; 
    }
    
    function getResetState()
    {
    	return $this->reset_state_value;
    }
    
    function setResetDates( $flag = true )
    {
    	$this->reset_dates = $flag;
    }
    
    function getResetDates()
    {
    	return $this->reset_dates;
    }

    function setResetAssignments( $flag = true ) {
        $this->resetAssignments = $flag;
    }

    function getResetAssignments() {
        return $this->resetAssignments;
    }

    function setResetUids( $flag = true ) {
        $this->resetUids = $flag;
    }

    function getResetUids() {
        return $this->resetUids;
    }
}