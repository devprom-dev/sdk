<?php

include_once "WikiEditorBase.php";

include_once SERVER_ROOT_PATH."pm/views/wiki/parsers/WikiParser.php";
include_once SERVER_ROOT_PATH."pm/views/wiki/parsers/WikiHtmlParser.php";

class WikiSyntaxEditor extends WikiEditorBase
{
 	function getDisplayName()
 	{
 		return text(814);
 	}

 	function hasHelpSection()
 	{
 		return true;
 	}

 	function getEditorParser()
 	{
 		return null;
 	}

 	function getPageParser()
 	{
 		return new WikiParser( $this->getObjectIt() );
 	}

 	function getHtmlParser()
 	{
 		return new WikiHtmlParser( $this->getObjectIt() );
 	}

 	function getHtmlSelfSufficientParser()
 	{
 		return new WikiHtmlSelfSufficientParser( $this->getObjectIt() );
 	}
 	
 	function getComparerParser()
 	{
 		return $this->getHtmlParser();
 	}

 	function draw( $content, $b_editable = false )
 	{
 		if ( !$b_editable )
 		{
			echo '<div class="readonly" id="'.$this->getFieldId().'" attributeName="'.$this->getFieldName().'">';
				echo ($content == '' ? ' &nbsp; ' : $content);
			echo '</div>';
			
			return;
 		}
 		
 		$object_it = $this->getObjectIt();
 		
 		$base_url = getSession()->getApplicationUrl();
 		
 		if ( is_object($object_it) )
 		{
 			$page_id = $object_it->getId();
 			switch( $object_it->object->getClassName() )
 			{
 				case 'WikiPage':
 					$preview_url  = $base_url.'preview.php?wiki_id='.$page_id;
 					break;

 				case 'BlogPost':
 					$preview_url  = $base_url.'preview.php?post_id='.$page_id;
 					break;
 			}
 		}
 		else
 		{
 			$preview_url  = $base_url.'preview.php';
 		}
 		
 		$rows = $this->getMode() & WIKI_MODE_MINIMAL 
 		    ? $this->getMinRows() : $this->getMaxRows();

		?>
		<script type="text/javascript" src="/scripts/markitup/jquery.markitup.js"></script>
		<link rel="stylesheet" type="text/css" href="/scripts/markitup/skins/markitup/style.css?v=<? echo $_SERVER['APP_VERSION'] ?>" />
		<link rel="stylesheet" type="text/css" href="/scripts/markitup/sets/wiki/style.css?v=<? echo $_SERVER['APP_VERSION'] ?>" />
		
		<script type="text/javascript" >
			function countLineBreaks()
			{
				var area = document.getElementById('<? echo $this->getFieldId(); ?>');
				if ( !area ) return;

				textAreaWith = area.clientWidth == 0 ? area.offsetWidth : area.clientWidth;
				nCols = Math.ceil(textAreaWith / 6.7);

     			lines = area.value.split('\n');
     			nRowCnt = lines.length; 
			
				for(i=0;i<lines.length;i++) {
					wrapLine = Math.ceil(lines[i].length / nCols);
					if(wrapLine > 1) nRowCnt += (wrapLine - 1); 
				}
				
				if(nRowCnt < <?php echo $rows ?>) nRowCnt = <?php echo $rows ?>;
				if (typeof ActiveXObject != 'undefined') {
					nRowCnt += 7;
				}
				return nRowCnt;
			} 

			function adjustRows () 
			{
				var area = document.getElementById('<? echo $this->getFieldId(); ?>');
				area.rows = countLineBreaks() + 1;
			}

			<?php  if ( $this->getMode() & WIKI_MODE_MINIMAL ) { ?>
			mySettings = {
			    resizeHandle: false,
				onShiftEnter: {keepDefault:false, replaceWith:'\n\n'},
				markupSet: [
						{name:'Heading 1', key:'1', openWith:'h1 ', closeWith:'' },
						{name:'Heading 2', key:'2', openWith:'h2 ', closeWith:'' },
						{name:'Heading 3', key:'3', openWith:'h3 ', closeWith:'' },
						{name:'Heading 4', key:'4', openWith:'h4 ', closeWith:'' },
						{name:'Heading 5', key:'5', openWith:'h5 ', closeWith:'' },
						{name:'Heading 6', key:'6', openWith:'h6 ', closeWith:'' },
						{separator:'---------------' },		
						{name:'Bold', key:'B', openWith:" *", closeWith:"* "}, 
						{name:'Italic', key:'I', openWith:" _", closeWith:"_ "}, 
						{name:'Stroke through', key:'S', openWith:' -', closeWith:'- '}, 
						{separator:'---------------' },
						{name:'Bulleted list', openWith:'(!(* |!|*)!)'}, 
						{name:'Numeric list', openWith:'(!(# |!|#)!)'}, 
						{separator:'---------------' },
						{name:'Link', key:"L", openWith:"[page= text=", closeWith:"]"}, 
						{name:'Url', openWith:"[url= text=", closeWith:"]"},
						{separator:'---------------' },
						{name:'Note', openWith:'[note=', closeWith:']', placeHolder:''},
						{name:'Code', openWith:'[code]', closeWith:'[/code]'}, 
				]
			};
			<?php } else { ?>
			mySettings = {
				    resizeHandle: false,
					previewInWindow: '',
					previewParserPath:	'<? echo $preview_url; ?>', // path to your Wiki parser
					previewParserVar: 'content',
					onShiftEnter: {keepDefault:false, replaceWith:'\n\n'},
					markupSet: [
						{name:'Heading 1', key:'1', openWith:'h1 ', closeWith:'' },
						{name:'Heading 2', key:'2', openWith:'h2 ', closeWith:'' },
						{name:'Heading 3', key:'3', openWith:'h3 ', closeWith:'' },
						{name:'Heading 4', key:'4', openWith:'h4 ', closeWith:'' },
						{name:'Heading 5', key:'5', openWith:'h5 ', closeWith:'' },
						{name:'Heading 6', key:'6', openWith:'h6 ', closeWith:'' },
						{separator:'---------------' },		
						{name:'Bold', key:'B', openWith:" *", closeWith:"* "}, 
						{name:'Italic', key:'I', openWith:" _", closeWith:"_ "}, 
						{name:'Stroke through', key:'S', openWith:' -', closeWith:'- '}, 
						{separator:'---------------' },
						{name:'Bulleted list', openWith:'(!(* |!|*)!)'}, 
						{name:'Numeric list', openWith:'(!(# |!|#)!)'}, 
						{separator:'---------------' },
						{name:'Link', key:"L", openWith:"[page= text=", closeWith:"]"}, 
						{name:'Url', openWith:"[url= text=", closeWith:"]"},
						{separator:'---------------' },
						{name:'Note', openWith:'[note=', closeWith:']', placeHolder:''},
						{name:'Code', openWith:'[code]', closeWith:'[/code]'}, 
						{separator:'---------------' },
						{name:'Preview', key:"P", call:'preview', className:'preview'}
					]
				};
			<?php } ?>
		
		   var ctrlKey = false;
		   
 		   function customPreview()
 		   {
 		   		$(this).preview();
 		   }
 		   
 		   function makeClickableFiles()
 		   {
 				$('div.embeddedRow a.modify_image').each(function() {
 					$(this).attr('title', "<? echo text(817) ?>");
 					$(this).click(function() 
 					{
 						$('#<? echo $this->getFieldId() ?>').focus();
 					    var title = $(this).attr('name').replace(/\[/,"(").replace(/\]/,")");
	 					$.markItUp({openWith:'[image='+title+' width=50%]',closeWith:''});
	 					return false;
 					});
 				});
		   }
			function editorFocus()
 		   {
			   $('#<? echo $this->getFieldId() ?>').focus();
 		   }

		   $(document).ready(function() 
		   {
		      	$('#<? echo $this->getFieldId() ?>').markItUp(mySettings);

		      	$('.preview').find('a').bind('mousedown', function(){
		      		setTimeout( function(){
						window.scrollTo( 0, $('.markItUpPreviewFrame').position().top );
		      		}, 200);
		      	});
		      	makeClickableFiles();
		   });

		   <?php  if ( !($this->getMode() & WIKI_MODE_MINIMAL) ) { ?>
		   window.setInterval('adjustRows()', 500, 'javascript');
		   <?php } ?>
		</script>
		<?
		
		echo '<div class="readonly">';
			echo '<textarea class="reset" tabindex="'.$this->getTabIndex().'" style="overflow:hidden;width:100%" id="'.
				$this->getFieldId().'" rows='.$rows.' name="'.$this->getFieldName().'" '.($this->getRequired() ? 'required' : '').' >'.$content.'</textarea>';
		echo '</div>';
				
		$field = $this->getAttachmentsField();
		if ( is_object($field) )
		{	
		    $field->setEditMode( true );
		    $field->setReadonly( false );
		    
			$form = $field->getForm();
			
 			$form->setAddButtonText( text(1317) );
			
 			echo '<div style="margin:0 0 6px 0;" class="formvalueholder">';
				$field->draw();
			echo '</div>';
			
			$form->drawScripts();
		}
 	}

	function drawPreviewButton()
	{
		global $tabindex;
		$tabindex++;
		
		$script = "javascript: $('.preview').find('a').trigger('mousedown');";
		
		echo '<input class="btn btn-primary" tabindex="'.$tabindex.'" style="float:left;margin-right:18px;" onclick="'.$script.'" type="button" id="previewbtn" title="'.
			text(1509).'" value="'.translate('Просмотр').'">';
	}
	
	function getDescription()
	{
		ob_start();
		
		echo str_replace('%1', '<a href="http://devprom.ru/docs#2474">'.text(1165).'</a>', text(1264));
		echo parent::getDescription();
		
		$result = ob_get_contents();
		
		ob_end_clean();
		
		return $result;
	}
	
	function drawHelpSection()
 	{
 		parent::drawHelpSection();
 		
 		echo '<tr><td class="wiki_sub" height=15 valign=middle>'.translate('Дополнительно').'</td></tr>';
 		echo '<tr><td style="padding-top:6px;"><a href="http://devprom.ru/docs#2474">'.text(1165).'</a></td></tr>';
 	}
 	
 	public function getExportActions( $object_it )
 	{
 		$actions = array();
 		
		$method = new WikiExportRtfWebMethod();
		
		$actions[] = array( 
			'name' => $method->getCaption(), 
			'url' => $method->url( $object_it )
		);
 		
		return $actions;
 	}
}