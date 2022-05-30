<?php
namespace Devprom\ServiceDeskBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class IssueFeedbackFormType extends AbstractType
{
    private $em = null;
    private $translator;

    function __construct($em, $translator) {
        $this->em = $em;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
            ->add('feedback', ChoiceType::class,
                array(
                    'choices' => [
                        '★' => '1',
                        '★★' => '2',
                        '★★★' => '3',
                        '★★★★' => '4',
                        '★★★★★' => '5'
                    ]
                )
            )
            ->add('feedbackText', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Devprom\ServiceDeskBundle\Entity\Issue',
            'csrf_protection' => false,
            'user' => null,
            'allowAttachment' => false
        ));
    }

    public function getName()
    {
        return 'issue-feedback';
    }
}
