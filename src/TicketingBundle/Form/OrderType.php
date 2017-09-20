<?php


namespace TicketingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('bookingDate', DateType::class,
                array(
                    'label' =>'Date de la visite',
                    'label_attr' =>array('id' =>'bookingDateLabel'),
                    'widget' => 'single_text',
                    'html5' =>false,
                    'attr' => array('class' => 'bookingDatepicker text-center'),
                    'required' => false,
                    'format' => 'dd/MM/yyyy'
                ))

            ->add('quantity', IntegerType::class,
                array(
                'label' =>'Quantité désirée',
                'required' => false,
                'label_attr' =>array('id' =>'qtyLabel'),
                'attr' => array('class' => 'text-center'),
                //'data' => 0,
                    ))
            ->add('tickets', CollectionType::class,
                array(
                    'entry_type' => TicketType::class,
                    'entry_options' => array('attr' =>
                        array('class' =>'ticket-form')),
                    'allow_add' => true,
                    'allow_delete' =>true,
                    'error_bubbling' => false,
                    'label'=> false,

                ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TicketingBundle\Entity\Order'
        ));
    }
}