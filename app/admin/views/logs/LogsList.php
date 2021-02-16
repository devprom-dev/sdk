<?php

class LogsList extends StaticPageList
{
    function extendModel()
    {
        $this->getObject()->addAttribute('Size', '', translate('Размер'), true);
        $this->getObject()->setAttributeVisible('BackupFileName', true);
        $this->getObject()->setAttributeVisible('Caption', false);

        parent::extendModel();
    }

	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'Size':
				echo round($object_it->get($attr) / 1024 / 1024, 2).' Mb';
				break;
				
			case 'BackupFileName':
				echo '<a href="'.$object_it->getViewUrl().'">'.$object_it->get($attr).'</a>';
				break;
				
			default:
				return parent::drawCell( $object_it, $attr );
		}
	}
}
