<?php
include "predicates/ProjectTemplateLicensedPackagesPredicate.php";

class ProjectTemplateRegistryAll extends ObjectRegistrySQL
{
    function getFilters()
    {
        return array_merge( parent::getFilters(),
            array ( new ProjectTemplateLicensedPackagesPredicate() )
        );
    }
}