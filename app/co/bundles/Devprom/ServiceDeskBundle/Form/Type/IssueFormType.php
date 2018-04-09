<?php

namespace Devprom\ServiceDeskBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class IssueFormType extends AbstractType
{
    private $vpds = array();
    private $allowAttachment;
    private $em = null;
    private $user = null;

    function __construct($em, $vpds, $user, $allowAttachment = false)
    {
        $this->em = $em;
        $this->vpds = $vpds;
        $this->user = $user;
        $this->allowAttachment = $allowAttachment;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$vpds = $this->vpds;

    	$builder
            ->add('caption', 'text', array('label' => 'issue_caption'))
            ->add('description', 'textarea', array('label' => 'issue_description'))
            ->add('severity', 'entity', array(
                'class' => 'Devprom\ServiceDeskBundle\Entity\Severity',
                'label' => 'issue_severity'
            ));

        $result = $this->em->getRepository('DevpromServiceDeskBundle:IssueType')->findBy(array(
            "vpd" => $vpds,
            "visible" => 'Y'
        ));

        if ( count($result) > 0 ) {
            $builder
                ->add('issueType', 'entity', array(
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
                        );
                    },
                    'data' => array_shift($result),
                    'required' => false
                ));
        }

        $productResult = array();
        if ( $this->user->getCompany() ) {
            foreach( $this->user->getCompany()->getProducts() as $productCompany ) {
                $productResult[] = $productCompany->getProduct();
            }
        }
        if ( count($productResult) < 1 ) {
            $productResult = $this->em->getRepository('DevpromServiceDeskBundle:Product')->findBy(array(
                "vpd" => $vpds
            ));
        }

		if ( count($productResult) < 1 && count($this->vpds) > 1 )
		{
			$builder->add('project', 'entity', array(
                'label' => 'issue_project',
                'class' => 'Devprom\ServiceDeskBundle\Entity\Project',
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $er) use ($vpds) {
                    return $er->createQueryBuilder('p')->where('p.vpd IN (:vpdarray)')->setParameter('vpdarray', $vpds);
                },
                'required' => true
            ));
		}
		else
		{
			$builder->add('project', 'entity_hidden', array(
                'class' => 'Devprom\ServiceDeskBundle\Entity\Project'
            ));

            if ( count($productResult) > 0 ) {
                $builder->add('product', 'entity', array(
                    'label' => 'issue_product',
                    'class' => 'Devprom\ServiceDeskBundle\Entity\Product',
                    'choice_label' => 'name',
                    'query_builder' => function(EntityRepository $er) use ($productResult) {
                        return $er->createQueryBuilder('p')->where('p IN (:products)')
                            ->setParameter('products', $productResult)->orderBy('p.name', 'ASC');
                    },
                    'required' => false
                ));
            }
		}
            
        if ($this->allowAttachment) {
            $builder->add("newAttachment", new AttachmentFormType(), array(
                'label' => 'issue_newAttachment',
                'required' => false
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Devprom\ServiceDeskBundle\Entity\Issue',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'issue';
    }
}
