<?php

include_once "Field.php";

class FieldDictionary extends Field
{
 	var $object, $style, $displayhelp, $options, $null_option;
	
 	private $translate_options = false;
 	private $null_title = '';
 	private $objectIt = null;
 	private $titleRequired = '';
 	private $multiple = false;
 	private $attributes = array();
 	
	function FieldDictionary( $object )
	{
	    if ( $object instanceof OrderedIterator ) {
            $this->object = $object->object;
            $this->objectIt = $object;
        }
        else {
            $this->object = $object;
        }

		$this->style = '';
		$this->null_option = true;
		
		$this->displayhelp = is_object($this->object) && 
			(is_a($this->object, 'Metaobject') || is_subclass_of($this->object, 'Metaobject')) && 
			$this->object->getAttributeType('Description') != '' &&
			$this->object->IsDictionary();
		
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

	function setNullTitle( $value ) {
	    $this->null_title = $value;
    }

    function setTitleRequired( $value ) {
	    $this->titleRequired = $value;
    }

    function setMultiple( $value ) {
	    $this->multiple = $value;
    }

    function getMultiple() {
	    return $this->multiple;
    }

	function translateOptions( $translate = true )
	{
		$this->translate_options = $translate;
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

	function setAttributes( $array ) {
	    $this->attributes = $array;
    }

	function getText()
	{
		$value = $this->getValue();
		if ( $value == '' ) return '';

		$items = \TextUtils::parseItems($value);
        $title = array();
	    foreach( $this->getOptions() as $option ) {
	        if ( in_array($option['value'], $items) ) $title[] = $option['caption'];
	    }
	    if ( count($title) > 0 ) return join(', ', $title);

	    $valueIt = $this->object->getExact($this->getValue());
	    if ( $valueIt->getId() == '' ) {
	        $registry = new ObjectRegistrySQL($this->object);
	        $valueIt = $registry->Query(
                array(
                    new FilterInPredicate($value)
                )
            );
        }

		$uid = new ObjectUID;
	    return $uid->getUidTitle($valueIt);
	}
	
	function getOptions()
	{
	    $options = array();
	    
		$uid = new ObjectUID;

        if ( !is_object($this->objectIt) ) {
            if ( count($this->object->getVpds()) > 1 ) {
                $this->objectIt = $this->object->getRegistry()->Query(
                    array(
                        new FilterBaseVpdPredicate()
                    )
                );
            }
            else {
                $this->objectIt = $this->object->getAll();
            }
        }
        $entity_it = $this->objectIt;
        $entity_it->moveFirst();

		while( !$entity_it->end() )
		{
			$title = $uid->getUidTitle($entity_it);
			
		    $options[] = array (
                'value' => $entity_it->getId(),
                'referenceName' => $entity_it->get('ReferenceName'),
                'caption' => $this->translate_options ? translate($title) : $title,
                'disabled' => false
            );

		    $entity_it->moveNext();
		}
		
		return $options;
	}
	
	function getGroups()
	{
		return array( '' => '' );
	}
	
 	function draw( $view = null )
	{
		global $tabindex;

		if ( $this->readOnly() ) {
            echo $this->getText();
			return;
		}

		$tab_index = $this->getTabIndex() > 0 ? $this->getTabIndex() : $tabindex;

		$groups = $this->getGroups();
		$this->options = $this->getOptions();

		$hasNullOption = false;
		foreach( $this->options as $option )
		{
		    if ( $option['value'] == '' ) $hasNullOption = true;

		    $groupKey = $option['group'];
			if ( !array_key_exists($groupKey,$groups) ) $groupKey = '';
            if ( !array_key_exists('items', $groups[$groupKey]) ) {
                $groups[$groupKey] = array_merge(
                    $groups[$groupKey],
                    array(
                        'items' => array()
                    )
                );
            }

		    $groups[$groupKey]['items'][] = $option;
		}

		if ( $this->titleRequired != '' ) {
		    echo '<div style="padding-bottom: 5px;">';
		        echo $this->titleRequired;
            echo '</div>';
        }
		?>
		<select class="dictionary input-block-level"
                tabindex="<? echo $tab_index ?>"
                onchange="<?php echo $this->script ?>"
                style="<? echo $this->style ?>"
                name="<? echo ($this->getMultiple() ? $this->getName().'[]' : $this->getName() ); ?>"
                id="<? echo $this->getId(); ?>" <?=($this->getRequired() ? 'required' : '')?> <?=($this->getMultiple() ? 'multiple' : '')?>
                default="<?=htmlentities($this->getDefault())?>"
                <?=join(' ', $this->attributes)?> >

		<?php if ( !$this->getMultiple() && $this->null_option && !$hasNullOption ) { ?>
			<option value="" referenceName=""><?=$this->null_title?></option>
			<?php } ?>
			<?
			$valueinlist = false;
			$values = \TextUtils::parseItems($this->getValue());

			foreach( $groups as $group )
			{
				if ( !is_array($group) ) continue;
				if ( !is_array($group['items']) ) continue;

				if ( $group['label'] != '' ) echo '<optgroup label="'.$group['label'].'">';
				foreach( $group['items'] as $option )
				{
					$selected = (in_array($option['value'],$values) || count($values) < 1 && $option['value'] == '' || count($this->options) == 1 && $this->getRequired()) ? 'selected ' : '';
					
					?>
						<option value="<? echo $option['value']; ?>" <? echo $selected; ?> referenceName="<?=$option['referenceName']?>" <?=($option['disabled'] ? 'disabled' : '')?> ><?=$option['caption']?></option>
					<?
					
					if ( $selected || in_array($option['value'],$values) )
					{
						$valueinlist = true;
					}
				}
				if ( $group['label'] != '' ) echo '</optgroup>';
			}
			
			if ( !$valueinlist && $this->getValue() != '' )
			{
				echo '<option value="'.$this->getValue().'" selected >'.$this->getText().'</option>';
			}
			?>
		</select>
		<?
	}
	
	function drawHelpButton()
	{
	}
	
	function setScript( $script )
	{
		$this->script = $script;
	}
}