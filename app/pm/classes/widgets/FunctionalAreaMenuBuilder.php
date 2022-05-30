<?php

class FunctionalAreaMenuBuilder
{
    private $report = null;
    private $module = null;

    public function getReport() {
        return $this->report;
    }

    public function setReport( $report ) {
        $this->report = $report;
    }

    public function getModule() {
        return $this->module;
    }

    public function setModule( $module ) {
        $this->module = $module;
    }

    public function build( FunctionalAreaMenuRegistry & $set )
    {
    }
}