<?php

namespace Devprom\ServiceDeskBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type\HiddenEntityType;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class IssueFormType extends AbstractType
{
    private $em = null;
    private $translator;

    function __construct($em, $translator) {
        $this->em = $em;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$vpds = $options['vpds'];

    	$builder
            ->add('caption', TextType::class, array('label' => 'issue_caption'))
            ->add('description', TextareaType::class, array(
                    'label' => 'issue_description'
                ));

    	if ( defined('SERVICE_DESK_SEVERITY_REQUIRED') ? SERVICE_DESK_SEVERITY_REQUIRED : true ) {
            $builder->add('severity', EntityType::class, array(
                'class' => 'Devprom\ServiceDeskBundle\Entity\Severity',
                'label' => 'issue_severity',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.orderNum', 'asc');
                },
                'placeholder' => '-- '.$this->translator->trans('issue_severity').' --'
            ));
        }
    	else {
            $builder->add('severity', HiddenEntityType::class, array(
                'class' => 'Devprom\ServiceDeskBundle\Entity\Severity'
            ));
        }

        $result = $this->em->getRepository('DevpromServiceDeskBundle:IssueType')->findBy(
            array(
                "vpd" => $vpds,
                "visible" => 'Y'
            ),
            array(
                'orderNum' => 'asc'
            )
        );

        if ( count($result) > 0 ) {
            $builder
                ->add('issueType', EntityType::class, array(
                    'label' => 'issue_issueType',
                    'class' => 'Devprom\ServiceDeskBundle\Entity\IssueType',
                    'choice_label' => 'name',
                    'query_builder' => function(EntityRepository $er) use ($result) {
                        $qb = $er->createQueryBuilder('it');
                        return $qb->where(
                            $qb->expr()->in('it.id', array_map(
                                function($row) {
                                    return $row->getId();
                                },
                                $result
                            ))
                        )->orderBy('it.orderNum', 'asc');
                    },
                    'data' => array_shift($result),
                    'required' => defined('SERVICE_DESK_ISSUETYPE_REQUIRED') ? SERVICE_DESK_ISSUETYPE_REQUIRED : false,
                    'placeholder' => '-- '.$this->translator->trans('issue_issueType').' --'
                ));
        }

        $vpdWithProducts = array();
        $projectWithProducts = $this->em->getRepository('DevpromServiceDeskBundle:Project')->findBy(array(
            "vpd" => $vpds,
            "knowledgeBaseUseProducts" => 'Y'
        ));
        foreach( $projectWithProducts as $project ) {
            $vpdWithProducts[] = $project->getVpd();
        }

        $productParms = array(
            "vpd" => array_intersect($vpds, $vpdWithProducts),
        );

        $allowedProductTypes = $this->em->getRepository('DevpromServiceDeskBundle:ProductType')->findBy(array(
            "vpd" => $vpds,
            "hasIssues" => 'Y'
        ));
        if ( count($allowedProductTypes) > 0 ) {
            $productParms['type'] = $allowedProductTypes;
        }
        $productParms['type'] = array_merge($productParms['type'], array(null));

        $user = $options['user'];
        if ( $user->getCompany() ) {
            foreach( $user->getCompany()->getProducts() as $productCompany ) {
                $productParms["id"][] = $productCompany->getProduct()->getId();
            }
        }

        $productResult = $this->em->getRepository('DevpromServiceDeskBundle:Product')->findBy($productParms);
		if ( count($productResult) < 1 && count($vpds) > 1 )
		{
			$builder->add('project', EntityType::class, array(
                'label' => 'issue_project',
                'class' => 'Devprom\ServiceDeskBundle\Entity\Project',
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $er) use ($vpds) {
                    return $er->createQueryBuilder('p')->where('p.vpd IN (:vpdarray)')->setParameter('vpdarray', $vpds);
                },
                'required' => true,
                'placeholder' => '-- '.$this->translator->trans('issue_product').' --'
            ));
		}
		else
		{
            $projects = $this->em->getRepository('DevpromServiceDeskBundle:Project')->findBy(array(
                "vpd" => $vpds
            ));

            if ( count($productResult) > 0 ) {
                $builder->add('project', HiddenEntityType::class, array(
                    'class' => 'Devprom\ServiceDeskBundle\Entity\Project',
                    'data' => array_shift($projects)
                ));
                $builder->add('product', EntityType::class, array(
                    'label' => 'issue_product',
                    'class' => 'Devprom\ServiceDeskBundle\Entity\Product',
                    'choice_label' => 'name',
                    'query_builder' => function(EntityRepository $er) use ($productResult) {
                        return $er->createQueryBuilder('p')->where('p IN (:products)')
                            ->setParameter('products', $productResult)->orderBy('p.name', 'ASC');
                    },
                    'required' => defined('SERVICE_DESK_PRODUCT_REQUIRED') ? SERVICE_DESK_PRODUCT_REQUIRED : false,
                    'placeholder' => '-- '.$this->translator->trans('issue_product').' --'
                ));
            }
            else {
                $builder->add('project', HiddenEntityType::class, array(
                    'class' => 'Devprom\ServiceDeskBundle\Entity\Project',
                    'data' => array_shift($projects)
                ));
            }
		}
            
        if ($options['allowAttachment']) {
            $builder->add("newAttachment", AttachmentFormType::class, array(
                'label' => 'issue_newAttachment',
                'required' => false
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Devprom\ServiceDeskBundle\Entity\Issue',
            'csrf_protection' => false,
            'vpds' => array(),
            'user' => null,
            'allowAttachment' => false
        ));
    }

    public function getName()
    {
        return 'issue';
    }
}
