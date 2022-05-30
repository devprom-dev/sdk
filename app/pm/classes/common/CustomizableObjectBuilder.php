<?php

abstract class CustomizableObjectBuilder
{
    protected $session;
    
    function __construct( PMSession $session )
    {
        $this->session = $session;
    }
    
    public function getSession()
    {
        return $this->session;
    }
    
    abstract function build(CustomizableObjectRegistry & $set, $useTypes);
}