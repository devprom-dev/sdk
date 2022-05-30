<?php

 class InfoSection
 {
 	 private $page;
 	 private $async_load;
	 private $placement = 'right';
     private $id = '';

 	function __construct()
 	{
        $this->async_load = true;
        $this->id = strtolower(get_class($this));
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
 	
 	function setPage( $page )
 	{
 		$this->page = $page;
 	}

	 public function setPlacement( $placement ) {
		 $this->placement = $placement;
	 }

	 public function getPlacement() {
		 return $this->placement;
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

	function draw()
	{
		$caption = $this->getCaption();
		if ( $caption != '' )
		{
			echo '<div class=page_sub style="float:left;width:99%;">';
				echo '<div style="float:left;">';
					echo $this->getCaption().': ';
				echo '</div>';
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
		return $this->id;
	}

    function setId( $id ) {
         $this->id = $id;
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
	
	function render( $view, $parms = array() )
	{
		echo $view->render(
				$this->getTemplate(),
				array_merge($parms, $this->getRenderParms())
		);
	}

	function hasAccess() {
 	    return getFactory()->getAccessPolicy()->can_read(
 	            getFactory()->getObject('Module')->createCachedIterator(
                    array(
                        array(
                            'cms_PluginModuleId' => 'section:'.strtolower(get_class($this))
                        )
                    )
                )
        );
    }
 }