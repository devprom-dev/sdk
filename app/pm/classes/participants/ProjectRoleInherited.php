<?php
include_once "ProjectRole.php";
include "ProjectRoleInheritedRegistry.php";

class ProjectRoleInherited extends ProjectRole
{
    function __construct() {
        parent::__construct( new ProjectRoleInheritedRegistry() );
    }
}