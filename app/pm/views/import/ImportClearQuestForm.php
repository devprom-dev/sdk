<?php

class ImportClearQuestForm extends ImportTextForm
{
 	function getAddCaption()
 	{
 		return text(423);
 	}
 	
 	function getCommandClass()
 	{
 		return 'requestsimportclearquest';
 	}

	function getAttributes()
	{
		return array('ClearQuest', 'Release', 'Delimiter', 'Users', 'Defects', 'AttachmentsDesc', 'History', 'Attachments'); 	
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'ClearQuest':
				return text(947); 	

			case 'Release':
				return translate('Релиз'); 	

			case 'Delimiter':
				return text(948); 	

			case 'Attachments':
				return text(949); 	

			case 'Users':
				return text(950); 	

			case 'Defects':
				return text(951); 	

			case 'AttachmentsDesc':
				return text(952); 	

			case 'History':
				return text(953); 	
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Defects':
			case 'Users':
			case 'AttachmentsDesc':
			case 'History':
				return 'file'; 	
		}
	}

 	function getDescription( $attribute )
 	{
		switch ( $attribute )
		{
			case 'ClearQuest':
 				return text(435);

			case 'Release':
 				return text(433);

			case 'Delimiter':
 				return text(426);

			case 'Attachments':
 				return text(432);

			case 'Users':
 				return text(427);

			case 'Defects':
 				return text(425);

			case 'AttachmentsDesc':
 				return text(436);

			case 'History':
 				return text(437);
		}
 	}

	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
			case 'Attachments':
				return SERVER_ROOT_PATH.'clearquest/'; 
		}
	}

	function IsAttributeRequired( $attribute )
	{
		switch ( $attribute )
		{
			case 'AttachmentsDesc':
			case 'History':
				return false;
			
			default:
				return true; 	
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		global $model_factory;
		
		if ( $attribute == 'Delimiter' )
		{
			echo '<select id="'.$attribute.'" name="'.$attribute.'" tabindex="'.$tab_index.'">';
				echo '<option value="9" selected>TAB</option>';
				echo '<option value="44">,</option>';
			echo '</select>';
		}
		elseif ( $attribute == 'ClearQuest' )
		{
			echo '<select id="'.$attribute.'" name="'.$attribute.'" tabindex="'.$tab_index.'">';
				echo '<option value="CQ6" selected>ClearQuest 6</option>';
				echo '<option value="CQ7">ClearQuest 7</option>';
			echo '</select>';
		}
		elseif ( $attribute == 'Release' )
		{
			$release = $model_factory->getObject('pm_Version');
			$release_it = $release->getAll();
			
			echo '<select id="'.$attribute.'" name="'.$attribute.'" tabindex="'.$tab_index.'">';
				while ( !$release_it->end() )
				{
					echo '<option value="'.$release_it->getId().'">'.
						translate('Релиз').': '.$release_it->getDisplayName().'</option>';
					$release_it->moveNext();
				}
			echo '</select>';
		}
		else
		{
			parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}
}