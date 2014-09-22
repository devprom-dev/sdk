<?php

class DateFormatDictionary extends FieldDictionary
{
	function DateFormatDictionary()
	{
		global $model_factory;
		
		parent::FieldDictionary( $model_factory->getObject('cms_Language') );
	}

	function getOptions()
	{
	    $formats = array (
	            new DateFormatRussian(),
	            new DateFormatAmerican(),
	            new DateFormatEuropean()
	    );
	    
	    $options = array();
	    
	    foreach( $formats as $format )
	    {
	        $options[] = array(
	                'value' => get_class($format),
	                'caption' => $format->getDisplayName()
	        );
	    }
	    
	    return $options;
	}
	
	function draw2()
	{
		global $model_factory, $plugins;

		if ( $this->readOnly() )
		{
			$class_name = $this->getValue();
			if ( class_exists( $class_name ) )
			{
				$format = new $class_name;
				$title = $format->getDisplayName(); 				
			}
			else
			{
				$title = $class_name;
			}
			
			echo '<input class="readonly" readonly value="'.$title.'" style="width:100%">';
		}
		else
		{
			$items = array ( 
				new Language, 
				new LanguageEnglish 
			);

			echo '<select tabindex="'.$this->getTabIndex().'" style="width:100%;" name="'.$this->getName().'">';
				foreach( $items as $item )
				{
					$format = $item->getDefaultDateFormat();
					
					echo '<option value="'.get_class($format).'" '.(strtolower($this->getValue()) == strtolower(get_class($format)) ? 'selected' : '').' >';
						echo $format->getDisplayName();
					echo '</option>';
				}
			echo '</select>';
		}
	}
}
