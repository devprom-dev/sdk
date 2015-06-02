<?php

 class InfoSection
 {
 	var $closable, $page, $async_load;
 	
 	function InfoSection() 
 	{
 		$this->closable = false;
 		$this->async_load = true;
 	}

   	function __destruct()
 	{
 		$this->page = null;
 	}
 	
 	function setAsyncLoad( $async )
 	{
 	    $this->async_load = $async;
 	}
 	
 	function getAsyncLoad()
 	{
 	    return $this->async_load;
 	}
 	
 	function setClosable( $closable = true )
 	{
 		$this->closable = $closable;
 	}
 	
 	function setPage( $page )
 	{
 		$this->page = $page;
 	}
 	
 	function & getPage()
 	{
 		return $this->page;
 	}
 	
 	function getCaption() 
 	{
 		return '';
 	}
 	
	function drawBody() {
	}

	function Closeable()
	{
		return $this->closable;
	}
	
	function draw() 
	{
		$caption = $this->getCaption();
		if ( $caption != '' )
		{
			$script = "closeInfoSection('".$this->getId()."');";
			
			echo '<div class=page_sub style="float:left;width:99%;">';
				echo '<div style="float:left;">';
					echo $this->getCaption().': ';
				echo '</div>';
				if ( $this->closable )
				{
					echo '<div style="float:right;">';
						echo '<a style="text-decoration:none;" href="javascript: '.$script.'"><img style="margin:2px 2px 0 0;" src="/images/cross-mini.png"></a>';
					echo '</div>';
				}
			echo '</div>';
			echo '<div style="clear:both;"></div>';
		}
		
		?>
		<div style="padding:4px 3px 4px 3px;padding-bottom:12pt;">
			<div id="<? echo $this->getId() ?>">
			<? 
				$this->drawBody() 
			?>
			</div>
		</div>
		<?
	}
	
	function getId()
	{
		return strtolower(get_class($this));
	}
	
	function getParameters()
	{
		return array();
	}
	
	function getIcon()
	{
	    return 'icon-signal';
	}
	
	function isActive() 
	{
		return true;
	}
	
	function getActions()
	{
		return array();
	}
	
	function drawMenu( $caption = '<img src="/images/report.png">', 
				       $actions = array(), 
				       $style = 'background-color:#fffff2;' )
	{
		if ( count($actions) < 1 )
		{
			$actions = $this->getActions();
		}
		
		if ( count($actions) > 0 )
		{
			$popup = new PopupMenu();
			$popup->draw('list_row_popup', $caption, $actions);
		}
	}
	
	function getTemplate()
	{
		return 'core/PageSectionOther.php';
	}
	
	function getRenderParms()
	{
		return array(
			'section' => $this,
            'async' => $this->getAsyncLoad()
		);
	}
	
	function render( &$view )
	{
		echo $view->render( $this->getTemplate(), $this->getRenderParms() ); 
	}
 }