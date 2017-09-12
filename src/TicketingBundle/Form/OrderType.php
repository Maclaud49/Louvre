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
                    'invalid_message' =>"La date est invalide",
                    'required' => false,
                    'format' => 'dd/MM/yyyy'
                ))

            ->add('quantity', IntegerType::class,
                array(
                'invalid_message' =>'Merci de saisir un chiffre compris entre 1 et 100',
                'label' =>'Quantité désirée',
                'required' => false,
                'label_attr' =>array('id' =>'qtyLabel'),
                'attr' => array('class' => 'text-center'),
                'data' => 0,
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

            $builder->addEventListener(
                FormEvents::PRE_SUBMIT,    // 1er argument : L'évènement qui nous intéresse : ici, PRE_SET_DATA
                function(FormEvent $event) { // 2e argument : La fonction à exécuter lorsque l'évènement est déclenché
                    // On récupère notre objet Advert sous-jacent
                    //$order = $event->getData();
                    //$qty = $order->getQuantity();
                   // var_dump($order);

                    /*if (null === $order) {
                        return; // On sort de la fonction sans rien faire lorsque $advert vaut null
                    }

                    $tickets = $order->getTickets();
                    $orderAmount = 0;
                    foreach($tickets as $ticket){
                        $ticket->setTicketPrice();
                        $orderAmount += $ticket->getPrice();
                        $order->setOrderAmount($orderAmount);
                    }*/


                }
            );
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TicketingBundle\Entity\Order'
        ));
    }
}