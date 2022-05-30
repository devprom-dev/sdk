<?php
include_once SERVER_ROOT_PATH."admin/classes/UserModelExtendedBuilder.php";
include_once SERVER_ROOT_PATH."admin/methods/UserFilterRoleWebMethod.php";
include_once SERVER_ROOT_PATH."admin/methods/UserFilterStateWebMethod.php";
include_once SERVER_ROOT_PATH."admin/methods/BlockUserWebMethod.php";
include_once SERVER_ROOT_PATH."admin/methods/UnBlockUserWebMethod.php";

include ('UserForm.php');
include ('UserTable.php');
include ('UserFormAsyncParticipancePre.php');
include ('UserFormAsyncParticipance.php');

class UserPage extends AdminPage
{
    private $object = null;

	function __construct()
	{
		parent::__construct();

		$object_it = $this->getObjectIt();

		if ( $this->needDisplayForm() )
		{
			$this->addInfoSection(new PageSectionAttributes(
				$this->getFormRef()->getObject(), 'additional', translate('Дополнительно'))
			);
            $this->addInfoSection(new PageSectionAttributes(
                    $this->getFormRef()->getObject(), 'notifications-tab', text(1912))
            );
			if ( is_object($object_it) && $object_it->getId() > 0 ) {
				$this->addInfoSection(new PageSectionLastChanges($object_it));
			}
		}
	}
	
	function getObject()
	{
	    if ( !is_object($this->object) ) {
            $this->object = $this->buildObject();
        }
	    return $this->object;
	}

	function buildObject() {
        getSession()->addBuilder( new UserModelExtendedBuilder() );
        return getFactory()->getObject('User');
    }

	function getTable()
	{
		return new UserTable( $this->getObject() );
	}

	function getEntityForm()
	{
		if ( $_REQUEST['cms_UserId'] != '' ) {
			$user_it = $this->getObject()->getRegistryBase()->Query(
			    array(
			        new FilterInPredicate($_REQUEST['cms_UserId'])
                )
            );
		}
		
		if ( is_object($user_it) && $_REQUEST['mode'] == 'role' ) {
			return new UserParticipanceForm( $user_it );
		}
		
		if ( is_object($user_it) && $_REQUEST['mode'] == 'participant' ) {
			return new UserParticipancePreForm( $user_it );
		}

		return new UserForm( $this->getObject() );
	}
}
