<?php

include_once "classes/Subversion.php";
include_once "classes/SubversionRevision.php";
include_once "classes/SubversionUser.php";
include_once "classes/SubversionAuthor.php";
include_once "classes/SCMFileChange.php";
include_once 'SourceControlPMPlugin.php';

class SourceControlPlugin extends PluginBase
{
    function getNamespace()
    {
        return 'sourcecontrol';
    }

    function getFileName()
    {
        return 'sourcecontrol.php';
    }

    function getCaption()
    {
        return text('sourcecontrol1');
    }

    function getIndex()
    {
        return parent::getIndex() + 150;
    }

    function getSectionPlugins()
    {
        return array( new SourceControlPMPlugin );
    }
    
    function getClasses()
    {
        return array (
                'pm_subversion' => array('Subversion', 'Subversion.php'),
                'pm_subversionrevision' => array('SubversionRevision', 'SubversionRevision.php'),
        		'pm_subversionuser' => array('SubversionUser', 'SubversionUser.php'),
                'pm_scmfilechanges' => array('SCMFileChange', 'SCMFileChange.php'),
        );
    }
}