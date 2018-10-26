<?php
include_once SERVER_ROOT_PATH.'core/c_session.php';
include_once SERVER_ROOT_PATH."core/classes/project/PortfolioAllBuilder.php";
include_once 'COAccessPolicy.php';
include_once 'COSystemTriggers.php';
include_once "ResourceBuilderCoLanguageFile.php";
include_once "ProjectWelcomeStylesheetBuilder.php";

class COSession extends SessionBase
{
 	function getSite() {
 	    return 'co';
 	}

 	function getCacheKey() {
        return 'apps/'.$this->getUserIt()->getId();
    }

    function getApplicationUrl() {
 	    return '/co/';
 	}

 	function buildFactories()
    {
        global $model_factory;

        $model_factory = new \ModelFactoryExtended(
            \PluginsFactory::Instance(),
            getFactory()->getCacheService(),
            $this->getCacheKey(),
            new \CoAccessPolicy(getFactory()->getCacheService(), $this->getCacheKey())
        );

        parent::buildFactories();
    }

    function createBuilders()
 	{
 	    return array_merge(
 	    		array (
 	    			new ResourceBuilderCoLanguageFile()
 	    		),
 	    		parent::createBuilders(),
 	    		array (
                    new PortfolioAllBuilder(),
 	    			new ProjectWelcomeStylesheetBuilder(getSession()),
                    new COSystemTriggers()
 	    		)
 	    );
 	}
}
