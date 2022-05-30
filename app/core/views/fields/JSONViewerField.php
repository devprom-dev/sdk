<?php
include_once SERVER_ROOT_PATH."/cms/views/Field.php";

class JSONViewerField extends Field
{
    function render( $view )
    {
    	echo '<span class="input-block-level well well-text">';
    	echo $view->render(SERVER_ROOT_PATH . 'plugins/incidents/views/fields/templates/JSONViewerField.tpl.php',
        		array (
        				'json' => self::stripTags($this->getValue())
        		)
        	);
    	echo '</span>';
    }
    
    function draw(  $view = null  )
    {
    	echo $view->render('incidents/views/fields/templates/JSONViewerField.tpl.php', 
        		array (
        				'json' => self::stripTags($this->getValue())
        		)
        	);
    }
    
    public static function stripTags( $text )
    {
    	return preg_replace(
    			array (
    					'/<\/?p>/i',
    					'/<\/?div>/i'
    			),
    			array (
    					'',
    					''
    			),
    			html_entity_decode($text, ENT_COMPAT | ENT_HTML401, APP_ENCODING)
    		);
    }

    function contentEditable() {
        return false;
    }
}