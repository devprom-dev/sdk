<?php

include (dirname(__FILE__).'/../methods/c_plugin_methods.php');
include_once SERVER_ROOT_PATH.'cms/c_iterator_file.php';

class PluginList extends StaticPageList
{
	var $plugins;

	function getIterator()
	{
		global $plugins;
			
		$this->plugins = $plugins;

		$it = new IteratorFile( $this->getObject(), dirname(__FILE__).'/../../plugins' );
			
		$rowset = $it->getRowset();
		
		foreach( $rowset as $row => $data )
		{
			if ( $data['Caption'] == 'plugins.php' )
			{
				unset($rowset[$row]);
			}
		}
		
		return $this->getObject()->createCachedIterator(array_values($rowset));
	}

	function getColumns()
	{
		$this->object->addAttribute('Description', '', translate('Назначение'), true);
		$this->object->addAttribute('File', '', translate('Файл'), true);

		return parent::getColumns();
	}

	function IsNeedToDisplay( $attr )
	{
		return $attr == 'Caption' || $attr == 'Description' || $attr == 'File';
	}

	function drawCell( $object_it, $attr )
	{
		if( $attr == 'Caption' )
		{
			$namespaces = $this->plugins->getNamespaces();
			foreach ( $namespaces as $namespace )
			{
				if ( $namespace->getFileName() == $object_it->getDisplayName() )
				{
					echo $namespace->getCaption();
					return;
				}
			}
			
			echo $object_it->getDisplayName();
		}

		$namespaces = $this->plugins->getNamespaces();
		foreach ( $namespaces as $namespace )
		{
			if ( $namespace->getFileName() == $object_it->getDisplayName() )
			{
				if( $attr == 'Description' )
				{
					echo $namespace->getDescription();
					break;
				}
			}
		}

		if( $attr == 'File' )
		{
			echo $object_it->getDisplayName();
		}
	}

	function IsNeedToDisplayOperations()
	{
		return true;
	}

	function IsNeedToModify()
	{
		return false;
	}

	function getItemActions( $column_name, $object_it )
	{
		$actions = parent::getItemActions( $column_name, $object_it );

		$filename = $object_it->getDisplayName();

		$method = new TogglePluginWebMethod($filename);
		
		array_push( $actions, array( 
		    'url' => $method->getJSCall( array('file' => $filename, 'action' => md5(time())) ),
		    'name' => $method->getCaption()
		));

		return $actions;
	}

	function getRowColor( $object_it, $attr )
	{
		global $plugins;

		if ( !$plugins->pluginEnabled($object_it->getDisplayName()) )
		{
			return 'silver';
		}
		else
		{
			return 'black';
		}
	}

	function getGroupFields()
	{
		return array();
	}
}
