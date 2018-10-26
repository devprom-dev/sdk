<?php
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';
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

        $method = new ObjectModifyWebMethod($page_it);
        if( $method->hasAccess() && !$this->getEditMode() )
        {
            array_push($actions, array( 
                'name' => translate('Редактировать'),
                'url' => $method->getJSCall(),
                'type' => 'button'
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

    function validateInputValues( $id, $action )
    {
        if ( $_REQUEST['Blog'] == '' ) {
            $_REQUEST['Blog'] = $project_it = getFactory()->getObject('Project')->getExact($_REQUEST['Project'] )->get('Blog');
        }
        return parent::validateInputValues( $id, $action );
    }

    function getFieldValue( $field )
    {
        $object_it = $this->getObjectIt();

        switch ( $field )
        {
            case 'AuthorId':
                if ( !is_object($object_it) || is_object($object_it) && $object_it->get('Author') == '' )
                {
                    return getSession()->getParticipantIt()->getId();
                }
                break;

            case 'ContentEditor':
                return get_class( $this->getEditor() );
                
            case 'Blog':
                $projectId = parent::getFieldValue('Project');
                if ( $projectId == '' ) {
                    $project_it = getSession()->getProjectIt();
                }
                else {
                    $project_it = getFactory()->getObject('Project')->getExact($projectId);
                }
                return $project_it->get('Blog');
        }

        return parent::getFieldValue( $field );
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
                if ( !$this->getEditMode() ) {
                    $field->setReadonly( true );
                }
                break;

            case 'Content':
                $field->setTabIndex( 3 );
                $field->setId( $field->getId().$this->form_index );
                if ( !$this->getEditMode() ) {
                    $field->setReadonly( true );
                }
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

        		if ( $this->getEditMode() )
				{
					$field->setHasBorder( false );
					$field->getEditor()->setMode( WIKI_MODE_NORMAL );
				}
				else
				{
    				$field->setCssClassName( 'wysiwyg-text' );
				}
                $field->setToolbar(WikiEditorBase::ToolbarFull);
                $field->setRows(25);
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
        			'comments_count' => is_object($this->getObjectIt()) ? getFactory()->getObject('Comment')->getCountForIt($this->getObjectIt()) : 0,
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