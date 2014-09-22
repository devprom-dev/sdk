<?php

class AdminPageMenu extends PageMenu
{
	function getSection()
	{
		return 'admin';
	}

	function getPages()
	{
		$pages = array();

		array_push($pages, array( 
			'name' => translate('�������'),
			'items' => array (
				array ( 'name' => translate('����������'), 'url' => '/admin/activity.php' ),  
			    array ( 'name' => translate('����������'), 'url' => '/admin/updates.php' ),  
				array ( 'name' => translate('��������� �����'), 'url' => '/admin/backups.php' ),
				array ( 'name' => translate('����'), 'url' => '/admin/log/' )
			)  
		));
		
		array_push($pages, array( 'url' => '/admin/users.php', 'name' => translate('������������'),
			'items' => array(
				array( 'url' => '/admin/users.php', 'name' => translate('������'), 'title' => translate('������ �������������') ),
				array(),
				array( 'url' => '/admin/blacklist.php', 'name' => translate('����������') )
			)
		));
		array_push($pages, array( 'url' => '/admin/projects.php', 'name' => translate('�������'),
			'items' => array(
				array( 'url' => '/admin/projects.php', 'name' => translate('������'), 'title' => translate('������ ��������') ),
				array(),
				array( 'url' => '/admin/templates.php', 'name' => translate('�������') )
			)
		));

		array_push($pages, array( 'url' => '/admin/commonsettings.php', 'name' => translate('���������'),
			'items' => array(
				array( 'url' => '/admin/commonsettings.php', 'name' => text(1833) ),
				array( 'url' => '/admin/mailer/', 'name' => translate('�����') ),
				array(),
				array( 'url' => '/admin/jobs.php', 'name' => translate('�������') ),
				array ( 'url' => '/admin/checks.php', 'name' => translate('��������') ),
				array ( 'url' => '/admin/license/', 'name' => translate('��������������') ),
				array( 'url' => '/admin/plugins.php', 'name' => translate('�������') ),
		        array(),
				array( 'url' => '/admin/dictionaries.php', 'name' => translate('�����������'), 'items' => $this->getDictionaries() ),
		)
		));
		
		return $pages;
	}
	
	function getDictionaries()
	{
		global $model_factory;
		
		$entity = new Metaobject('entity');
		
		$it = $entity->getByRefArray( array ( 
			'IsDictionary' => 'Y',
			'ReferenceName' => array(
				'pm_ProjectRole', 
				'pm_TaskType', 
				'Priority', 
				'pm_Importance',
				'pm_ChangeRequestLinkType',
				'pm_TestExecutionResult'
			)
		));
		
		$items = array();
		
		while( !$it->end() )
		{
			$items[] = array ( 
				'name' => $it->getDisplayName(),
				'url' => '/admin/dictionaries.php?dict='.$it->get('ReferenceName')
			);
				
			$it->moveNext();
		}
		
		return $items;
	}
}
