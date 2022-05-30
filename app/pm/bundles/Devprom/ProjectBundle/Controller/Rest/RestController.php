<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Devprom\ProjectBundle\Service\Model\ModelService;
use Devprom\ProjectBundle\Service\Model\FilterResolver\ModifiedAfterFilterResolver;
use Devprom\ProjectBundle\Service\Model\FilterResolver\StateFilterResolver;
use Devprom\ProjectBundle\Service\Model\FilterResolver\CommonFilterResolver;

abstract class RestController extends FOSRestController implements ClassResourceInterface
{
    protected function getEntity(Request $request) {
        return $this->getClassName($request);
    }

	public function cgetAction(Request $request)
	{
		try    	
		{
			if ( getSession()->getAuthenticationFactory()->writeOnly() ) {
				throw new Exception("Access restricted");
			}

	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService($request)->find(
								$this->getEntity($request),
								intval($request->get('limit')),
								intval($request->get('page')) - 1,
                                $request->get('output') != ''
                                            ? $request->get('output')
                                            : ModelService::OUTPUT_HTML
							), 200
					));
		}
		catch( \Exception $e )
		{
			\Logger::getLogger('System')->error($e->getMessage());
			throw $this->createNotFoundException($e->getMessage(), $e);
		}
	}
	
    public function getAction(Request $request, $id)
    {
		try    	
		{
            if ( getSession()->getAuthenticationFactory()->writeOnly() ) {
                throw new Exception("Access restricted");
            }

	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService($request)->get(
								$this->getEntity($request),
								$id,
                                $request->get('output') != ''
                                            ? $request->get('output')
                                            : ModelService::OUTPUT_HTML
							), 200
					));
		}
		catch( \Exception $e )
		{
			\Logger::getLogger('System')->error($e->getMessage());
			throw $this->createNotFoundException($e->getMessage(), $e);
		}
    }
	
	public function cpostAction(Request $request)
    {
		try    	
		{
            if ( getSession()->getAuthenticationFactory()->readOnly() ) {
                throw new Exception("Access restricted");
            }

	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService($request)->set(
                                $this->getEntity($request),
				        		$this->getPostData($request),
                                '',
                                $request->get('output') != ''
                                            ? $request->get('output')
                                            : ModelService::OUTPUT_HTML
							), 200
					));
		}
		catch( \Exception $e )
		{
			\Logger::getLogger('System')->error($e->getMessage());
			throw $this->createNotFoundException($e->getMessage(), $e);
		}
    }
    
	public function putAction(Request $request, $id)
    {
		try    	
		{
            $factory = getSession()->getAuthenticationFactory();
            if ( $factory->readOnly() || $factory->writeOnly() ) {
                throw new Exception("Access restricted");
            }

	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService($request)->set(
			        				$this->getEntity($request),
				        			$this->getPostData($request),
			        				$id,
                                $request->get('output') != ''
                                            ? $request->get('output')
                                            : ModelService::OUTPUT_HTML
							), 200
					));
		}
		catch( \Exception $e )
		{
			\Logger::getLogger('System')->error($e->getMessage());
			throw $this->createNotFoundException($e->getMessage());
		}
    }

	public function deleteAction(Request $request, $id)
    {
		try    	
		{
            $factory = getSession()->getAuthenticationFactory();
            if ( $factory->readOnly() || $factory->writeOnly() ) {
                throw new Exception("Access restricted");
            }

	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService($request)->delete(
			        				$this->getEntity($request),
			        				$id,
                                $request->get('output') != ''
                                            ? $request->get('output')
                                            : ModelService::OUTPUT_HTML
							), 200
					));
		}
		catch( \Exception $e )
		{
			\Logger::getLogger('System')->error($e->getMessage());
			throw $this->createNotFoundException($e->getMessage());
		}
    }

    protected function getPostData(Request $request) {
        return \JsonWrapper::decode($request->getContent());
    }
    
    protected function getModelService(Request $request)
    {
    	return new ModelService(
            new \ModelValidator(
                    array (
                            new \ModelValidatorObligatory(),
                            new \ModelValidatorTypes()
                    )
            ),
            new \ModelDataTypeMapper(),
            $this->getFilterResolver($request),
            null,
            $request->get('version') != 'v1'
		);
    }

	function getClassName(Request $request)
	{
        $className = $request->get('class');
        if ( $className == '' ) {
            $className = str_replace('delete_', '',
                str_replace('put_', '',
                    str_replace('post_', '',
                        str_replace('get_', '',
                            $request->get('_route')))));
        }

        switch( $className ) {
			case 'issues':
            case 'issue':
                $className = 'requests';
		}

        $singular = array (
            '/^(ox)en/i' => '$1',
            '/(alias|status)es$/i' => '$1',
            '/([octop|vir])i$/i' => '$1us',
            '/(cris|ax|test)es$/i' => '$1is',
            '/(shoe)s$/i' => '$1',
            '/(o)es$/i' => '$1',
            '/(bus)es$/i' => '$1',
            '/([m|l])ice$/i' => '$1ouse',
            '/(x|ch|ss|sh)es$/i' => '$1',
            '/([^aeiouy]|qu)ies$/i' => '$1y',
            '/([lr])ves$/i' => '$1f',
            '/([ti])a$/i' => '$1um',
            '/(n)ews$/i' => '$1ews',
            '/s$/i' => '',
        );
        return preg_replace( array_keys($singular), array_values($singular), $className );
	}

	protected function getFilterResolver(Request $request) {
		return array(
			new ModifiedAfterFilterResolver(
				$request->get('updatedAfter'),
				$request->get('updatedBefore'),
				$request->get('createdAfter'),
				$request->get('createdBefore')
			),
            new StateFilterResolver($request->get('state')),
            new CommonFilterResolver($request->get('in'))
        );
	}
}
