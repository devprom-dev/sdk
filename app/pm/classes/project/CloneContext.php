<?php

class CloneContext
{
    private $ids_map = array();
    private $default_parms = array();
    private $use_existing_refs = false;
    private $broken_references = array();
    private $reset_state_value = true;
    private $reset_dates = true;
    private $resetAssignments = false;
    private $resetUids = false;
    private $restoreFromTemplate = true;
    private $reuseProject = false;
    private $raiseExceptions = false;
    private $resetBaseline = false;
    
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

    function setRestoreFromTemplate( $flag ) {
        $this->restoreFromTemplate = $flag;
    }

    function getRestoreFromTemplate() {
        return $this->restoreFromTemplate;
    }

    function setReuseProject($flag = true) {
        $this->reuseProject = $flag;
    }

    function getReuseProject() {
        return $this->reuseProject;
    }

    function setRaiseExceptions($value) {
        $this->raiseExceptions = $value;
    }

    function getRaiseExceptions() {
        return $this->raiseExceptions;
    }

    function setResetBaseline($flag) {
        $this->resetBaseline = $flag;
    }

    function getResetBaseline() {
        return $this->resetBaseline;
    }
}