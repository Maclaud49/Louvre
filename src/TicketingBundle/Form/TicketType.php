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
                    'ticketing.form.other.fullday' => true,
                    'ticketing.form.other.halfday' =>false,
                ),
                    'label' => 'ticketing.form.label.type',
                    'required' =>true,
                    'label_attr' => array('class' => 'fullOrHalfDay'),
                    'choice_attr' =>function($val,$key,$index){
                       return['class' => 'halfOrFull_'.$index];
                },

            ))
            ->add('firstName',TextType::class,
                array(
                    'label' => 'ticketing.form.label.firstName',
                    'attr' => array('class' => 'text-center requiredMessage'),
                    'required' => true,
                ))
            ->add('lastName', TextType::class,
                array(
                    'label' =>'ticketing.form.label.lastName',
                    'attr' => array('class' => 'text-center requiredMessage'),
                    'required' => true,
                ))
            ->add('country', TextType::class,
                array(
                    'label' =>'ticketing.form.label.country',
                    'attr' => array('class' => 'text-center requiredMessage' ),
                    'required' => true,

                ))
            ->add('birthdayDate', BirthdayType::class,
                array(
                    'label' =>'ticketing.form.label.birthday',
                    'attr' => array('class' => 'text-center'),
                    'required' => true,
                    'widget' => 'choice',
                    'html5' => true,
                    'format' => 'dd-MM-yyyy',
                    'placeholder' => array(
                        'year' => 'ticketing.form.other.year', 'month' => 'ticketing.form.other.month', 'day' => 'ticketing.form.other.day')
                ))
            ->add('reducedPrice', CheckboxType::class,
                array(
                    'label' =>'ticketing.form.label.reducedPrice',
                    'label_attr' => array('class' => 'reducedPriceLabel'),
                    'attr' => array('id' => 'reducedPrice','title' =>'ticketing.form.other.credentials'),

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

    public function getName()
    {
        return 'ticket';
    }
}