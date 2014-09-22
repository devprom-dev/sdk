<?php

namespace Devprom\ServiceDeskBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class IssueFormType extends AbstractType
{
    private $projectVPD;

    private $allowAttachment;

    function __construct($projectVPD, $allowAttachment = false)
    {
        $this->projectVPD = $projectVPD;
        $this->allowAttachment = $allowAttachment;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $projectVPD = $this->projectVPD;

        $builder
            ->add('caption', 'text')
            ->add('description', 'textarea')
            ->add('issueType', 'entity', array(
                'class' => 'Devprom\ServiceDeskBundle\Entity\IssueType',
                'property' => 'name',
                'query_builder' => function(EntityRepository $er) use ($projectVPD) {
                    $qb = $er->createQueryBuilder('it');
                    return $qb->where($qb->expr()->eq('it.vpd', '\''.$projectVPD.'\''));
                }
            ))
            ->add('product', 'entity', array(
                'class' => 'Devprom\ServiceDeskBundle\Entity\Product',
                'property' => 'name',
                'query_builder' => function(EntityRepository $er) use ($projectVPD) {
                    $qb = $er->createQueryBuilder('p');
                    return $qb->where($qb->expr()->eq('p.vpd', '\''.$projectVPD.'\''));
                }
            ))
            ->add('priority');
        if ($this->allowAttachment) {
            $builder->add("newAttachment", new AttachmentFormType(), array(
                'required' => false,
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
