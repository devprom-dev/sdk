<?php

 include SERVER_ROOT_PATH.'pm/methods/c_portlet_methods.php';
         
 ///////////////////////////////////////////////////////////////////////////////////////
 class Portlet
 {
	function getCaption() {
		return '';
 	}
 	
 	function getCaptionMinimized() {
 		return $this->getCaption();
 	}
 	
 	function draw() 
 	{
 		if($this->IsMaximized()) {
 			$this->drawMaximized();
 		}
 		else {
 			$this->drawMinimized();
 		}
 	}
 	
 	function IsAvailable() {
 		return true;
 	}
 	
 	function IsVisible() {
 		$method = $this->getMethodClose();
 		return $this->IsAvailable() && !$method->isSwitchedOn();
 	}
 	
 	function IsClosable() {
 		return false;
 	}
 	
 	function IsMinimizable() {
 		return false;
 	}

 	function IsMaximized() {
 		return !$this->IsMinimized();
 	}

 	function IsMinimized() {
 		$method = new PortletStateWebMethod($this);
 		return $method->isSwitchedOn();
 	}

 	function IsClosed() {
 		$method = $this->getMethodClose();
 		return $method->isSwitchedOn();
 	}
 	
 	function drawMaximized() 
 	{
 		global $part_it;
 	?>
 		<div class=portlet_table style="float:left;height:100%;width:100%;margin-bottom:20px;">
			<div class=portlet_header style="padding:0;width:100%;">
				<div style="float:left;padding:2pt 4pt 2pt 4pt;">
					<b><? echo $this->getCaption() ?></b>
				</div>
				<div style="float:right;padding:4pt 4pt 2pt 4pt;">
				<?
					if ( $part_it->getId() != GUEST_UID ) 
					{
						$this->drawMainWindowLink();
					
						if ( $this->IsMinimizable() )
						{	
							$this->drawMinimizedLink();
						}
	 							
						if($this->IsClosable()) 
						{
							$this->drawCloseLink(); 
						}
					} 
				?>
				</div>
			</div>
			<div id="<? echo get_class($this) ?>" valign=top style="clear:both;overflow:hidden;">
				<? $this->drawBody() ?>
			</div>
		</div>
 	<?
 	}
	
	function getMethodClose() {
		return new PortletCloseWebMethod( $this );
	}

	function getMethodMaximize() {
		return new PortletStateWebMethod( $this );
	}

	function drawMinimizedLink() 
	{
		$method = $this->getMethodMaximize();
		$method->drawLink();
	}

	function drawCloseLink() 
	{
		$method = $this->getMethodClose();
		$method->drawLink();
	}
	
	function drawMainWindowLink()
	{
		echo '<a class=modify_image href="'.$this->getMainWindowLink().
			'" title="'.translate('Открыть').'"><img src="/images/application_get.png" border=0 style="margin-bottom:-4px;"></a>';
	}
	
	function getMainWindowLink()
	{
		return '';
	}
	
	function drawMinimized()
	{
		$method = $this->getMethodMaximize();
		$method->drawLink();
		
		$new_items= $this->getNewItemsCount();
		if ( $new_items > 0 )
		{
			echo ' (+'.$new_items.')';
		}
	}
 	
 	function drawBody() {
 	}
 	
 	function getNewItemsCount()
 	{
 		return 0;
 	}
 }
 
?>
