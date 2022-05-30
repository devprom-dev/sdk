<?php
namespace Devprom\ServiceDeskBundle\Twig;
use Devprom\ProjectBundle\Service\Model\ModelService;
use Devprom\ServiceDeskBundle\Entity;

class ShowAttributeTwigExtention extends \Twig\Extension\AbstractExtension
{
    private $container;

    function __construct($container) {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig\TwigFunction('showattribute', function($entity, $attributeUserName)
            {
                if ( ! $entity instanceof \Devprom\ServiceDeskBundle\Entity\Issue ) {
                    return get_class($entity);
                }

                global $session;
                $session = new \PMSession( $entity->getProject()->getCodeName(),
                    new \AuthenticationFactory(
                        is_object(getSession())
                            ? getSession()->getUserIt()
                            : getFactory()->getObject('User')->createCachedIterator(
                                    array (
                                        array (
                                            'Caption' => '',
                                            'Email' => ''
                                        )
                                    )
                              )
                    )
                );
                getFactory()->setAccessPolicy(new \AccessPolicy(getFactory()->getCacheService()));

                $object = getFactory()->getObject('Request');
                $objectIt = $object->getExact($entity->getId());

                $result = ModelService::computeFormula(
                    $objectIt, '{' . $attributeUserName . '}'
                );
                $lines = array();
                foreach ($result as $computedItem) {
                    if (!is_object($computedItem)) {
                        $lines[] = $computedItem == '0' ? '' : $computedItem;
                    } else {
                        $lines[] = $computedItem->getDisplayName();
                    }
                }
                return join(', ', $lines);
            }),
        );
    }

    /**
     * Returns the name of the extension.
     * @return string The extension name
     */
    public function getName()
    {
        return "showattribute";
    }
}
