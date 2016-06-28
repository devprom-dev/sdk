<?php
 
 require_once('common.php');
 
 $uid = $_REQUEST['uid'];
 if ( !isset($uid) ) 
 {
 	exit(header('Location: /404'));
 }
 
 $object_uid = new ObjectUid;
 
 $object_it = $object_uid->getObjectIt($uid);

 if ( !getFactory()->getAccessPolicy()->can_read($object_it) )
 {
 	exit(header('Location: /404'));
 }

 $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 
 switch ( $object_it->object->getClassName() )
 {
 	case 'pm_Task':
 		if ( !$methodology_it->HasTasks() && $object_it->get('ChangeRequest') > 0 )
 		{
 			$issue_it = $object_it->getRef('ChangeRequest');
 			$uid = 'I-'.$issue_it->getId();
 		}
 		
 		break;
 }
 
 $object_url = $object_uid->getObjectUrl($uid);

foreach(array('baseline', 'case') as $parm ) {
    if ( is_numeric($_REQUEST[$parm]) && $_REQUEST[$parm] > 0 ) {
        $object_url .= strpos($object_url, '?') > 0 ? '&'.$parm.'='.$_REQUEST[$parm] : '?'.$parm.'='.$_REQUEST[$parm];
    }
}

if($object_url == '') {
 	exit(header('Location: /404'));
}
else {
	exit(header('Location: '.$object_url));
}
