<?php

class PageMenu
{
 	var $pages, $current;
 	
 	function PageMenu()
 	{
		$this->makePages();
 	}
 	
 	function makePages()
 	{
 		$plugins = getFactory()->getPluginsManager();
 		
 		$section = $this->getSection();

        $pages = $this->getPages();

        if ( count($pages) > 0 ) {
            $tabs = is_object($plugins) ? $plugins->getHeaderTabs( $section ) : array();
        }

	    foreach ( $tabs as $tab )
	    {
	    	$b_tab_merged = false;
	    	
			foreach ( $pages as $key => $page )
			{
				if ( $page['name'] != $tab['name'] ) continue;
				
				if ( $page['type'] == 'hidden' && $tab['type'] != 'extends' )
				{
					$b_tab_merged = true;
					
					break;
				}
			    
				if ( !is_array($page['items']) )
				{
					$pages[$key]['items'] = array();
				}
				
				$pages[$key]['items'] = array_merge(
					$pages[$key]['items'], $tab['items']);
				
				if ( $tab['module'] != '' )
				{
					$pages[$key]['url'] = $tab['url'];
					$pages[$key]['type'] = '';
				}

				if ( $tab['title'] != '' )
				{
					$pages[$key]['name'] = $tab['title']; 
				}
				
				$b_tab_merged = true;
				
				break;
			}

			if ( !$b_tab_merged )
			{
				$new_page = array( 
					'url' => $tab['url'], 
					'name' => $tab['name'], 
					'items' => $tab['items'],
					'uid' => $tab['uid'] );
						
				if ( $tab['after'] != '' )
				{
					foreach ( $pages as $key => $page )
					{
						if ( $page['name'] == $tab['after'] )
						{
							$begin = array_slice( $pages, 0, $key + 1 );
							$end = array_slice( $pages, $key + 1, count($pages) );
							
							$pages = array_merge(
								$begin, array( $new_page ), $end );
							break;
						}
					}
				}
				else
				{
					$key = array_push( $pages, $new_page );
				}
			}
	    }
	    
	    foreach ( $pages as $key => $page ) 
	    {
	    	$parts = preg_split('/\?/', $page['url']);
	    	$url = $parts[0];
	    	
	    	if ( $url == '' )
	    	{
	    		continue;
	    	}
	    	
	    	$url_pattern = '/'.str_replace('/', '\/', str_replace('.', '_', $url)).'/i';
	    	$script = str_replace('.', '_', $_SERVER['SCRIPT_NAME']);

	    	if ( preg_match($url_pattern, str_replace('.', '_', $_SERVER['REQUEST_URI']) ) ) 
			{
	    		$current_page_id = $key;
	    		break;
	    	}

			if ( is_array($page['items']) )
			{
				foreach ( $page['items'] as $subitem )
				{	    	
				    $parts = preg_split('/\?/', $subitem['url']);
			    	$url = $parts[0];

			    	if ( $url == '' )
			    	{
			    		continue;
			    	}
			    	
	    			$url_pattern = '/'.str_replace('/', '\/', str_replace('.', '_', $url)).'/i';
			    	
			    	if ( preg_match($url_pattern, str_replace('.', '_', $_SERVER['REQUEST_URI']) ) ) 
					{
						$current_page_id = $key;
			    		break;
			    	}
				}
			}
	    }

	    $this->pages = $pages;
	    
	    $this->current = $current_page_id;
 	}
 	
 	function getSection()
 	{
 		return '';
 	}
 	
 	function getPages()
 	{
 		return array();
 	}
 	
 	function getTabs()
 	{
 		if ( count($this->pages) && array_key_exists($this->current, $this->pages) )
 		{
 			$this->pages[$this->current]['active'] = true;
 		}
 		
 		return $this->pages;
 	}

 	function getSelectedIndex()
 	{
 		return $this->current;
 	}
 	
 	function getSelectedPage()
 	{
 		return $this->pages[$this->current];
 	}
 	
 	function getPageByUid( $uid )
 	{
 		foreach ( $this->pages as $page )
 		{
 			if ( $page['uid'] == $uid )
 			{
 				return $page;
 			}
 		}
 		
 		return array();
 	}
 	
 	function draw()
 	{
 		echo '<div class="menu">';
	 		echo '<div class="menu_tabs">';
	 			echo '<div class="menuitemseparator">';
					echo ' &nbsp; &nbsp;&nbsp;';
				echo '</div>';
	
				foreach( $this->pages as $key => $page ) 
				{
					if ( $page['type'] == 'hidden' )
					{
						continue;
					}
	
					if( $key == $this->current ) 
					{
						$class = 'active';
					}
					else
					{
						$class = '';
					}
	
					if ( $page['url'] == '' )
					{				
						echo '<div class="menuitemseparator">';
							echo '&nbsp;&nbsp;&nbsp;';
						echo '</div>';
					}
					else
					{
						echo '<div class="menuitem">';
							$this->drawItem( $page['url'], 
								$page['name'], $page['items'], $class );
						echo '</div>'; 
					}
				}        		
			echo '</div>';	
				
			echo '<div style="width:20%;float:right;">';
				$this->drawSearch();
			echo '</div>';	
		echo '</div>';
 	}
 	
 	function drawItem( $url, $title, $actions, $class )
 	{
 		$popup = new PopupMenu();
 		$popup->draw("menuitem_popup ".$class, trim($title, '.'), $actions, $url);
 	}
 	
 	function drawSearch()
 	{
 	}
}
