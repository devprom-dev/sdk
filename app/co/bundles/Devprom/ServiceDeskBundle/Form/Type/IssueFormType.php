<?php

namespace Devprom\ServiceDeskBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class IssueFormType extends AbstractType
{
    private $vpds = array();
    private $allowAttachment;

    function __construct($vpds, $allowAttachment = false)
    {
        $this->vpds = $vpds;
        $this->allowAttachment = $allowAttachment;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$vpds = $this->vpds;
		
    	$builder
            ->add('caption', 'text')
            ->add('description', 'textarea')
            ->add('issueType', 'entity', array(
                'class' => 'Devprom\ServiceDeskBundle\Entity\IssueType',
                'property' => 'name',
                'query_builder' => function(EntityRepository $er) use ($vpds) {
                    $qb = $er->createQueryBuilder('it');
                    return $qb->where($qb->expr()->eq('it.vpd', '\''.array_pop($vpds).'\''));
                }
            ))
            ->add('priority');
            
		if ( count($this->vpds) > 1 )
		{
			$builder->add('project', 'entity', array(
                'class' => 'Devprom\ServiceDeskBundle\Entity\Project',
                'property' => 'name',
                'query_builder' => function(EntityRepository $er) use ($vpds) {
                    return $er->createQueryBuilder('p')->where('p.vpd IN (:vpdarray)')->setParameter('vpdarray', $vpds);
                },
                'required' => true
            ));
		}
		else
		{
			$builder->add('product', 'entity', array(
                'class' => 'Devprom\ServiceDeskBundle\Entity\Product',
                'property' => 'name',
                'query_builder' => function(EntityRepository $er) use ($vpds) {
                    $qb = $er->createQueryBuilder('p');
                    return $qb->where($qb->expr()->eq('p.vpd', '\''.array_pop($vpds).'\''));
                },
                'required' => false
            ));
		}
            
        if ($this->allowAttachment) {
            $builder->add("newAttachment", new AttachmentFormType(), array(
                'required' => false
            ));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Devprom\ServiceDeskBundle\Entity\Issue'
        ));
    }

    public function getName()
    {
        return 'issue';
    }
}
