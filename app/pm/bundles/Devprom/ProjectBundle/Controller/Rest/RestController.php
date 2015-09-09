<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Devprom\ProjectBundle\Service\Model\ModelService;

include_once SERVER_ROOT_PATH.'core/classes/model/validation/ModelValidator.php';
include_once SERVER_ROOT_PATH.'core/classes/model/mappers/ModelDataTypeMapper.php';

abstract class RestController extends FOSRestController implements ClassResourceInterface
{
    abstract protected function getEntity(Request $request);
    abstract protected function getFilterResolver(Request $request);
	
	public function cgetAction(Request $request)
	{
		try    	
		{
	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService($request)->find(
			        				$this->getEntity($request),
									$request->get('limit'),
									$request->get('offset')
							), 200
					)->setHeader("Cache-Control", "no-cache, must-revalidate")
			);
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
	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService($request)->get(
			        				$this->getEntity($request), $id
							), 200
					)->setHeader("Cache-Control", "no-cache, must-revalidate")
			);
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
	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService($request)->set(
			        				$this->getEntity($request),
				        			$this->getPostData($request)
							), 200
					)->setHeader("Cache-Control", "no-cache, must-revalidate")
			);
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
	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService($request)->set(
			        				$this->getEntity($request),
				        			$this->getPostData($request),
			        				$id
							), 200
					)->setHeader("Cache-Control", "no-cache, must-revalidate")
			);
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
	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService($request)->delete(
			        				$this->getEntity($request),
			        				$id
							), 200
					)->setHeader("Cache-Control", "no-cache, must-revalidate")
			);
		}
		catch( \Exception $e )
		{
			\Logger::getLogger('System')->error($e->getMessage());
			throw $this->createNotFoundException($e->getMessage());
		}
    }
    
    protected function getPostData(Request $request)
    {
    	return $request->request->all();
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
    			$this->getFilterResolver($request)
		);
    }
}