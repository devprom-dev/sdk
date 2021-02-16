<?php

class EELDAPSelectRulesForm extends AdminForm
{
 	function getAddCaption()
 	{
 		return text(2760);
 	}
 	
 	function getCommandClass()
 	{
 		return 'ldapimport';
 	}

	function getAttributes()
	{
		return array( 'Rules' );
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'Rules':
				return '';
				
			case 'Info':
			    return text(2809);
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
		    case 'Info':
		        return 'text';
		        
			default:
				return 'text';
		}
	}

	function IsAttributeRequired( $attribute )
	{
		return false; 	
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function IsAttributeModifiable( $attribute )
	{
		return true;
	}

	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
			case 'Info':
				try
				{
					$logger = Logger::getLogger('LDAP');
					$appender = $logger->getAppender('LDAPFileAppender');
					
					if ( !is_object($appender) ) return '.';
					
					$file = fopen( $appender->getFileName(), 'r' );
					$content = fread( $file, filesize($appender->getFileName()) );
					fclose($file);
				
					return $content;
				}
				catch( Exception $e)
				{
					error_log('Unable initialize logger: '.$e->getMessage());
					
					return "";
				}
				
			default:
				parent::getAttributeValue( $attribute );
		}
	}
	
	function drawAttribute( $attribute, $view )
	{
		switch ( $attribute )
		{
			case 'Rules':
				$this->drawCatalogue(
                    array(
                        array (
                            'title' => LDAP_DOMAIN,
                            'folder' => true,
                            'key' => LDAP_DOMAIN,
                            'expanded' => false,
                            'lazy' => true,
                            'unselectable' => true
                        )
                    ),
                    '?export=true'
                );
				break;
				
			default:
				parent::drawAttribute( $attribute, $view );
		}
	}
	
	function drawCatalogue($data, $url)
	{
    	?>
        <tr><td class=value style="padding-bottom:20px;">
            <div id="treeview-title" style="padding-bottom:12px;">
                <p><?=text(2767)?></p>
                <a class="btn btn-light btn-xs" onclick="javascript: selectAll(true);"><?=translate('Выбрать все')?></a>
                <a class="btn btn-light btn-xs" onclick="javascript: selectAll(false);"><?=translate('Сбросить')?></a>
                <a class="btn btn-light btn-xs" onclick="javascript: expandAll();"><?=translate('Развернуть')?></a>
            </div>
            <div id="directory" data-type="json">
                <?=\JsonWrapper::encode($data)?>
            </div>
        </td></tr>
    	<script type="text/javascript">
    		$(function() {
                $('#directory').fancytree({
                    debugLevel: 0,
                    checkbox: true,
                    selectMode: 2,
                    lazyLoad: function(event, data) {
                        var node = data.node;
                        data.result = {
                            url: "<?=$url?>",
                            data: { lazyroot: node.key },
                            cache: false
                        };
                    },
                    init: function() {
                        $.ui.fancytree.getTree("#directory").expandAll();
                    },
                    strings: {
                        loading: "<?=text(1708)?>",
                        loadError: "<?=text(677)?>",
                        noData: "<?=text(2649)?>"
                    }
                });

                registerFormValidator('<?=$this->getId()?>', function() {
                    var form = $('#<?=$this->getId()?>');
                    form.find('input[dynamic]').remove();
                    var selNodes = $.ui.fancytree.getTree("#directory").getSelectedNodes();
                    $.each(selNodes, function(index, node) {
                        form.find('fieldset').append('<input type="hidden" dynamic name="nodes[]" value="' + node.key + '">');
                    });
                    return true;
                });
    		});

    		function selectAll(value) {
                $.ui.fancytree.getTree("#directory").selectAll(value);
            }
            function expandAll() {
                $.ui.fancytree.getTree("#directory").expandAll();
            }
    	</script>
    	<?
	}
	
	function getButtonText() {
		return translate('Импортировать');
	}
}
   