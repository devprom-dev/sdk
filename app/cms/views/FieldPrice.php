<?php

class FieldPrice extends Field
{
 	var $object_it;
	
 	function draw()
	{
		$currency = array('EUR', 'USD', 'RUR');
	?>
		<table cellpadding=0 cellspacing=0>
			<tr>
				<td>
					<input tabindex="<? echo $this->getTabIndex() ?>" style="width:70pt;" name="<? echo $this->name; ?>" value="<? echo $this->value; ?>">
				</td>
				<td>&nbsp</td>
				<td>
					<select id="<? echo $this->id ?>" style="margin-bottom:-3pt;" name="<? echo $this->name.'Code'; ?>">
					<?
            			for($i = 0; $i < count($currency); $i++) {
							if(isset($this->object_it)) {
								$selected = $currency[$i] == $this->object_it->get($this->name."Code") ? "selected" : "";
							}
						?>
							<option value="<? echo $currency[$i]; ?>" <? echo $selected; ?>><? echo $currency[$i]; ?></option>
						<?
            			}
					?>
					</select>
				</td>
			</tr>
		</table>
	<?
	}
}
