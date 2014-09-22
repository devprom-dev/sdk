<?php

include_once "Field.php";

class FieldDictionary extends Field
{
 	var $object, $style, $displayhelp, $options, $null_option;
	
	function FieldDictionary( & $object ) 
	{
		$this->object = $object;
		
		$this->style = '';
		$this->null_option = true;
		
		$this->options = $this->getOptions();

		$this->displayhelp = is_object($this->object) && 
			(is_a($this->object, 'Metaobject') || is_subclass_of($this->object, 'Metaobject')) && 
			$this->object->getAttributeType('Description') != '' &&
			$this->object->entity->get('IsDictionary') == 'Y';
		
		parent::Field();
	}
	
	function setStyle( $style )
	{
		$this->style = $style;
	}
	
	function setNullOption( $enabled )
	{
	    $this->null_option = $enabled;
	}
	
	function hideHelp()
	{
		$this->displayhelp = false;
	}
	
	function displayHelp()
	{
		return $this->displayhelp && 
			( $this->readOnly() && $this->getValue() != '' || !$this->readOnly());
	}
	
	function getObject()
	{
		return $this->object;
	}
	
	function getStyle()
	{
		return $this->style;
	}
	
	function readOnly()
	{
	    return !$this->getEditMode() || parent::readOnly();
	}
	
	function getText()
	{
		$value = $this->getValue();
	    
		if ( $value == '' ) return '';
		
		$options = $this->getOptions();
	    
	    foreach( $options as $option )
	    {
	        if ( $value == $option['value'] ) return $option['caption']; 
	    }
	    
		$uid = new ObjectUID;
		
	    return $uid->getUidTitle($this->object->getExact($this->getValue()));
	}
	
	function getOptions()
	{
	    $options = array();
	    
		$uid = new ObjectUID;
		
		$entity_it = $this->object->getAll();

		while( !$entity_it->end() )
		{
		    $options[] = array (
                'value' => $entity_it->getId(),
                'referenceName' => $entity_it->get('ReferenceName'),
                'caption' => $uid->getUidTitle($entity_it),
                'disabled' => false
            );

		    $entity_it->moveNext();
		}
		
		return $options;
	}
	
 	function draw()
	{
		global $tabindex, $model_factory;

		if ( $this->readOnly() )
		{
			echo '<input type="hidden" id="'.$this->getId().'" name="'.$this->getName().'" value="'.$this->getValue().'">';
			echo '<input class="input-block-level" type="text" disabled value="'.$this->getText().'">';
		}
		else
		{
			$tab_index = $this->getTabIndex() > 0 ? $this->getTabIndex() : $tabindex;
		?>
		<select class="input-block-level" tabindex="<? echo $tab_index ?>" onchange="<?php echo $this->script ?>" style="<? echo $this->style ?>" name="<? echo $this->getName(); ?>" id="<? echo $this->getId(); ?>" <?=($this->getRequired() ? 'required' : '')?> >
		<?php if ( $this->null_option ) { ?>
			<option value="" referenceName=""></option>
			<?php } ?>
			<?
			$valueinlist = false;
			
			foreach( $this->options as $key => $option )
			{
				$selected = ($option['value'] == $this->getValue() && $this->getValue() != '' || count($this->options) == 1 && $this->getRequired()) ? 'selected ' : '';
				
				?>
					<option value="<? echo $option['value']; ?>" <? echo $selected; ?> referenceName="<?=$option['referenceName']?>" <?=($option['disabled'] ? 'disabled' : '')?> ><?=$option['caption']?></option>
				<?
				
				if ( $selected )
				{
					$valueinlist = true;
				}
			}
			
			if ( !$valueinlist && $this->getValue() != '' )
			{
				echo '<option value="'.$this->getValue().'" selected >'.$this->getText().'</option>';
			}
			?>
		</select>
		<?
		}
	}
	
	function drawHelpButton()
	{
	}
	
	function setScript( $script )
	{
		$this->script = $script;
	}
}