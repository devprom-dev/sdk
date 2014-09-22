<?php
 
 class IssueTypeFrame
 {

 	static function getIcon( & $type_it )
 	{
         return IssueTypeFrame::getIconByRefName($type_it->get('ReferenceName'));
 	}

    static function getIconByRefName($referenceName)
    {
        switch ($referenceName)
        {
            case 'enhancement':
                $icon = 'layout_edit.png';
                break;

            case 'bug':
                $icon = 'bug.png';
                break;

            default:
                $icon = 'layout_add.png';
                break;
        }

        return $icon;
    }
 }
 
?>