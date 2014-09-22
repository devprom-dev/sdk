<?php

include_once "SelectWebMethod.php";

class SelectRefreshWebMethod extends SelectWebMethod
{
 	function SelectRefreshWebMethod()
 	{
 		$this->setType( '' );
 		parent::SelectWebMethod();
 	}
 	
 	function drawSelect( $parms_array, $default_value ) 
 	{
 		global $script_number;
 		$url = $this->getUrl( $parms_array );
 		
 		$script_number += 1;

 		$values = $this->getValues();
 		if ( count($values) < 1 )
 		{
 			return;
 		}
 		
 		$keys = array_keys($values);
		$style = $this->getStyle();
		
		$default_values = preg_split('/,/', $default_value);
		array_walk( $default_values, 'trim' );
		
		?>
	 	<select id=select_<? echo get_class($this).$script_number ?> name="<?php echo $this->getName(); ?>" class="<?php echo $this->getClass(); ?>" onclick="javascript: filterLocation.cancel();" url="<? echo $url ?>" onchange="javascript: selectRefreshMethod('<? echo $url ?>', '<? echo get_class($this).$script_number ?>')" style="<? echo $style ?>;<? echo ($default_value != '' ? 'background:#FBF7D3;' : '')?>" cardinality="<?php echo $this->getType() ?>">
	 	<?
	 		for($i = 0; $i < count($keys); $i++) {
	 			$checked = trim($keys[$i]) == trim($default_value) || in_array(trim($keys[$i]), $default_values) ? 'selected sticked="true"' : '';
	 			echo '<option value="'.$keys[$i].'" '.$checked.' >'.$values[$keys[$i]].'</option>';
	 		}
	 	?>
	 	</select>
		<?
 	}
 	
 	function getName()
 	{
 		return '';
 	}
 	
 	function setType( $type )
 	{
 		$this->type = $type;
 	}
 	
 	function getType()
 	{
 		return $this->type;
 	}
 	
 	function getClass()
 	{
 		return '';
 	}
 	
 	function getStyle()
 	{
 		return 'margin-left:1pt;padding-left:1pt;margin-top:2pt;';
 	}
}