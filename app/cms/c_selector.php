<?php

 /////////////////////////////////////////////////////////////////////////////////////////////
 class FieldSelector extends Field
 {
 	var $object_it, $object;
	
	//-----------------------------------------------------------------------------------------------
	function FieldSelector( $object_it ) {
		$this->object_it = $object_it;
		$this->object = $object_it->object;
	}
	
	//-----------------------------------------------------------------------------------------------
	function getSelectorUrl() {}
	
	//-----------------------------------------------------------------------------------------------
	function IsNeedToClear() {
		return true;
	}
	
	//-----------------------------------------------------------------------------------------------
 	function draw()
	{
		if($this->value != '') {
			$displayName = $this->object_it->getDisplayName();
			$navigate_url = $this->object_it->getViewUrl();
		}
		?>
		<script language="javascript">
			function select<? echo $this->name; ?>()
			{
				var left = window.screenLeft + 300;
				var top = window.screenTop + 50;
				window.open( '<? echo $this->getSelectorUrl(); ?>', 
						     '_blank', 'width=500,height=400,left='+left+',top='+top+',scrollbars=yes,toolbar=no,menubar=no,location=no,status=yes,resizable=yes' );
				return false;
			}
			function clear<? echo $this->name; ?>()
			{
				document.all('<? echo $this->name.'Name'; ?>').value = ''; 
				document.all('<? echo $this->name; ?>').value = '';
				return false;
			}
			function goto<? echo $this->name; ?>()
			{
				if(typeof window.navigate != 'undefined') {
					window.navigate("<? echo $navigate_url ?>", "_self");
				} else {
					window.location = "<? echo $navigate_url ?>";
				}
				return false;
			}
		</script>
	
		<input type="hidden" id="<? echo $this->name; ?>" name="<? echo $this->name; ?>" value="<? echo $this->value; ?>">
		<table width=100% cellpadding=0 cellspacing=0>
			<tr>
				<td style="padding-right:3pt;">
					<input style="width:100%" disabled id="<? echo $this->name.'Name'; ?>" value="<? echo $displayName; ?>">
				</td>
				<? 
					if (!$this->readonly) 
					{
				?>
				<td width=23>
					<button title="<? echo_lang('Выбрать') ?>" style="font-family:verdana;font-size:8pt;border-style:groove;border:.5pt solid #d5d5df;background:#f5f5f5;margin-top:-1px;width:21px;height:21px;"
							onclick="return select<? echo $this->name; ?>();">...</button>
				</td>
				<? 	if($this->IsNeedToClear()) { ?>
				<td width=23>
					<button title="<? echo_lang('Очистить') ?>" style="font-family:verdana;font-size:8pt;border-style:groove;border:.5pt solid #d5d5df;background:#f5f5f5;margin-top:-1px;width:21px;height:21px;"
							onclick="return clear<? echo $this->name; ?>();">X</button>
				</td>
				<? 	}
				
					} // $this->readonly
					
					if($this->value != '')
					{
				?>
				<td width=23>
					<button title="<? echo_lang('Перейти к объекту') ?>" style="font-family:verdana;font-size:8pt;border-style:groove;border:.5pt solid #d5d5df;background:#f5f5f5;margin-top:-1px;width:21px;height:21px;"
							onclick="return goto<? echo $this->name; ?>();">></button>
				</td>
				<?
					}
				?>
			</tr>
		</table>
		<?
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////
 class FieldListSelector extends FieldSelector
 {
	function getSelectorUrl() {
		if(strtolower(get_class($this->object_it->object)) == 'metaobject') {
			return 'selector.php?kind=listselector&class=metaobject&entity='.$this->object_it->object->getClassName().'&field='.$this->name;
		} 
		else {
			return 'selector.php?kind=listselector&class='.get_class($this->object_it->object).'&field='.$this->name;
		}
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////
 class FieldTreeSelector extends FieldSelector
 {
	function getSelectorUrl() {
		if(strtolower(get_class($this->object)) == 'metaobject') 
			return 'selector.php?kind=treeselector&class=metaobject&entity='.$this->object->getClassName().'&field='.$this->name;
		else
			return 'selector.php?kind=treeselector&class='.get_class($this->object).'&field='.$this->name;
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////
 class Selector
 {
 	var $object;
	var $field;

	//-----------------------------------------------------------------------------------------------
 	function Selector( $object, $field )
	{
		$this->object = $object;
		$this->field = $field;

		header('Content-type: text/html; charset=windows-1251');
	?>
		<html>
		<script language="javascript">
			function select<? echo $field; ?>( id, name ) {
				window.opener.document.all('<? echo $field; ?>').value = id;
				window.opener.document.all('<? echo $field.'Name'; ?>').value = name;
				window.close();
			}
		</script>
		<title>Выбор объекта</title>
		<body>
    		<table cellpadding=0 cellspacing=0 width=100% height=100%>
    			<tr><td height=10>
    				<? $this->drawHeader(); ?>
    			</td></tr>
    			<tr><td height=10>
					<? $this->drawBody(); ?>
    			</td></tr>
    			<tr><td >
    				<? $this->drawFooter(); ?>
    			</td></tr>
    		</table>
		</body>
		</html>
	<?
	}
	
	//-----------------------------------------------------------------------------------------------
	function drawItem( $objectid, $objectname )
	{
	?>
		<a href="javascript: select<? echo $this->field; ?>('<? echo $objectid; ?>', '<? echo $objectname; ?>');">
        	<? echo $objectname; ?>
        </a>
	<?
	}

	function getUrl() {}
	function drawHeader() {}
	function drawBody() {}
	function drawFooter() {}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////
 class ListSelector extends Selector
 {
	var $maxonpage = 10;
	var $it;
	
 	function ListSelector( $object, $field )
	{
		global $_REQUEST;
		$offset = $_REQUEST['offset'];
		$search = $_REQUEST['search'];
		
		if($search != '') {
    		$this->it = $object->getLike($search);
		}
		else {
    		$this->it = $object->getAll();
		}
    	$this->it->moveToPos( $offset );
		
		parent::Selector( $object, $field );
	}

	//-----------------------------------------------------------------------------------------------
	function getUrl() {
		global $_REQUEST;
		$search = $_REQUEST['search'];
		if(strtolower(get_class($this->object)) == 'metaobject') {
			return 'selector.php?class=metaobject&entity='.$this->object->getClassName().'&kind='.get_class($this).'&field='.$this->field.'&search='.$search;
		} 
		else {
			return 'selector.php?class='.get_class($this->object).'&kind='.get_class($this).'&field='.$this->field.'&search='.$search;
		}
		
	}
	
	//-----------------------------------------------------------------------------------------------
	function drawHeader()
	{
		global $_REQUEST;
		$search = $_REQUEST['search'];
	?>
		<table width=100%>
			<tr>
				<td height=30 style="padding:4pt;">
					<table width=100%>
						<tr>
							<td>
            					<form action="<? echo $this->getUrl(); ?>" method=post>
                					<table width=100%>
                						<tr>
                							<td width=45>Поиск: </td>
                							<td><input name="search" style="width:100%" value="<? echo $search; ?>"/></td>
                							<td width=44><input type="submit" style="width:40pt;" value="Найти"/></td>
                						</tr>
                					</table>
            					</form>
							</td>
							<td width=20>
            					<form action="<? echo $this->getUrl(); ?>" method=post>
        							<input type="hidden" name="search" value="">
        							<input title="Сбросить" style="width:16pt;" type="submit" value="X">
            					</form>
							</td>
						</tr>
					</table>
				</td>
				<td></td>
				<td align=right>
					<table><tr>
						<td>Страницы: </td>
						<? $this->drawNavigator( $this->it ); ?>
						<td></td>
					</tr></table>
				</td>
			</tr>
		</table>
	<?
	}
	
	//-----------------------------------------------------------------------------------------------
	function drawBody() 
	{
		global $_REQUEST;
		$offset = $_REQUEST['offset'];
	?>
        <table width=100%>
			<tr>
				<td width=20 style="background:#eaeaea;border:.5pt solid #d5d5df;padding:4pt;">№</td>
				<td style="background:#eaeaea;border:.5pt solid #d5d5df;padding:4pt;">Название</td>
			</tr>
        <?
			for($i=0; $i < min($this->it->count() - $offset, $this->maxonpage); $i++)
			{
        	?>
        	<tr>
				<td align=right style="background:#eaeaea;border:.5pt solid #d5d5df;padding:4pt;"><? echo $offset + $i + 1; ?></td>
        		<td style="padding-left:4pt;border:.5pt solid #d5d5df;padding:4pt;">
					<? $this->drawItem( $this->it->getId(), $this->it->getDisplayName() ); ?>
        		</td>
        	</tr>
        	<?
           		$this->it->moveNext();
        	}					
        ?>			
        </table>
	<?	
	}
	
	//-----------------------------------------------------------------------------------------------
	function drawNavigator( $object_it )
	{
		global $_REQUEST;
		$offset = $_REQUEST['offset'];
		$freshnews = 0;
		// название страницы
		$pagename = $this->getUrl(); //'c_selector.php?class='.$this->object->getClassName().'&kind=listselector&field='.$this->field;
		// общее число страниц
        $pages = ($object_it->count() - $freshnews) / $this->maxonpage;
		// выводим номера страниц
        for ($i = 0; $i < $pages; $i++)
        {
			$pageurl = $pagename.(strpos($pagename, '?') > 0 ? '&' : '?').'offset='.($i * $this->maxonpage + $freshnews);
        	$current = $i * $this->maxonpage == ($offset - $freshnews);
	       	if($current) 
				echo '<td class=pagenum align=center width=16><b><a href="'.$pageurl.'">'.($i+1).'</a></b></td>';
        	else 
				echo '<td class=pagenum align=center width=16><a href="'.$pageurl.'">'.($i+1).'</a></td>';
        }
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////
 class TreeSelector extends ListSelector
 {
 	function drawBody()
	{
		global $_REQUEST;
		$offset = $_REQUEST['offset'];
		$search = $_REQUEST['search'];
		
		if($search != '' ) {
			parent::drawBody();
			return;
		}
	?>
		<script>
			function toggleTreeItem( name )
			{
				var l = document.getElementById(name);
				var i = document.getElementById('img'+name);
			  	if(l.style.display == 'none') {
			  		l.style.display = 'block';
					i.src = '/images/treeminus.png';
			  	} else {
			  		l.style.display = 'none';
					i.src = '/images/treeplus.png';
			  	}			
			}
		</script>
        <table width=100% cellpadding=0 cellspacing=0 style="border-collapse:collapse;">
			<tr>
				<td>
					<table cellpadding=0 cellspacing=0 width=100% style="border-collapse:collapse;">
						<tr>
							<td width=20 style="background:#eaeaea;border:.5pt solid #d5d5df;padding:4pt;"></td>
							<td style="background:#eaeaea;border:.5pt solid #d5d5df;padding:4pt;">Название</td>
						</tr>
					</table>
				</td>
			</tr>
        <?
            for($i=0; $i < min($this->it->count() - $offset, $this->maxonpage); $i++)
			{
            	$this->drawTreeItem( $i, $this->it, 0 );
            	$this->it->moveNext();
            }
        ?>
        </table>
	<?	
	}
	
	function drawTreeItem( $i, $it, $levelnum )
	{
		static $layer_id = 0;
		$children = $it->getChildren();
		
		$layer = "level_".$layer_id;
		$layer_id++;
	?>
      	<tr>
			<td>
				<table cellpadding=0 cellspacing=0 width=100% style="border-collapse:collapse;">
					<tr>
						<td width=20 align=right style="background:#eaeaea;border:.5pt solid #d5d5df;padding:4pt;border-top:none;">
						</td>
            			<td width="<? echo (($levelnum+1)*15); ?>" valign=top align=right style="padding-top:5pt;border-bottom:.5pt solid #d5d5df;">
            			<?
            				if( $children->count() > 0)
            				{
            			?>
            				<a href="javascript: { toggleTreeItem ('<? echo $layer; ?>'); }">
            					<img id="<? echo 'img'.$layer; ?>" border=0 src="<? echo '/images/treeplus.png'; ?>">
            				</a>
            			<?
            				}
            			?>
            			</td>
                   		<td style="padding-left:4pt;border:.5pt solid #d5d5df;padding:4pt;border-left:none;border-top:none;">
            				<? $this->drawItem( $it->getId(), $it->getDisplayName() ); ?>
                   		</td>
					</tr>
				</table>
			</td>
       	</tr>
		<tr id="<? echo $layer; ?>" style="display:none;">
			<td>
				<table width=100% cellpadding=0 cellspacing=0 style="margin-top:-1pt;border-collapse:collapse;">
            	<?
            		for( $j = 0; $j < $children->count(); $j++) 
					{
						//echo $children->getId();
            			$this->drawTreeItem( $j, $children, $levelnum + 1 );
						//echo $children->getId();
            			$children->moveNext();
            		}
            	?>
				</table>
			</td>
		</tr>
	<?
	}
 }
 
?>