<?php

namespace TicketingBundle\Stripe;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Templating\EngineInterface;
use TicketingBundle\Entity\Order;
use TicketingBundle\TicketingBundle;

class PaiementStripe{

    private $APIKey;

    public function  __construct(String $APIKey){

        $this->APIKey = $APIKey;
    }

    /**
     * @param Order $order
     * @param String $token
     * Create a charge to the user's card
     */
    public function paiementByStripe(Order $order, String $token)
    {
        \Stripe\Stripe::setApiKey($this->APIKey);

        $amount = $order->getOrderAmount();

            $charge = \Stripe\Charge::create(array(
                "amount" => $amount * 100, // Amount in cents
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Stripe"
            ));
    }




}