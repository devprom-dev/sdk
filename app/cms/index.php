<?php

  include('common.php');
  include('design.php');
  include('c_model.php');
  include('c_package_view.php');
  include('c_businessfunction.php');

  //////////////////////////////////////////////////////////////////////////////////////
  $ents = new Entity;
  $pckgs = new Package;
  $fnc = new BusinessFunction;

  beginPage('Главная');
?>
	<table width=70% cellpadding=6 cellspacing=0 style="border-collapse:collapse;">
		<tr><td height=15></td></tr>
<?
	$package_it = $pckgs->getAll();
    for($i = 0; $i < $package_it->count(); $i++) 
	{
	?>
		<tr><td height=30 valign=top style="border:.5pt solid #cecefe;background:#A9A3A3;color:white;font-weight:bold;">
			<? echo $package_it->getCaption(); ?> »
		</td></tr>
		<tr><td style="padding-left:6pt;border:.5pt solid #cecefe;padding-bottom:8pt;">
			<table cellpadding=3 width=100%>
				<tr><td valign=top width=30%>
					<table cellpadding=4>
			<?
				$entity_it = $ents->getByRef('packageId', $package_it->getId());
			    for($j = 0; $j < $entity_it->count(); $j++) 
				{
					$object = new Metaobject($entity_it->get("ReferenceName"));
					
					if(is_subclass_of($object, 'StoredObjectDB')) {
						$view = $object->createDefaultView();
    	            ?>
                    <tr>
                    	<td><a href="<? echo $object->getPageTableName(); ?>"><? echo $view->getCaption(); ?></a></td>
                    </tr>
        	        <?
					}
					$entity_it->moveNext();
				}
			?>
					</table>
				</td>
				<td valign=top>
					<table cellpadding=4>
			<?
				
				$fnc_it = $fnc->getByRef('packageId', $package_it->getId());
			    for($j = 0; $j < $fnc_it->count(); $j++) 
				{
	        		?>
        			<tr>
        				<td><a href="command.php?class=<? echo $fnc_it->get("ReferenceName"); ?>"><? echo $fnc_it->getCaption(); ?></a>
        				</td>
        			</tr>
    	    		<?
					$fnc_it->moveNext();
				}			
				?>
					</table>
				</td></tr>
			</table>
		</td></tr>
	<?
		$package_it->moveNext();
	}
	
	if($package_it->count() < 1) {
	?>
		<tr>
			<td>
				Для создания модели предметной области перейдите на страницу <a href="model.php">настройки модели »</a>
			</td>
		</tr>
	<?
	}
?>
	</table>
<?
	endPage();
?>