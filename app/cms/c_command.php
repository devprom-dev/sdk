<?php

 class Command
 {
 	var $parameters = array();
	var $parmvalues = array();
	
	function Command()
	{
		global $_REQUEST;
		$action = $_REQUEST[get_class($this).'action'];
		
		if(isset($action)) {
			if($action == 'command.execute') {
				for($i = 0; $i < count($this->parameters); $i++)
				{
					array_push($this->parmvalues, $_REQUEST['Parm'.$i]);
				}
				exit(header('Location: '.$this->Execute() ));
			}
		}
	}
	
	function addParameter( $field, $caption )
	{
		$field->name = "Parm".count($this->parameters);
		array_push($this->parameters, array( $field, $caption) );
	}
	
	function getUrl() {
		return 'command.php?class='.get_class($this);
	}
	
	function draw()
	{
	?>
		<form action="<? echo $this->getUrl(); ?>" method="post">
		<table style="border:.5pt solid #efefef;" cellpadding=5 cellspacing=5 width=50%>
			<tr>
				<td height=40 valign=top style="border-bottom:.5pt solid #efefef;">
					<? echo $this->getCaption(); ?>
				</td>
			</tr>
			<tr>
				<td>
					<table width=100%>
					<?
						for($i = 0; $i < count($this->parameters); $i++)
						{
						?>
							<tr>
								<td><? echo $this->parameters[$i][1]; ?></td>
							</tr>
							<tr>
								<td style="padding-bottom:15pt;"><? $this->parameters[$i][0]->draw(); ?></td>
							</tr>
						<?
						}
					?>
					</table>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align=right>
					<input type="hidden" name="<? echo get_class($this); ?>action" value="command.execute">
					<input type="submit" value="Выполнить">
				</td>
			</tr>
		</table>
		</form>
	<?
	}
	
 	function getCaption() {}
 	function Execute() {}
 }

?>