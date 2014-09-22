<script type="text/javascript">
var options = 
{
	beforeSubmitCallback: function( formId ) 
	{
	},
	successCallback: function(response) 
	{
		hideCommentForm();
	} 
};
</script>

<?php

$parms['buttons_template'] = 'pm/CommentsFormButtons.php';
$parms['buttons_parms'] = array( 'prevcomment' => $prevcomment );

echo $view->render('core/FormAsyncBody.php', $parms);
