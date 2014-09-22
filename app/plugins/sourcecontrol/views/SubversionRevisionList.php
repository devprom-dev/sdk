<?php

class SubversionRevisionList extends SubversionList
{
    function getColumns()
    {

        $revision = $this->object;

        $revision->addAttribute('Content', '', translate('Содержание'), true);

        $rep_id = $revision->getFirst(1)->get('Repository');
        
        if ( $rep_id > 0 )
        {
            $connector = $revision->getAttributeObject('Repository')->getExact($rep_id)->getConnector();
            if ($connector->hasNumericVersion()) {
                $revision->setAttributeVisible('Version', false);
            } else {
                $revision->setAttributeVisible('VersionNum', false);
            }
        }
    
        return parent::getColumns();
    }

    function IsNeedToDelete( )
    {
        return PageList::IsNeedToDelete();
    }
    
    function IsNeedToModify( $object_it ) { return false; }
    
    function IsNeedToDisplay( $attr )
    {
        switch ( $attr )
        {
            case 'Project':
                
                return false;

            default:
                
                return PageList::IsNeedToDisplay( $attr );
        }
    }

    function drawCell( $object_it, $attr )
    {
        global $model_factory;
        
        switch ( $attr )
        {
            case 'Content':
                
                $menu = $model_factory->getObject('Module')->getExact('sourcecontrol/revision')
                    ->buildMenuItem('?mode=details&subversion='.$object_it->get('Repository').'&version='.$object_it->get('Version'));
                
                echo '<a href="'.$menu['url'].'">';
                    echo translate('Файлы');
                echo '</a>';
                
                break;

            case 'Author':
                
                $part_it = $object_it->getRef('Participant');
                
                if ( $part_it->getId() > 0 )
                {
                    echo $part_it->getDisplayName();
                }
                else
                {
                    parent::drawCell( $object_it, $attr );
                }
                
                break;

            default:
                
                parent::drawCell( $object_it, $attr );
        }
    }

    function IsNeedToDisplayNumber()
    {
        return false;
    }

    function getColumnWidth( $attr )
    {
        switch ( $attr )
        {
            case 'Version':
                return '80';

            case 'Description':
                return '50%';

            default:
                return parent::getColumnWidth( $attr );
        }
    }

    function getGroupFields()
    {
        return array_merge(PageList::getGroupFields(), array('Author'));
    }

    function getGroupDefault()
    {
        return 'none';
    }

    function getNoItemsMessage()
    {
        global $model_factory;
        
        $filter_values = $this->getFilterValues();
        
        $menu = $model_factory->getObject('Module')->getExact('sourcecontrol/connection')
            ->buildMenuItem('?connection='.array_pop(preg_split('/,/',$filter_values['subversion'])));
        
	    $job_it = $model_factory->getObject('co_ScheduledJob')->getByRef('ClassName', 'processrevisionlog');

	    $refresh_url = '/tasks/command.php?class=runjobs&job='.$job_it->getId().'&redirect='.urlencode($_SERVER['REQUEST_URI']); 
        
        return str_replace('%2', $refresh_url, str_replace('%1', $menu['url'], text('sourcecontrol25')));
    }
}