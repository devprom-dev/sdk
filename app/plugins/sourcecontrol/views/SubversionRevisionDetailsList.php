<?php

class SubversionRevisionDetailsList extends SubversionList
{
    var $connector;

    function getIterator()
    {
        global $_REQUEST;

        $table = $this->getTable();
        $repo_it = $table->getSubversionIt();

        $this->connector = $repo_it->getConnector();
        return $this->connector->getVersionFiles($_REQUEST['version']);
    }

    function getColumns()
    {
        $this->object->addAttribute('File', '', translate('Имя каталога или файла'), true);
        $this->object->addAttribute('Path', '', translate('Путь'), true);
        $this->object->addAttribute('Change', '', translate('Вид изменения'), true);

        return parent::getColumns();
    }

    function IsNeedToModify( $object_it ) { return false; }
    
    function IsNeedToDisplay( $attr )
    {
        return $attr == 'Path' || $attr == 'File' || $attr == 'Change';
    }

    function drawCell( $object_it, $attr )
    {
        global $_REQUEST, $project_it;

        $table = $this->getTable();
        
        $repo_it = $table->getSubversionIt();

        switch( $attr )
        {
            case 'File':
            	
                if ( $object_it->get('Action') != translate('Удалено') )
	            {
	                $path = $object_it->utf8towin($object_it->get('Path'));
	                
	                $content = preg_split('/\//', $object_it->get('ContentType'));
	
	                if ( $content[0] == 'text' )
	                {
	                    echo '<a href="/pm/'.$project_it->get('CodeName').
		                    '/module/sourcecontrol/files?path='.urlencode($path).
		                    '&version='.SanitizeUrl::parseUrl($_REQUEST['version']).'&subversion='.$repo_it->getId().
		                    '&name='.urlencode($object_it->get('Name')).'">'.
		                    $object_it->utf8towin($object_it->get('Name')).'</a>';
	                }
	                else
	                {
	                    echo '<a href="?export=download&path='.urlencode($path).
		                    '&version='.SanitizeUrl::parseUrl($_REQUEST['version']).'&subversion='.$repo_it->getId().
		                    '&name='.urlencode($object_it->get('Name')).'">'.
		                    $object_it->utf8towin($object_it->get('Name')).'</a>';
	                }
	            }
	            else
	            {
	                echo $object_it->utf8towin($object_it->get('Name'));
	            }
            	
            	break;
            	
            case 'Path':
            	
                if ( $object_it->get('Action') != translate('Удалено') )
	            {
	                echo $object_it->utf8towin($object_it->get('Path'));
	            }
	            
	            break;
	            
            case 'Change':
            	
                echo $object_it->get('Action');
            	
	            if ( $object_it->get('Action') == translate('Изменено') )
	            {
	                $log_it = $this->connector->getFileLogs(
	                        $object_it->get('Path'), 0, $_REQUEST['version'] );
	                 
	                $preversion = $log_it->get('Version');
	                $log_it->moveNext();
	                 
	                if ( !$log_it->end() )
	                {
	                    echo ' (<a href="/pm/'.$project_it->get('CodeName').
	                    '/module/sourcecontrol/files?mode=diff&path='.$object_it->get('Path').
	                    '&version='.$log_it->get('Version').'&preversion='.$preversion.'&subversion='.$repo_it->getId().
	                    '&name='.$object_it->get('Name').'">'.translate('Посмотреть изменения').'</a>)';
	                }
	            }
	            
	            break;
	            
            default:
            	parent::drawCell( $object_it, $attr );
        }
	}
}