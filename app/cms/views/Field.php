<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorTypeNull.php";

class Field
{
 	var $name;
	var $value;
	var $readonly;
	var $id;
	var $value_was_set;
	var $tabindex;
	var $text;
	var $default;
	var $edit_mode = true;
	var $required = false;
	
	function Field() 
	{
		global $tabindex;
		$tabindex += 1;
		//$this->value_was_set = false;
	}
	
	function draw( $view = null )
	{
	}

	function drawToolbar() {}
	
	function render( $view )
	{
        $this->draw( $view );
	}
	
	function drawScripts()
	{
	    
	}
	
	function setRequired( $required = true )
	{
	    $this->required = $required;    
	}
	
	function getRequired()
	{
	    return $this->required;
	}
	
	function setName( $name )
	{
		$this->name = $name;
	}
	
	function getName()
	{
		return $this->name;
	}
	
	function setText( $value )
	{
		$this->text = $value;
	}
	
	function getText()
	{
		if ( $this->text != '' )
		{
			return $this->text;
		}
		else
		{
			return self::getHtmlValue($this->getValue());
		}
	}

    static function getHtmlValue( $text )
    {
        $text = preg_replace('/&quot;/', '"', $text);
        $text = preg_replace_callback('/\[url=([^\]]+)\s+text=([^\]]+)]/im', array(self::class, iterator_url_callback), $text);
        $text = preg_replace_callback('/\[url=([^\]]+)]/im', array(self::class, iterator_url_callback), $text);

        $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
        $text = preg_replace($url, '<a href="$0" target="_blank">$0</a>', $text);

        $text = preg_replace_callback('/(^|[^\w\.\,\:\;\/\#">]+)(\[?[A-Z]{1}-[0-9]+\]?)([\s]*|$)/mi', array(self::class,iterator_uid_callback), $text);

        return nl2br($text);
    }

    function setDefault( $value )
	{
		$this->default = $value;
	}
	
	function getDefault()
	{
		return $this->default;
	}
	
	function setValue( $value )
	{
		$this->value = $value;
	}
	
	function getValue()
	{
		return $this->value == 'NULL' ? '' : $this->value;
	}
	
	function getEncodedValue()
	{
	    return htmlspecialchars(
	    			html_entity_decode($this->getValue(), ENT_QUOTES | ENT_HTML401, APP_ENCODING),
	    					ENT_COMPAT | ENT_HTML401, APP_ENCODING);
	}    
	
	function setReadOnly( $flag )
	{
		$this->readonly = $flag;
	}

	function readOnly()
	{
		return $this->readonly;
	}
	
	function setEditMode( $flag )
	{
	    $this->edit_mode = $flag;    
	}
	
	function getEditMode()
	{
	    return $this->edit_mode;
	}
	
	function setId( $value )
	{
		$this->id = $value;
	}
	
	function getId()
	{
		return $this->id;
	}
	
	function setTabIndex( $tab )
	{
		$this->tabindex = $tab;
	}
	
	function getTabIndex()
	{
		return $this->tabindex;
	}
	
	function getValidator()
	{
		return new ModelValidatorTypeNull();
	}

    static function iterator_url_callback( $match )
    {
        if ( strpos($match[1], '/') == 0 )
        {
            $match[1] = _getServerUrl().$match[1];
        }

        $text = $match[2] != '' ? $match[2] : $match[1];

        return '<a target="_blank" href="'.$match[1].'">'.$text.'</a>';
    }

    static function iterator_uid_callback( $match )
    {
        $uid = new ObjectUID;

        $object_it = $uid->getObjectIt(trim($match[2], '[]'));

        if ( is_object($object_it) && $object_it->count() > 0 )
        {
            $result = trim($match[1], '[]');

            $result .= $uid->getUidIconGlobal( $object_it );

            $result .= trim($match[3], '[]');

            return $result;
        }
        else
        {
            return $match[0];
        }
    }

    function getCssClass() {
        return '';
    }
}