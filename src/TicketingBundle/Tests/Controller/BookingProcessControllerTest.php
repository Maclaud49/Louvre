<?php

namespace TicketingBundle\Tests\Controller;

use TicketingBundle\Form\OrderType;
use TicketingBundle\Entity\Order;
use TicketingBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;


class BookingProcessControllerTest extends WebTestCase{

    public function testOrderProcess(){

        $client = static::createClient();
        $crawler = $client->request('POST', 'fr/ticketing/booking');


        ///////////////////Ticket form functionnal test
        $form = $crawler->selectButton('validate-btn')->form(array(
            'order[bookingDate]' => '28/08/2018',
            'order[quantity]' => 1,
            //'order[tickets]' => 'order[tickets][0]',
            'order[tickets][0][type]' => 1,
            'order[tickets][0][firstName]' => 'Mickaël',
            'order[tickets][0][lastName]' => 'Test',
            'order[tickets][0][country]' => 'France',
            'order[tickets][0][birthdayDate][day]' => 13,
            'order[tickets][0][birthdayDate][month]' => 4,
            'order[tickets][0][birthdayDate][year]' => 1981,
            'order[tickets][0][reducedPrice]' => 1,

        ));

        $crawler = $client->submit($form);

        $this->assertTrue(
            $client->getResponse()->isRedirect('/fr/ticketing/payment'),
            'response is a redirect to /fr/ticketing/payment');

        ////////////////////////////////////Stripe functionnal test
        $crawler = $client->followRedirect();


        $link = $crawler->selectLink('Modifier mes informations')->link();
        $crawler = $client->click($link);

        $this->assertContains('/fr/ticketing/booking', $client->getResponse()->getContent(),
            'response is a redirect to /fr/ticketing/booking');

        //Back to payment page
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        $recipient = 'mickael@parlow-co.com';
        $type = 'card';

        //Invalid token case;
        $token = 'tok_chargeDeclinedFraudulent';
        $formStripe = $crawler->filter('#stripeForm')->form(
        array(
            'stripeToken' => $token,
            'stripeEmail' =>$recipient,));

        $crawler = $client->submit($formStripe);

        $this->assertTrue(
            $client->getResponse()->isRedirect('/fr/ticketing/payment'),
            'response is a redirect to /fr/ticketing/summary');

        $crawler = $client->followRedirect();

        $this->assertContains('La transaction a été déclinée.',$client->getResponse()->getContent());

        //Valid case token
        $token = 'tok_visa';
        $formStripe = $crawler->filter('#stripeForm')->form(
            array(
                'stripeToken' => $token,
                'stripeEmail' =>$recipient,));

        $crawler = $client->submit($formStripe);

        $this->assertTrue(
            $client->getResponse()->isRedirect('/fr/ticketing/summary'),
            'response is a redirect to /fr/ticketing/summary');

        $crawler = $client->followRedirect();
        $this->assertContains('La transaction a été validée.',$client->getResponse()->getContent());
    }

}