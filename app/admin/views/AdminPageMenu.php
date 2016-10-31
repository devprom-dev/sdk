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
			'items' => array (
				array ( 'name' => translate('Активности'), 'url' => '/admin/activity.php', 'uid' => 'acitivty' ),
			    array ( 'name' => translate('Обновления'), 'url' => '/admin/updates.php', 'uid' => 'updates' ),
				array( 'url' => '/admin/jobs.php', 'name' => text(2024), 'uid' => 'jobs' ),
			    array ( 'name' => translate('Резервные копии'), 'url' => '/admin/backups.php', 'uid' => 'backups' ),
				array ( 'name' => translate('Логи'), 'url' => '/admin/log/', 'uid' => 'logs' )
			)  
		));
		
		array_push($pages, array(
			'uid' => 'users',
			'url' => '/admin/users.php', 'name' => translate('Пользователи'),
			'items' => array(
				array( 'url' => '/admin/users.php', 'name' => translate('Список'), 'uid' => 'users', 'title' => translate('Список пользователей') ),
				array(),
				array( 'url' => '/admin/blacklist.php', 'name' => translate('Блокировки'), 'uid' => 'blacklist' )
			)
		));
		array_push($pages, array(
			'uid' => 'projects',
			'url' => '/admin/projects.php', 'name' => text('projects.name'),
			'items' => array(
				array( 'url' => '/admin/projects.php', 'name' => translate('Список'), 'title' => translate('Список проектов'), 'uid' => 'projects' ),
				array(),
				array( 'url' => '/admin/templates.php', 'name' => translate('Шаблоны'), 'uid' => 'templates' )
			)
		));

		array_push($pages, array(
			'uid' => 'settings',
			'url' => '/admin/commonsettings.php', 'name' => translate('Настройки'),
			'items' => array(
				array( 'url' => '/admin/commonsettings.php', 'name' => text(1833), 'uid' => 'commonsettings' ),
				array( 'url' => '/admin/mailer/', 'name' => translate('Почта') ),
				array(),
				array ( 'url' => '/admin/checks.php', 'name' => translate('Проверки') ),
				array ( 'url' => '/admin/license/', 'name' => translate('Лицензирование') ),
				array( 'url' => '/admin/plugins.php', 'name' => translate('Плагины'), 'uid' => 'plugins' ),
				array( 'url' => '/admin/systemtemplates/', 'name' => translate('Тексты') ),
				array(),
				array( 'url' => '/admin/dictionaries.php', 'name' => translate('Справочники'), 'items' => $this->getDictionaries() ),
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
