<?php

namespace TicketingBundle\Controller;

use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TicketingBundle\Entity\Order;
use TicketingBundle\Entity\Ticket;
use TicketingBundle\Form\OrderType;
use Symfony\Component\HttpFoundation\Request;
use TicketingBundle\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class BookingProcessController extends Controller
{
    public function bookingAction(Request $request)
    {

        $order = new Order();
        $form   = $this->get('form.factory')->create(OrderType::class, $order);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {


            //Set ticket price for each ticket
           foreach ($order->getTickets() as $ticket){
               $ticket->setTicketPrice();
           }

           //Set order price
            $orderAmount=0;
            foreach ($order->getTickets() as $ticket){
                $orderAmount += $ticket->getPrice();
            }
            $order->setOrderAmount($orderAmount);

            $this->get('session')->set('order',$order);

            $qty = $order->getQuantity();
            $this->get('session')->set('qty', $qty);



            return $this->redirectToRoute('ticketing_paiement'
            );
        }
            return $this->render('TicketingBundle:BookingProcess:booking.html.twig', array(
                'form' => $form->createView()
            ));

    }

    public function soldTicketsNbAction(Request $request){

        if ($request->isXMLHttpRequest()) {

            $orderRepository=$this->getDoctrine()
                ->getManager()
                ->getRepository('TicketingBundle:Order');

            $date =  $request->request->get('date');
            //for european time
            $timestamp = strtotime(str_replace('/','-',$date));
            $dateTimeFormat = 'd-m-Y H:i:s';
            $selectedDate = new \DateTime();
            // If you must have use time zones
            // $date = new \DateTime('now', new \DateTimeZone('Europe/Helsinki'));
            $selectedDate->setTimestamp($timestamp)->format($dateTimeFormat);
            $soldTicketsNb=$orderRepository->SoldTicketsNb($selectedDate);

            $day = date('D',$timestamp);

            $leftTickets = 1000-$soldTicketsNb;

            return new JsonResponse(array('ticketsLeft' => $leftTickets ,'day' =>$day));
        }
        return new JsonResponse('Il ne s\'agit pas d\'une requête AJAX');
    }


    public function paiementAction()
    {

        $order = $this->get('session')->get('order');

        return $this->render('TicketingBundle:BookingProcess:paiement.html.twig', array('order' => $order));
    }

    public function summaryAction()
    {
        $order = $this->get('session')->get('order');
        $email = $this->get('session')->get('email');
        $qty = $this->get('session')->get('qty');


        return $this->render('TicketingBundle:BookingProcess:summary.html.twig',  array('order' => $order, 'email' => $email, 'qty' => $qty));
    }

    public function mailOrderAction()
    {
        return $this->render('TicketingBundle:BookingProcess:mailOrder.html.twig');
    }

    public function checkoutAction(Request $request)
    {
        $order = $this->get('session')->get('order');
        $stripe = $this->get('ticketing.paiement.stripe');
        $recipientEmail = $request->get('stripeEmail');
        $this->get('session')->set('email', $recipientEmail);
        $token = $request->get('stripeToken');
        $em = $this->getDoctrine()->getManager();


        try {
            //Charge the customer
            $stripe->paiementByStripe($order, $token);

            //Send mail to the customer
            $this->mailTickets($order,$recipientEmail);

            //Save order on the bdd
            /*$em->persist($order);
            $em->flush();*/

            $this->addFlash("success", "La transaction a été validée");
            return $this->redirectToRoute('ticketing_summary');
        } catch (\Stripe\Error\Card $e){
            $this->addFlash("error", "La transaction n'a pas été validée");
            return $this->redirectToRoute("ticketing_paiement");
            // The card has been declined
        }
    }

    public function mailTickets(Order $order, $recipient)
    {
        $mailer = $this->container->get('mailer');
        $message = \Swift_Message::newInstance()
            ->setSubject('Justificatif pour votre visite du musée du Louvre')
            ->setFrom('billet.simple.alaska@gmail.com')
            ->setTo($recipient)
            ->setBody($this->renderView('TicketingBundle:Emails:Etickets.html.twig', array('order' => $order)),'text/html'
            )
            ->addPart($this->renderView('TicketingBundle:Emails:Etickets.text.twig', array('order' => $order)),'text/plain');
        $mailer->send($message);
    }

}
