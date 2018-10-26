<?php

/////////////////////////////////////////////////////////////////////////////////
class CoMainContent extends CoPageContent
{
	function validate()
	{
		return true;
	}
	
	function draw()
	{
		global $model_factory, $user_it, $_REQUEST;
		
		$page = $this->getPage();
		$devprom_it = $page->getDevpromProjectIt();
		
		if ( $user_it->IsReal() )
		{
			$url_create_project = '/room/createproject';
			$url_create_site = '/room/createsite';
			$url_create_feedback = "/room/createfeedback";
		}
		else
		{
			$url_create_project = "javascript: getLoginForm('/room/createproject');";
			$url_create_site = "javascript: getLoginForm('/room/createsite');";
			$url_create_feedback = "javascript: getLoginForm('/room/createfeedback');";
		}
		
		// introduction
		echo '<div id="intro">';
		    $page->drawWhiteBoxBegin();
			
			echo '<div id="rightpanel" style="width:100%;">';
			    echo '<div class="section" id="function3" style="width:100%;">';

					echo '<div style="float:left;width:100%;">';
						echo '<br/>';
					echo '</div>';
					
					echo '<div class="description" id="desc1">';
					    echo '<div style="float:left;width:2%;">&nbsp;</div>';
					    echo '<div style="float:left;width:45%;">';
						    echo '<h1>Облачный сервис управления Agile проектами</h1>';
						    echo '<br/>';
						    echo str_replace('%1', $url_create_project, text('procloud539'));
						echo '</div>';
					    echo '<div style="float:left;width:5%;">&nbsp;</div>';
					    echo '<div style="float:left;width:45%;">';
					    
                    		echo '<div class="wb_header">';
                    			echo '<div id="tp">';
                    				echo '<div id="lt">';
                    				echo '</div>';
                    			echo '</div>';
                    		echo '</div>';
                    		echo '<div class="wb_body">';
                    			echo '<div id="md" style="min-height:420px;">';
    						        
                    			    echo '<div id="loginform" style="position:relative;border:none;"></div>';
                    			
                    			echo '</div>';
                    		echo '</div>';
                    		echo '<div class="wb_footer">';
                    			echo '<div id="bt">';
                    				echo '<div id="lt">';
                    				echo '</div>';
                    			echo '</div>';
                    		echo '</div>';
					    
					    echo '</div>';
				    echo '</div>';

					echo '<div style="float:left;width:100%;">';
						echo '<br/>';
						echo '<br/>';
					echo '</div>';
				    
			    echo '</div>';
			echo '</div>';

			$page->drawWhiteBoxEnd();
		echo '</div>';
		
    		echo '<div style="clear:both;width:100%;"></div>';
		echo '<br/>';

		?>
		<script type="text/javascript">
			$(document).ready(function() {
				  $("#search input").keydown(
				     function(e){
				       var key = e.charCode || e.keyCode || 0;
				       if ( key == 13 ) searchProject();
				     }
				  );
			});
		</script>
		<?
	}
}

?>
