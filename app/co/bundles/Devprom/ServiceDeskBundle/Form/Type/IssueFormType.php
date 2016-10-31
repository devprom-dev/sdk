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
    private $showProducts = true;

    function __construct($vpds, $allowAttachment = false, $showProducts = true)
    {
        $this->vpds = $vpds;
        $this->allowAttachment = $allowAttachment;
        $this->showProducts = $showProducts;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$vpds = $this->vpds;

    	$builder
            ->add('caption', 'text')
            ->add('description', 'textarea')
            ->add('issueType', 'entity', array(
                'class' => 'Devprom\ServiceDeskBundle\Entity\IssueType',
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $er) use ($vpds) {
                    $qb = $er->createQueryBuilder('it');
                    return $qb->where($qb->expr()->eq('it.vpd', '\''.array_pop($vpds).'\''));
                },
                'empty_value' => 'issue_type_name',
                'required' => false     
            ))
            ->add('severity');
            
		if ( count($this->vpds) > 1 )
		{
			$builder->add('project', 'entity', array(
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
			$vpd = array_pop($vpds);
			
			$builder->add('project', 'entity_hidden', array(
                'class' => 'Devprom\ServiceDeskBundle\Entity\Project'
            ));

            if ( $this->showProducts ) {
                $builder->add('product', 'entity', array(
                    'class' => 'Devprom\ServiceDeskBundle\Entity\Product',
                    'choice_label' => 'name',
                    'query_builder' => function(EntityRepository $er) use ($vpd) {
                        $qb = $er->createQueryBuilder('p');
                        return $qb->where($qb->expr()->eq('p.vpd', '\''.$vpd.'\''));
                    },
                    'required' => false
                ));
            }
		}
            
        if ($this->allowAttachment) {
            $builder->add("newAttachment", new AttachmentFormType(), array(
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
