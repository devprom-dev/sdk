<?php

/////////////////////////////////////////////////////////////////////////////////
class CoFilesPageContent extends CoPageContent
{
	function validate()
	{
		global $project_it, $_REQUEST, $model_factory, $user_it;
		
		if ( !is_object($project_it) )
		{
			return false;
		}
		
		if ( !$project_it->IsPublic() || $project_it->HasProductSite() )
		{
			return false;
		}

		if ( !$project_it->IsPublicArtefacts() )
		{
			return false;
		}

		$session = new PMSession($project_it);
		
 		if ( $_REQUEST['id'] != '' )
 		{
			 $artefact = $model_factory->getObject('pm_Artefact');
			 $artefact_it = $artefact->getExact($_REQUEST['id']);
			 
			 if ( $artefact_it->count() < 1 )
			 {
			 	return false;
			 }
			 
			 $action = $model_factory->getObject('pm_DownloadAction');
			 $action->process($artefact_it->getId(), 'pm_Artefact');
			 
			 if ( $user_it->IsReal() )
			 {
				 $sub = $model_factory->getObject('co_ProjectSubscription');
		
				 $it = $sub->getByRefArray( 
					array( 'Project' => $project_it->getId(), 
						   'SystemUser' => $user_it->getId() ) );
		
				 if ( $it->count() < 1 )
				 {
					$sub->add_parms( 
						array( 'Project' => $project_it->getId(), 
							   'SystemUser' => $user_it->getId() ) );
				 }
			 }
			 
			 $downloader = new Downloader;
			
		  	 $downloader->echoFile(SERVER_FILES_PATH.$artefact->getClassName().'/'.
		  	 	basename($artefact_it->getFilePath( 'Content' )), 
			 		$artefact_it->getFileName( 'Content' ), 
			 			$artefact_it->getFileMime( 'Content' ));
			 			
			 die();
 		}
		
		return true;
	}

	function draw()
	{
		global $model_factory, $project_it, $user_it, $_REQUEST;
		
		$page = $this->getPage();
		
		$this->drawProjectHeader(translate('Файлы'));
		
		// introduction
		echo '<div id="fileslist">';

	 		$uid = new ObjectUid;
	 		$uid->project_it = $project_it;
	 		
			$artefact = $model_factory->getObject('pm_Artefact');
			$artefact->defaultsort = ' (SELECT k.OrderNum FROM pm_ArtefactType k ' .
										'WHERE k.pm_ArtefactTypeId = t.Kind) ASC, t.Kind, t.RecordModified DESC';
		
			$artefact_it = $artefact->getByRefArray(
				array ( 'Project' => $project_it->getId(), "IFNULL(IsArchived, 'N')" => 'N' ) );
		
			$prev_kind = 0;
			$visible_kinds = array();
			
			while ( !$artefact_it->end() )
			{
				if ( $prev_kind != $artefact_it->get('Kind') )
				{
					$type_it = $artefact_it->getRef('Kind');
					
					if ( $type_it->get('IsDisplayedOnSite') != 'Y' )
					{
						$artefact_it->moveNext();
						continue;
					}
					
					if ( $prev_kind != 0 )
					{
						$page->drawWhiteBoxEnd();
						echo '</div>';

						echo '<br/><br/>';
					}
	
					echo '<a name="'.$type_it->getId().'"></a>';
					
					echo '<div class="filesbox">';
						$page->drawWhiteBoxBegin();

						echo '<h2>';
							echo $type_it->getDisplayName();
						echo '</h2>';
					
					$prev_kind = $type_it->getId(); 
					array_push($visible_kinds, $prev_kind);
				}

				echo '<br/>';
								
				echo '<div class="post">';
					echo '<div id="entry">';
						echo '<a href="'.ParserPageUrl::parse($artefact_it).'">'.
							$artefact_it->getDisplayName().'</a> ';
	
						echo '<div>'.$artefact_it->getHtml('Description').'</div>';
					echo '</div>';
					
					echo '<div id="version">';
						echo translate('Версия').': '.$artefact_it->getVersion().', '.
							translate('объем').': '.$artefact_it->getFileSizeKb('Content').' Kb, '.
								translate('загрузок').': '.$artefact_it->getDownloadsAmount();
					echo '</div>';
				echo '</div>';
	
				$artefact_it->moveNext();
			}

			if ( $prev_kind != 0 )
			{
				$page->drawWhiteBoxEnd();
				echo '</div>';
			}

		echo '</div>';
		
		$artefact_type = $model_factory->getObject('pm_ArtefactType');
		$artefact_type_it = $artefact_type->getInArray('pm_ArtefactTypeId', $visible_kinds);

		echo '<div id="user_actions">';

			echo '<div class="action_box">';
				$page->drawGreyBoxBegin();

				echo '<div id="title">';
					echo translate('Разделы');
				echo '</div>';
	
				while ( !$artefact_type_it->end() )
				{
					if ( $artefact_type_it->get('IsDisplayedOnSite') == 'Y' )
					{
						echo '<div style="padding-bottom:6px;">';
							echo '<a href="#'.$artefact_type_it->getId().'">' .
								$artefact_type_it->getDisplayName().'</a>';
						echo '</div>';
					}
					
					$artefact_type_it->moveNext();
				}
				$page->drawGreyBoxEnd();
			echo '</div>';
							
			echo '<div>';
				$page->drawGreyBoxBegin();

				echo '<div id="title">';
					echo translate('Новые файлы');
				echo '</div>';

				$artefact_it = $artefact->getLatestDisplayed(5);
									
				while ( !$artefact_it->end() )
				{
					echo '<div>';
						echo '<a href="'.ParserPageUrl::parse($artefact_it).'">'.
								$artefact_it->getDisplayName().'</a>';
					echo '</div>';	

					$artefact_it->moveNext();
				} 		 		

				$page->drawGreyBoxEnd();
			echo '</div>';

		echo '</div>';

		echo '<div style="clear:both;width:100%;"></div>';
	}
}

?>
