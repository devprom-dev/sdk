<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Devprom\ProjectBundle\Service\Model\ModelService;

include_once SERVER_ROOT_PATH.'core/classes/model/validation/ModelValidator.php';
include_once SERVER_ROOT_PATH.'core/classes/model/mappers/ModelDataTypeMapper.php';

abstract class RestController extends FOSRestController implements ClassResourceInterface
{
	public function cgetAction()
	{
		try    	
		{
	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService()->find(
			        				$this->getEntity(),
			        				$this->getRequest()->get('limit'),
			        				$this->getRequest()->get('offset')
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
	
    public function getAction($id)
    {
		try    	
		{
	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService()->get(
			        				$this->getEntity(), $id
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
	
	public function cpostAction()
    {
		try    	
		{
	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService()->set(
			        				$this->getEntity(),
				        			$this->getRequest()->request->all()
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
    
	public function putAction($id)
    {
		try    	
		{
	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService()->set(
			        				$this->getEntity(),
				        			$this->getRequest()->request->all(),
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

	public function deleteAction($id)
    {
		try    	
		{
	        return $this->handleView(
	        		$this->view(
			        		$this->getModelService()->delete(
			        				$this->getEntity(),
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
    
    abstract protected function getEntity();
    
    abstract protected function getFilterResolver();
    
    protected function getModelService()
    {
    	return new ModelService(
    			new \ModelValidator(
						array (
								new \ModelValidatorTypes()
    					)
				), 
    			new \ModelDataTypeMapper(), 
    			$this->getFilterResolver()
		);
    }
}