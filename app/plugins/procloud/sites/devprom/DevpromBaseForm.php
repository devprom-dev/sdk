<?php

class DevpromBaseForm extends CoPageForm
{
	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function IsAttributeRequired( $attribute )
	{
		return false;
	}
	
	function draw()
	{
		$form_processor_url = '/command/'.$this->getCommandClass();
	
		$this->drawScript();
	?>
<style>
#loginform .title
{
	padding: 24px 24px 0 24px;
}

#loginform .field
{
	font-size:16px;
}

#loginform .value
{
	padding-bottom:12px;
}

#loginform .error
{
	padding-bottom:12px;
	color: red;
}

#loginform textarea
{
	width: 100%;
}

#loginform #close
{
	text-decoration:none;
}

#loginform #submit
{
	text-decoration:none;
}

#loginform #buttons #body
{
	background-image: url(/style/images/toanswer.png);
	padding:5px 12px 5px 12px;
	width: 78px;
	height: 18px;
}

#loginform .required
{
	font-weight: bold;
	color: red;
}
</style>
		<?php 
		echo '<div>';
			echo '<form id="myForm" action="'.$form_processor_url.'" method="post" style="width:100%;" onsubmit="javascript: return false;">';
				echo '<input type="hidden" id="action" name="action" value="'.$this->getAction().'">';
				echo '<input type="hidden" name="MAX_FILE_SIZE" value="1048576">';
				echo '<input type="hidden" id="lru" name="lru" value="">';
				echo '<input type="hidden" id="lrs" name="lrs" value="">';
				echo '<table style="width:100%;">';
				$attributes = $this->getAttributes();
		
				for ( $i = 0; $i < count($attributes); $i++ )
				{
					$this->drawAttribute( $attributes[$i] );
				}
				echo '</table>';
			echo '</form>';
			
			echo '<div id="result" style="clear:both;padding-bottom:12px;"></div>';

			echo '<div id="buttons" style="width:100%;">';
				echo '<div class="blackbutton" id="submitbutton" style="padding-right:12px;">';
					echo '<div id="body">';
						echo '<a class="btn btn-success" id="submit" href="javascript: '.$this->getSubmitScript().'">';
							echo $this->getButtonText();
						echo '</a>';
					echo '</div>';
					echo '<div id="rt"></div>';
				echo '</div>';
	
				echo '<div class="blackbutton" id="closebutton">';
					echo '<div id="body">';
						echo '<a class="btn btn-danger" id="close" href="javascript: closeLoginForm();">'.translate('Закрыть').'</a>';
					echo '</div>';
					echo '<div id="rt"></div>';
				echo '</div>';
			echo '</div>';
				
			echo '<div style="clear:both;"></div>';

		echo '</div>';
		
		?>
		<script type="text/javascript">
			$('#fileName').html($('#loginRedirectUrl').val());
			
			$('#loginform textarea').each( function() {
				$(this).attr('rows', 3);
			});
		</script>
		<?php 
	}

	function drawTitle()
	{
	}
	
	function getSubmitScript()
	{
		return 'submitForm(\''.$this->getAction().'\', refreshWindow)';
	}
}
