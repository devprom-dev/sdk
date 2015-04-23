<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

include 'FieldBlogAttachments.php';
include 'FieldBlogPostTagTrace.php';

if ( !class_exists('FieldSignature', false) )
{
    include SERVER_ROOT_PATH.'pm/views/ui/FieldSignature.php';
}

class BlogForm extends PMPageForm
{
    var $form_index = '';
    var $review_mode = false;

    function setFormIndex( $index )
    {
        $this->form_index = $index;
    }
    
    function setReviewMode( $mode )
    {
        $this->review_mode = $mode;
    }
    
    function getReviewMode()
    {
        return $this->review_mode;
    }

    function getActions( $page_it = null )
    {
        $actions = array();

        if ( !is_object($page_it) ) $page_it = $this->getObjectIt();

        if ( !is_object($page_it) ) return $actions;

        if( getFactory()->getAccessPolicy()->can_modify($page_it) && !$this->getEditMode() )
        {
            array_push($actions, array( 
                'name' => translate('Редактировать'),
                'url' => $page_it->getEditUrl(),
                'type' => 'button'
            ));
        }

        if ( getFactory()->getAccessPolicy()->can_create($this->getObject()) )
        {
            if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
            
            array_push($actions, array( 
                'name' => text(1359),
                'url' => $this->object->getPageName() 
            ));
        }

        return $actions;
    }

    function getEditor()
    {
        $object_it = $this->getObjectIt();

        $editor_class = is_object($object_it) ? $object_it->get('ContentEditor') : "";
        
        $editor = WikiEditorBuilder::build($editor_class);

        if ( is_object($object_it) )
        {
            $editor->setObjectIt( $object_it );
        }
        else
        {
            $editor->setObject( $this->getObject() );
        }

        return $editor;
    }

    function getFieldValue( $field )
    {
        global $part_it;

        $object_it = $this->getObjectIt();

        switch ( $field )
        {
            case 'AuthorId':
                if ( !is_object($object_it) || is_object($object_it) && $object_it->get('Author') == '' )
                {
                    return $part_it->getId();
                }
                break;

            case 'ContentEditor':

                return get_class( $this->getEditor() );
                
            case 'Blog':
                
                $session = getSession();
                
                $project_it = $session->getProjectIt();
                
                return $project_it->get('Blog');
                
            case 'Content':
                
                if ( $_REQUEST['from'] == 'requests' )
                {
                    return $this->getReleaseNotes();
                }
                
                break;
        }

        return parent::getFieldValue( $field );
    }

    function getReleaseNotes()
    {
        global $model_factory;
        
		$hashids = $model_factory->getObject('HashIds');
		$ids = $hashids->getIds( $_REQUEST['items'] );
		
 		$issue_type = $model_factory->getObject('pm_IssueType');
 		$issue_type_it = $issue_type->getByRef('ReferenceName', 'bug');

 		$request = $model_factory->getObject('pm_ChangeRequest');
 		$req_it = $request->getInArray('pm_ChangeRequestId', $ids);
 		
		$uid = new ObjectUID;
		
 		$issues = array();
 		$bugs = array();
 		$description = '';
 		
 		for ( $i = 0; $i < $req_it->count(); $i++ )
 		{
 			$info = $uid->getUidInfo($req_it);
 			
 			$title = str_replace(chr(13), ' ',  
 							str_replace(chr(10), ' ', 
 									'[url='.$info['url'].' text='.$info['uid'].'] '.$req_it->getWordsOnly('Caption', 15).
 										' ('.$info['state_name'].')'
 				));
 			
 			if ( $req_it->get('Type') == $issue_type_it->getId() )
 			{
 				 array_push($bugs, $title);
 			}
 			else
 			{
 				 array_push($issues, $title);
 			}
 			
 			$req_it->moveNext();
 		}

		if ( count($issues) > 0 )
		{
 			$description .= '*'.text(759).':*'.chr(10);
 			
 			foreach ( $issues as $issue )
 			{
 				$description .= '* '.$issue.chr(10);
 			}
		}
 		
		if ( count($bugs) > 0 )
		{
 			$description .= '*'.text(760).':*'.chr(10);
 			
 			foreach ( $bugs as $issue )
 			{
 				$description .= '* '.$issue.chr(10);
 			}
		}
		
		$editor = $this->getEditor();
		$editor->setObjectIt( $post_it );
		
		$parser = $editor->getEditorParser( 'wikisyntaxeditor' );
		
		if ( is_object($parser) )
		{
			$parser->setObjectIt( $post_it );
			return $parser->parse( $description );
		}
		else
		{
			return $description;
		}
    }
    
    function getFieldDescription( $attr )
    {
        switch ( $attr )
        {
            case 'Content':
                if ( $this->getEditMode() )
                {
                    $editor = $this->getEditor();
                    return $editor->getDescription();
                }

            default:
                return parent::getFieldDescription( $attr );
        }
    }

    function drawButtons()
    {
        if ( !$this->getEditMode() )
        {
            parent::drawButtons();
            
            return;
        }
        
        $editor = $this->getEditor();

        $editor->drawPreviewButton();

        parent::drawButtons();
    }

    function IsAttributeVisible( $attr_name )
    {
        global $_REQUEST;

        $this->object_it = $this->getObjectIt();
        	
        switch ( $attr_name )
        {
            case 'OrderNum':
                return $this->getEditMode();
                	
            case 'Tags':
                return true;

            case 'ContentEditor':
            case 'Blog':
                return false;

            case 'AuthorId':
                return $this->getReviewMode();
                
            default:
                return parent::IsAttributeVisible( $attr_name );
        }
    }

    function createField( $name )
    {
        global $model_factory;
        
        $field = parent::createField( $name );

        if ( !is_object($field) ) return $field;

        switch ( $name )
        {
            case 'Caption':

                $field->setTabIndex( 1 );

                $field->setId( $field->getId().$this->form_index );
                
                break;

            case 'Content':

                $field->setTabIndex( 3 );
                
                $field->setId( $field->getId().$this->form_index );

                break;
                
            case 'AuthorId':
                
                $object_it = $this->getObjectIt();
                
                if ( is_object($object_it) && $object_it->get('AuthorId') > 0 )
                {
                    $part = $model_factory->getObject('pm_Participant');
                    
                    $part_it = $part->getExact( $object_it->get('AuthorId') );
                    
                    $field->setAuthor( $part_it->getDisplayName() );
                    
                    $field->setDate( str_replace('%1', $object_it->getDateTimeFormat('RecordCreated'), text(1305)) );
                }
                
                break;
        }

        return $field;
    }

    function createFieldObject( $name )
    {
        global $model_factory;

        $this->object_it = $this->getObjectIt();

        switch ( $name )
        {
            case 'Tags':
                return new FieldBlogPostTagTrace( is_object($this->object_it)
                    ? $this->object_it : null );
                	
            case 'Caption':
        		if ( !$this->getEditMode() )
			    {
    				$field = new FieldWYSIWYG( get_class($this->getEditor()) );
     					
     				is_object($this->object_it) ? 
    					$field->setObjectIt( $this->object_it ) : 
    						$field->setObject( $this->getObject() );
    						
    			    $field->setMode( WIKI_MODE_INPLACE_INPUT );
			    }
			    else
			    {
			        $field = parent::createFieldObject($name);
			        
 				    $field->setDefault( translate('Название') );
			    }
                	
                return $field;

            case 'Content':

                $field = new FieldWYSIWYG( get_class($this->getEditor()) );

                is_object($this->object_it) ?
                    $field->setObjectIt( $this->object_it ) :
                    $field->setObject( $this->getObject() );

                $field->setAttachmentsField( new FieldBlogAttachments(
                        is_object($this->object_it) ? $this->object_it : $this->object
                ));
                
        		if ( $this->getEditMode() )
				{
					$field->setHasBorder( false );
					$field->getEditor()->setMode( WIKI_MODE_NORMAL );
				}
				else
				{
    				$field->setCssClassName( 'wysiwyg-text' );
				}

                return $field;
                
            case 'AuthorId':
                return new FieldSignature();

            default:
                return parent::createFieldObject( $name );
        }
    }
    
    function getRenderParms()
    {
        return array_merge( parent::getRenderParms(), 
        		array (
		            'comments' => new PageSectionComments( $this->getObjectIt() ),
        			'comments_count' => is_object($this->getObjectIt()) ? getFactory()->getObject('Comment')->getCount($this->getObjectIt()) : 0,
		            'index' => $this->form_index
        		)
        );
    }

    function getTemplate()
    {
        if ( !$this->getReviewMode() )
        {
            return "core/PageForm.php";
        }
        else
        {
            return "pm/BlogPostForm.php";
        }
    }
}