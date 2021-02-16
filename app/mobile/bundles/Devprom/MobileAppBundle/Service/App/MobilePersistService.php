<?php
namespace Devprom\MobileAppBundle\Service\App;
use Devprom\ProjectBundle\Service\Model\ModelService;
use Devprom\ProjectBundle\Service\Model\ModelChangeNotification;

class MobilePersistService
{
    private $userIt = null;

    function __construct( $project = '')
    {
        $this->userIt = getSession()->getUserIt();

        if ( $project != '' ) {
            \SessionBuilderProject::Instance()->openSession(array('project' => $project));
        }
    }

    public function storeData( $className, $objectId, $data )
    {
        $className = getFactory()->getClass($className);
        if ( !class_exists($className) ) return array();

        $dataService = $this->getDataModelService();
        try {
            $result = $dataService->set($className, $data, $objectId);
            if ( count($result) > 0 && $objectId < 1 ) {
                $result['mobile'] = '/mobile/form/' . $className . '/' . $result['Id'];
            }
            return $result;
        }
        catch( \Exception $e ) {
            return array(
                'error' => $e->getMessage()
            );
        }
    }

    public function storeComment( $className, $objectId, $data )
    {
        $className = getFactory()->getClass($className);
        if ( !class_exists($className) ) return array();

        $dataService = $this->getDataModelService();
        try {
            if ( $className == 'Comment' ) {
                $commentIt = getFactory()->getObject($className)->getExact($objectId);
                $objectIt = $commentIt->getAnchorIt();
                $data['ObjectClass'] = $commentIt->get('ObjectClass');
                $data['ObjectId'] = $commentIt->get('ObjectId');
                $data['PrevComment'] = $objectId;
                $data['VPD'] = $commentIt->get('VPD');
            }
            else {
                $objectIt = getFactory()->getObject($className)->getExact($objectId);
                $data['ObjectClass'] = strtolower($className);
                $data['ObjectId'] = $objectId;
                $data['VPD'] = $objectIt->get('VPD');
            }
            $data['AuthorId'] = $this->userIt->getId();

            $service = new ModelChangeNotification();
            $service->clearUser($objectIt, $this->userIt);

            return $dataService->set('Comment', $data);
        }
        catch( \Exception $e ) {
            return array(
                'error' => $e->getMessage()
            );
        }
    }

    public function dismissNotification($className, $objectId)
    {
        $className = getFactory()->getClass($className);
        if ( !class_exists($className) ) return array();

        $service = new ModelChangeNotification();
        $service->clearUser(getFactory()->getObject($className)->getExact($objectId), $this->userIt);
    }

    function getDataModelService()
    {
        return new ModelService(
            new \ModelValidator(
                array (
                    new \ModelValidatorObligatory(),
                    new \ModelValidatorTypes()
                )
            ),
            new \ModelDataTypeMapper(),
            array(),
            null,
            true
        );
    }
}