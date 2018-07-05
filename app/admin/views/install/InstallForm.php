<?php
include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';

class InstallForm extends AjaxForm
{
	function getAddCaption()
	{
		return text(990);
	}

	function getCommandClass()
	{
		return 'installsystem';
	}

	function getAttributes()
	{
		return array('Checkpoints', 'MySQLHost', 'Database', 'SkipCreation', 'SkipStructure', 'DatabaseUser', 'DatabasePass');
	}

	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'MySQLHost':
				return text(681);

			case 'Database':
				return text(682);

			case 'SkipCreation':
				return text(683);

			case 'SkipStructure':
				return text(931);

			case 'DatabaseUser':
				return text(684);

			case 'DatabasePass':
				return text(685);

            default:
                return parent::getName($attribute);
		}
	}

	function getDescription( $attribute )
	{
		switch ( $attribute )
		{
			case 'SkipCreation':
				return text(686);

			case 'SkipStructure':
				return text(932);

            default:
                return parent::getDescription($attribute);
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'MySQLHost':
			case 'Database':
			case 'DatabaseUser':
			case 'DatabasePass':
				return 'text';

			case 'SkipCreation':
			case 'SkipStructure':
				return 'char';

            case 'Checkpoints':
                return 'custom';
		}
	}

	function getAttributeValue( $attribute )
	{
		if ( $attribute == 'MySQLHost' )
		{
			return 'localhost';
		}
		else if ( $attribute == 'DatabaseUser' )
		{
			return 'devprom';
		}
		else if ( $attribute == 'Database' )
		{
			return 'devprom';
		}
		else
		{
			return parent::getAttributeValue( $attribute );
		}
	}

	function IsAttributeRequired( $attribute )
	{
		return $attribute != 'SkipCreation' && $attribute != 'SkipStructure';
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function getButtonText()
	{
		return translate('Установить');
	}

	function getSite()
	{
		return 'admin';
	}

	function getWidth()
	{
		return '100%';
	}

	function IsCentered()
	{
		return false;
	}

	function getRedirectUrl()
	{
		return '/admin/install';
	}

	function drawCustomAttribute($attribute, $value, $tab_index, $view)
    {
        switch( $attribute ) {
            case 'Checkpoints':
                $checkpoints = getCheckpointFactory()->getCheckpoint( 'CheckpointSystem' );
                $checkpoints->executeDynamicOnly();
                $checkpoints->check();

                $fails = array();
                $checkIt = getFactory()->getObject('SystemCheck')->getAll();
                while( !$checkIt->end() ) {
                    if ( $checkIt->getId() == 'c927e5f2e9f0ba7c76e3c1a8eb8ea819' ) {
                        $checkIt->moveNext();
                        continue;
                    }
                    if ( $checkIt->get('IsEnabled') == 'Y' ) {
                        if ( $checkIt->get('CheckResult') == 'N' ) {
                            $fails[$checkIt->getDisplayName()] = $checkIt->get('Description');
                        }
                    }
                    $checkIt->moveNext();
                }
                if ( count($fails) > 0 ) {
                    $text = text(2471).'<br/><br/>';
                    foreach( $fails as $title => $description ) {
                        $text .= $title . '<br/>' . $description . '<br/><br/>';
                    }
                    echo '<div class="alert alert-danger">'.$text.'</div>';
                }
                else {
                    echo '<div class="alert alert-success">'.text(2470).'</div>';
                }
                break;

            default:
                parent::drawCustomAttribute($attribute, $value, $tab_index, $view);
        }
    }
}
