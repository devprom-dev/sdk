<?php

 include('c_form.php');
 include('c_list.php');

 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class ViewSimple
 {
 	var $form, $object;

 	function ViewSimple( $object ) 
	{
		$this->object = $object;
		$this->form = $this->createForm();
	}
	
	function createForm() {
		return new Form( $this->object );
	}
	
	function getCaption() {
		return $this->form->getCaption();
	}
	
	function draw()
	{
	?>
        <table width=70%>
        	<tr>
        		<td valign=top>
                <?
                 // отрисовываем форму редактирования объекта
                 $this->form->draw();
                ?>
        		</td>
        </table>
	<?
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class ViewBasic extends ViewSimple
 {
 	var $list;

 	function ViewBasic( $object ) 
	{
		global $_REQUEST;
		
		parent::ViewSimple( $object );

		$this->list = $this->createListForm();
		
		if($_REQUEST['view'] == 'table' && $this->list->action != '') {
			exit(header('Location: '.$this->list->getUrl() ));
		}
	}

	function createListForm() {
		return new ListForm( $this->object );
	}
	
	function draw()
	{
	?>
        <table width=100%>
        	<tr>
        		<td width=40% valign=top>
                <?
                 // отрисовываем форму редактирования объекта
                 $this->form->draw();
                ?>
        		</td>
				<?
            	if(is_object($this->list)) 
            	{
				?>
        		<td width=20>
        		</td>
        		<td valign=top>
                <?
                 // отрисовываем список объектов
                 $this->list->draw();
                ?>
        		</td></tr>
				<?
            	}
        		?>
        </table>
	<?
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class ViewComposite extends ViewBasic
 {
 	var $aggregates = array();
	var $container_id;
	
	function ViewComposite( $object ) 
	{
		parent::ViewBasic( $object );

        $this->container_id = $_REQUEST[$this->object->getClassName().'Id'];
		for($i = 0; $i < count($object->aggregates); $i++) {
			$this->addAggregatedObject( $object->aggregates[$i] );
		}
	}
	
	function addAggregatedObject( $object )
	{
        if($this->container_id != '')
        {
			$classname = $object->getClassName();
			$object = getFactory()->getObject($classname, $this->object->getExact($this->container_id));

			array_push( $this->aggregates, $object->createDefaultView() );
		}
	}
	
 	function draw()
	{
		parent::draw();
	?>
		<table width=100% cellpadding=0 cellspacing=0>
	<?
	   for($i = 0; $i < count($this->aggregates); $i++) 
   	   {	
       ?>
   		<tr>
   			<td>
    		<?
    		$this->aggregates[$i]->draw();
    		?>
	    	</td>
   		</tr>
       	<?
		}	
	?>
		</table>
	<?
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class ViewTable
 {
 	var $list, $object, $view_num;

 	function ViewTable( $object = null ) 
	{
		$this->object = is_object($object) ? $object : $this->getObject();

		$list = $this->getList();
		if ( is_object($list) )
		{
			$this->setList( $list );
		}
	}
	
	function getObject()
	{
		return $this->object;
	}
	
	function getList()
	{
		return new ListTable( $this->object );
	}
	
	function & getListRef()
	{
		return $this->list;
	}

	function setList( $list )
	{
		global $view_num;

		$this->list = $list;

		$view_num += 1;
		$this->view_num = $view_num;

		$this->list->setOffsetName('offset'.$this->view_num);
	}
	
	function _getCaption() 
	{
		return $this->getCaption();
	}
	
	function getCaption() 
	{
		if ( is_object($this->object) )
		{
			return translate($this->object->getDisplayName());
		}
		else
		{
			return '';
		}
	}
	
	function getAddLinkUrl() {
		return $this->object->getPageName();
	}
	
	function getAddLinkName() {
		return translate('Добавить');
	}

	function getTablePageUrl() {
		return $this->object->getPageTableName();
	}
	
	function IsNeedToAdd() 
	{
	    if ( is_object($this->getObject()) ) return getFactory()->getAccessPolicy()->can_create($this->getObject());

        return false;
	}
	
	function IsNeedToDelete() 
	{
		return getFactory()->getAccessPolicy()->can_delete($this->object);
	}

	function IsNeedNavigator() {
		return true;
	}
	
	function IsNeedLinks() {
		return true;
	}

	function getUserLinks() {
		return array();
	}

	function getUserActionLinks() {
		return array();
	}
	
	function & getListIterator()
	{
	    $list = $this->getListRef();
	    
		if ( !is_object($list->getIteratorRef()) )
		{
			$list->retrieve();
		}
		
		return $list->getIteratorRef();
	}
	
	function & getListIteratorRef()
	{
	    $list = $this->getListRef();
	    
	    return $list->getIteratorRef();
	}

	function IsVisibleWhenEmpty()
	{
		return true;
	}
	
	function getFilterOrientation()
	{
		return 'right';
	}
	
	function draw( &$view = null )
	{
		$it = $this->getListIterator();
		
		if ( !$this->IsVisibleWhenEmpty() && $it->count() < 1 )
		{
			return;
		}
		
		$is_need_toadd = $this->IsNeedLinks() && $this->IsNeedToAdd();
		$is_need_todelete = $this->IsNeedLinks() && $this->list->IsNeedToDelete() && $it->count() > 0;
		$is_need_navigator = $this->IsNeedNavigator() && $it->count() > 0 && $this->list->moreThanOnePage();

		$action_links = $this->getUserActionLinks();
		$user_links = $this->getUserLinks();
		
		$caption = $this->_getCaption();

		$is_need_actions = ($is_need_toadd || $is_need_todelete || count($action_links) > 0 ||
			count($user_links) > 0);
		
		$has_read_access = getFactory()->getAccessPolicy()->can_read($it->object); 
		?>
        <table width=100% cellpadding=0 cellspacing=0>
			<?
			if( ( $is_need_actions || !$has_read_access ) )
			{
			?>
			<tr>
				<td style="padding-bottom:3pt;">
					<b>
						<? echo $caption; ?>
					</b>
				</td>
			</tr>
			<?
			}
			
			if ( !$has_read_access )
			{
				echo '<tr><td style="padding-left:6pt;padding-bottom:6pt">';
					echo text(549);
				echo '</td></tr>';
			}
			else
			{
			?>
			<tr>
				<td <? echo ($is_need_actions ? 'style="padding-bottom:6pt;"' : 'style="padding-bottom:6pt;"'); ?> >
					<?
					if ( !$is_need_actions )
					{
					?>
					<div style="float:left;padding-right:8px;">
						<b><? echo $caption; ?></b>
					</div>
					<?
					}
					if($is_need_toadd) 
					{
					?>
					<div style="float:left;">
						<a href="<? echo $this->getAddLinkUrl(); ?>"><? echo $this->getAddLinkName() ?></a>
					</div>
					<?
					}
					if($is_need_todelete) {
					?>								
					<div style="float:left;padding-left:8px;">
						<a href="javascript: var act = document.getElementById('list.form.action<? echo $this->view_num; ?>'); act.value = 'list.delete_group'; var lst = document.getElementById('list.form<? echo $this->view_num; ?>'); lst.submit(); "><? echo_lang('Удалить') ?></a>
					</div>
					<?
					}
					$keys = array_keys($action_links);
					for($i = 0; $i < count($keys); $i++) {
					?>
					<div style="float:left;padding-left:8px;">
						<a href="javascript: var act = document.getElementById('list.form.action<? echo $this->view_num; ?>'); act.value = 'list.<? echo $action_links[$keys[$i]]?>'; var lst = document.getElementById('list.form<? echo $this->view_num; ?>'); lst.submit(); "><? echo str_replace(' ', '&nbsp;', $keys[$i]); ?></a>
					</div>
					<?
					}
					$keys = array_keys($user_links);
					for($i = 0; $i < count($keys); $i++) {
					?>
					<div style="float:left;padding-left:8px;">
						<a href="<? echo $user_links[$keys[$i]]; ?>"><? echo $keys[$i] ?></a>
					</div>
					<?
					}

					$width =  '100%';
					?>
					<div style="width:<?php echo $width;?>;float:left;">
					<?
					
					$this->drawFilter();
					?>
					</div>
				</td>
			</tr>
        	<tr>
        		<td id="tablePlaceholder" valign=top style="padding-bottom:6pt;">
					<form id="list.form<? echo $this->view_num; ?>" action="<? $this->list->getUrl() ?>" method="POST">
					<input type="hidden" id="list.form.action<? echo $this->view_num; ?>" name="<? echo $this->object->getClassName(); ?>action" value="">
				
		<div>
                <?
                 // отрисовываем таблицу с записями
				 $this->list->render( $view, array() );
                ?>
        </div>
					</form>
        		</td>
			</tr>
			<tr>
				<td align=right>
					<table width=100%>
						<tr>
							<td align=left>
								<? $this->drawFooter(); ?>
							</td>
							<td align=right valign=top>
							<? 
								if($is_need_navigator) 
								{
									$this->list->drawNavigator(false);
								} 
							?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?
			}
			?>
        </table>
	<?
	}
	
	function drawFooter()
	{
	}
	
	function drawFilter()
	{
		echo '&nbsp;';
	}
 }
 
?>