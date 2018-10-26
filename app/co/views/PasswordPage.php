<?php

include ('PasswordForm.php');

class ResetPasswordPage extends CoPage
{
 	function getTable()
 	{
 		global $model_factory, $_REQUEST;
 		
 		$user_it = getSession()->getUserIt();
 		
 		if( $_REQUEST['key'] != '' )
 		{
	 		$user = $model_factory->getObject('cms_User');
			$it = $user->getAll();
			
			while ( !$it->end() ) 
			{
				if( $_REQUEST['key'] == $it->getResetPasswordKey() )
				{
					return new PasswordForm($it);		 
				}
				
				$it->moveNext();
			}
			
			exit(header('Location: /recovery'));
 		}

 		if ( is_object($user_it) && $user_it->count() > 0 )
 		{
 			return new PasswordForm($user_it);
 		}
 		else
 		{
 			exit(header('Location: /404'));
 		}
 	}

    function getFullPageRenderParms()
    {
        return array_merge(
            parent::getFullPageRenderParms(),
            array(
                'inside' => false
            )
        );
    }
}
