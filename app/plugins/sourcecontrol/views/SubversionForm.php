<?php

include 'FieldConnectorsDictionary.php';
include "FieldSubversionUser.php";

class SubversionForm extends PMPageForm
{
    function IsAttributeVisible( $attr_name )
    {
        switch($attr_name)
        {
            case 'Project':
                return false;
        }
        	
        return parent::IsAttributeVisible( $attr_name );
    }

    function createFieldObject( $name )
    {
        switch ( $name )
        {
            case 'ConnectorClass':

            	return new FieldConnectorsDictionary( getFactory()->getObject('pm_Subversion') );

            case 'Users':
            	
            	return new FieldSubversionUser( $this->getObjectIt() );
            	
            default:
                
            	return parent::createFieldObject( $name );
        }
    }

    function getFieldDescription( $name )
    {
        $object = $this->getObject();
        $connectors = $object->getConnectors();
        	
        switch ( $name )
        {
            case 'SVNPassword':
                return text(399);

            case 'LoginName':
                return text('sourcecontrol18');

            case 'SVNPath':
            case 'RootPath':
                $text = '';
                foreach( $connectors as $connector ) {
                    $text .= '<span for-class="'.strtolower(get_class($connector)).'">'.$connector->getCredentialsParmDescription( $name ).'</span>';
                }
                return $text;
                
            default:
            	return parent::getFieldDescription( $name );
        }
    }

	function drawScripts()
	{
		parent::drawScripts();
		
        $object = $this->getObject();
        
        $connectors = $object->getConnectors();
		
        $messages = array();
        
        foreach( $connectors as $connector )
        {
        	$messages[strtolower(get_class($connector))] = IteratorBase::wintoutf8($connector->checkPrerequisites());  
        }
        
		?>
		<script type="text/javascript">
			var messages = <?=JsonWrapper::encode($messages)?>;

			function showDescriptions()
			{
				$('span[for-class]').hide();
				$('span[for-class="'+$('#pm_SubversionConnectorClass').val()+'"]').show();
			}
			
			$(document).ready(function()
			{
				showDescriptions();	
				
				$('#pm_SubversionConnectorClass').change(function() 
				{
					$('#pm_SubversionConnectorClass').next('.help-block').remove();
					
					if ( $('#pm_SubversionConnectorClass').val() == '' ) return;

					showDescriptions();
					
					message = messages[$('#pm_SubversionConnectorClass').val()];
					
					if ( typeof message == 'undefined' ) return;

					if ( message == "" ) return;

					$('#pm_SubversionConnectorClass')
						.parent().append('<span class="help-block"><div class="alert alert-error">'+message+'</div></span>');
				});
			});
		</script>
		<?php 
	}
}