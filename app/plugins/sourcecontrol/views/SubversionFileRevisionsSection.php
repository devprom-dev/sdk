<?php

class SubversionFileRevisionsSection extends InfoSection
{
    var $subversion_it;

    function __construct( $subversion_it = null )
    {
        global $model_factory, $_REQUEST;
                
        if ( !is_a($subversion_it, 'SubversionIterator') )
        {
            $subversion = $model_factory->getObject('pm_Subversion');

            $this->subversion_it = $subversion->getExact($_REQUEST['subversion']);
        }
        else
        {
            $this->subversion_it = $subversion_it;
        }
        
        parent::InfoSection();
    }

    function getCaption() 
    {
        return translate('Версии');
    }

    function getParameters()
    {
        global $_REQUEST;
        
        return array (
            'subversion' => $_REQUEST['subversion'],
            'path' => $_REQUEST['path'],
            'name' => $_REQUEST['name']
        );    
    }
    
    function getRenderParms()
    {
        global $_REQUEST;
         
        $connector = $this->subversion_it->getConnector();
        
        $log_it = $connector->getFileLogs( $_REQUEST['path'] );
         
        $preversion = 0;
        
        $commits = array();
         
        while ( !$log_it->end() )
        {
            $commits[] = array (
                'version' => $log_it->get('Version'),
                'subversion' => $this->subversion_it->getId(),
                'author' => $log_it->get('Author'),
                'date' => $log_it->get('RecordModified'),
                'comment' => $log_it->get('Comment'),
                'version-url' => '?name='.SanitizeUrl::parseUrl($_REQUEST['name']).'&path='.SanitizeUrl::parseUrl($_REQUEST['path']).'&version='.$log_it->get('Version').'&subversion='.$this->subversion_it->getId()
            ); 
            
            if ( $preversion > 0 )
            {
                $commits[count($commits) - 1]['diff-url'] =
                    '?mode=diff&name='.SanitizeUrl::parseUrl($_REQUEST['name']).'&path='.SanitizeUrl::parseUrl($_REQUEST['path']).'&version='.$log_it->get('Version').'&preversion='.$preversion.'&subversion='.$this->subversion_it->getId();
            }
        
            $preversion = $log_it->get('Version');
            
            $log_it->moveNext();
        }
        
        return array_merge( parent::getRenderParms(), array (
            'commits' => $commits
        ));
    }
    
    function getTemplate()
    {
        return '../../plugins/sourcecontrol/templates/SubversionFileRevisionSection.php';
    }
}