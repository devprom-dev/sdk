<?php 

if ( !$formonly )
{
    $view->extend('core/PageBody.php'); 
    
    $view['slots']->output('_content');
}
else
{
    echo $scripts;
}

$no_sections_class = is_a($form, 'PMWikiForm') ? 'span12' : 'span10';

$has_caption = $uid_icon != '' || $caption != '' && $caption != $navigation_title;

?>

<div class="<?=($formonly ? '' : ($draw_sections && count($sections) > 0 ? 'span8' : $no_sections_class))?>">
    <form class="form-horizontal" id="<?=$form_id?>" method="post" action="<?=$form_processor_url?>" name="object_form" enctype="<?=($formonly ? "application/x-www-form-urlencoded" : "multipart/form-data")?>" autocomplete="off" class_name="<?=$form_class_name?>">
    	<fieldset>
    	
    	    <?php if (!$formonly) { ?>
    
        	    <div class="pull-left">
                    <ul class="breadcrumb">
            	        <?php if ( $navigation_title != '') { ?>
            	        <li>
            	            <a href="<?=$navigation_url?>"><?=$navigation_title?></a>
            	            <?php if ( $has_caption ) { ?><span class="divider">/</span> <?php } ?>
            	        </li>
            	        <?php } ?>
                    
                        <?php if ( $has_caption ) { ?>
                    	<li>
                   	        <?=($uid_icon != '' ? $uid_icon : $caption)?>
                    	</li>
                    	<?php } ?>
                    </ul> <!-- end breadcrumb -->
        		</div>

    		    <div class="pull-right actions">

    		        <?php $form->drawButtons(); ?>
        		
    		        <?php if ( count($actions) > 0 && $action != 'show' ) { ?>
    		        
        		        <div class="btn-group">
        					<a class="btn btn-small dropdown-toggle btn-inverse" href="#" data-toggle="dropdown">
        						<?=translate('Действия')?>
        						<span class="caret"></span>
        					</a>
        					<? echo $view->render('core/PopupMenu.php', array ('items' => $actions)); ?>
        				</div>
        				
        			<?php } ?>
        			
    			</div> <!-- end actions -->
        			
        	    <?php if ( $state_name != '' ) { ?>
        		<div class="pull-right actions">
       				<span class="label label-warning" style="margin-top:6px;"><?=$state_name?></span> &nbsp;
        		</div>
        		<?php } ?>
        		
        		<div class="clearfix"></div>
    		
    		<?php } ?>
    		
    	  	<input id="<?=$action_mode?>" type="hidden" name="action_mode" value="form">
    	  	<input name="entity" value="<?=$entity?>" type="hidden">
    	  	<input name="RecordVersion" value="<?=$record_version?>" type="hidden">
    		<input type="hidden" action="true" id="<?=$class_name?>action" name="<?=$class_name?>action" value="">
    		<input type="hidden" id="<?=$class_name?>Id" name="<?=$class_name.'Id'?>" value="<?=$object_id?>">
    		<input id="<?=$class_name?>redirect" type="hidden" name="redirect" value="<?=$redirect_url?>">
    		<input type="hidden" id="unsavedMessage" value="<?=text(632)?>">
    		<input type="hidden" id="deleteMessage" value="<?=$form->getDeleteMessage()?>">
    		<input type="hidden" name="Transition" value="<?=$transition?>">
    		
    		<?php 
    		
    		echo $view->render( $form_body_template, array(
                'warning' => $warning,
                'alert' => $alert,
                'attributes' => $attributes,
                'formonly' => $formonly,
                'form' => $form
            ));
    
			if ( $bottom_hint != '' )
			{
				echo $view->render('core/Hint.php', array('title' => $bottom_hint, 'name' => $class_name));
			}

            ?>
       </fieldset>
    </form>
</div>

<?php if (!$formonly && $draw_sections) { ?>
    <div class="span4">
        <?php 
        	echo $view->render('core/PageSections.php', array(
        		'sections' => $sections,
        		'object_class' => $object_class,
        		'object_id' => $object_id 
        	));
        ?>
    </div>
<?php } ?>


<?php 

if ( !$formonly && $draw_sections && count($bottom_sections) > 0 )
{
    echo '<div class="clearfix"></div>';
    
    echo $view->render('core/PageSections.php', array(
            'sections' => $bottom_sections,
            'object_class' => $object_class,
            'object_id' => $object_id
    ));

} // count($bottom_sections)

?>

<?php if ( !$formonly ) { ?>

<script type="text/javascript">
	var originalState = '';
    var formid = 'object_form';

	$(document).ready(function() 
	{
		makeForm('<?=$action?>');
	});
</script>

<?php } ?>

<script type="text/javascript">

    devpromOpts.saveButtonName = '<?=$button_save_title?>';
    devpromOpts.closeButtonName = '<?=translate('Отменить')?>';
    devpromOpts.deleteButtonName = '<?=translate('Удалить')?>';

</script>
