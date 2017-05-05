<?php

use Devprom\ApplicationBundle\Service\Atom\BlogService; 

include 'BlogForm.php';

class BlogTable extends PMPageTable
{
    var $object;
    var $form;

    function __construct( $object )
    {
        $this->form = new BlogForm($object);
        	
        parent::__construct($object);
    }

    function getNewActions()
    {
        $actions = array();

        $method = new ObjectCreateNewWebMethod($this->getObject());
        if ( $method->hasAccess() )
        {
            $actions[] = array(
                'name' => text(1353),
                'url' => $method->getJSCall()
            );
        }
    
        return $actions;
    }
    
    function getTemplate()
    {
        return 'pm/BlogTable.php';
    }
    
    function buildDatesFilter()
    {
    	$filter = new FilterAutocompleteWebMethod(getFactory()->getObject('BlogPostDates'));
    	
    	$filter->setValueParm( 'monthyear' );
    	
    	return $filter;
    }
    
    function getFilters()
    {
    	return array (
            $this->buildTagsFilter(),
            $this->buildDatesFilter()
    	);
    }

    protected function buildTagsFilter()
    {
        $tag = getFactory()->getObject('BlogPostTag');
        $filter = new FilterObjectMethod($tag, translate('Тэги'), 'tag');
        $filter->setIdFieldName('Tag');
        return $filter;
    }

	function getFilterPredicates()
	{
	    $values = $this->getFilterValues();
	    
		$predicates = array(
			new BlogPostTagsFilter( $values['tag'] ),
			new BlogPostDateFilter( $values['monthyear'] ),
    		new FilterInPredicate($_REQUEST['BlogPostId'])
		);
		
		return array_merge(parent::getFilterPredicates(), $predicates);
	}

    function getRenderParms( $parms )
    {
        global $_REQUEST, $model_factory;
        
        $this->form->setReviewMode( true );
    
        $form_parms = $this->form->getRenderParms();

        $post = $model_factory->getObject('BlogPost');
        
        $post->addSort( new SortAttributeClause('RecordCreated.D') );
        
        foreach( $this->getFilterPredicates() as $predicate )
        {
        	$post->addFilter( $predicate );
        }
        
        $post_it = $post->getLatest(20);
        
        $rss_service = new BlogService();

        return array_merge( parent::getRenderParms( $parms ), array (
            'scripts' => $form_parms['scripts'],
            'post_it' => $post_it,
        	'news_url' => $rss_service->getUrl( getSession()->getProjectIt() )
        ));
    }
    
    function draw( $view, $post_it )
    {
        while ( !$post_it->end() )
        {
            $this->renderPost( $view, $post_it );
            	
            $post_it->moveNext();
        }
    }

    function renderPost( $view, &$post_it )
    {
        $this->form->setFormIndex( $post_it->getId() );
        
        $this->form->show( $post_it->copy() );
        	
        $page = $this->getPage();
        	
        $this->form->render( $view, array_merge($page->getRenderParms(), array(
                'formonly' => true,
                'title' => translate('Блог')
        )));
    }
}