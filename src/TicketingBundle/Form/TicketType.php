<?php


namespace TicketingBundle\Form;


use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;



class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('type',      ChoiceType::class,
                array(
                    'choices' => array(
                    'Billet journée' => true,
                    'Billet demi-journée' =>false,
                ),
                    'label' => 'Type de billet',
                    'label_attr' => array('class' => 'fullOrHalfDay'),
                    'choice_attr' =>function($val,$key,$index){
                       return['class' => 'halfOrFull_'.$index];
                },

            ))
            ->add('firstName',TextType::class,
                array(
                    'label' => 'Nom',
                    'attr' => array('class' => 'text-center'),
                    'required' => true,
                ))
            ->add('lastName', TextType::class,
                array(
                    'label' =>'Prénom',
                    'attr' => array('class' => 'text-center'),
                    'required' => true,
                ))
            ->add('country', TextType::class,
                array(
                    'label' =>'Pays',
                    'attr' => array('class' => 'text-center'),
                    'required' => true,
                ))
            ->add('birthdayDate', BirthdayType::class,
                array(
                    'label' =>'Date de naissance',
                    'attr' => array('class' => 'text-center'),
                    'required' => true,
                    'widget' => 'choice',
                    'html5' => true,
                    'invalid_message' =>"La date est invalide",
                    'format' => 'dd-MM-yyyy',
                    'placeholder' => array(
                        'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour')
                ))
            ->add('reducedPrice', CheckboxType::class,
                array(
                    'label' =>'Tarif réduit',
                    'label_attr' =>array('title' =>'un justificatif sera demandé à l\'entrée du musée'),
                    'required' => false
                ))
        ;
     }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TicketingBundle\Entity\Ticket'
        ));
    }
}