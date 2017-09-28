<?php

namespace TicketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TicketingBundle\Entity\Order;
use TicketingBundle\Form\OrderType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;




class BookingProcessController extends Controller
{
    public function bookingAction(Request $request)
    {



        if( $this->get('session')->get('order')==null){
            $order = new Order();
        }
        else{
            $order = $this->get('session')->get('order');
            $orderNew = new Order();
            $this->get('session')->set('order',$orderNew);
        }

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

            return $this->redirectToRoute('ticketing_payment');
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


    public function paymentAction(Request $request)
    {

        $order = $this->get('session')->get('order');
        $locale = $request->attributes->get('_locale');

        return $this->render('TicketingBundle:BookingProcess:payment.html.twig', array('order' => $order, 'locale' => $locale));
    }

    public function summaryAction(Request $request)
    {
        $order = $this->get('session')->get('order');
        $email = $this->get('session')->get('email');
        $qty = $this->get('session')->get('qty');
        $em = $this->getDoctrine()->getManager();
        $locale = $request->attributes->get('_locale');

        if($email !=null) {
            //Save order on the bdd
            $em->persist($order);
            $em->flush();
            $orderNew = new Order();
            $this->get('session')->set('order', $orderNew);
            $this->get('session')->set('email', null);
        }


        return $this->render('TicketingBundle:BookingProcess:summary.html.twig',  array('order' => $order, 'email' => $email, 'qty' => $qty,'locale' => $locale));
    }


    public function checkoutAction(Request $request)
    {
        $order = $this->get('session')->get('order');
        $stripe = $this->get('ticketing.payment.stripe');
        $recipientEmail = $request->get('stripeEmail');
        $this->get('session')->set('email', $recipientEmail);
        $token = $request->get('stripeToken');
        $locale = $request->attributes->get('_locale');

        $mailer = $this->get('ticketing.mail.swiftmailer');


            try {
                //Charge the customer
                $stripe->paymentByStripe($order, $token);


                //Send mail to the customer
                $mailer->mailTickets($order, $recipientEmail, $locale);


                $this->addFlash("success", "ticketing.summaryPage.successMessage");
                return $this->redirectToRoute('ticketing_summary');
            } catch (\Stripe\Error\Card $e) {
                $this->addFlash("error", "ticketing.paymentPage.stripe.errorDeclinedMessage");
                return $this->redirectToRoute("ticketing_payment");
                // The card has been declined
            } catch (\Stripe\Error\RateLimit $e) {
                // Too many requests made to the API too quickly
                $this->addFlash("error", "ticketing.paymentPage.stripe.errorTooFastMessage");
                return $this->redirectToRoute("ticketing_payment");
            } catch (\Stripe\Error\InvalidRequest $e) {
                // Invalid parameters were supplied to Stripe's API
                $this->addFlash("error", "ticketing.paymentPage.stripe.errorInvalidParaMessage");
                return $this->redirectToRoute("ticketing_payment");
            } catch (\Stripe\Error\Authentication $e) {
                // Authentication with Stripe's API failed
                // (maybe you changed API keys recently)
                $this->addFlash("error", "ticketing.paymentPage.stripe.errorAPIKeyMessage");
                return $this->redirectToRoute("ticketing_payment");
            } catch (\Stripe\Error\ApiConnection $e) {
                // Network communication with Stripe failed
                $this->addFlash("error", "ticketing.paymentPage.stripe.errorNetworkMessage");
                return $this->redirectToRoute("ticketing_payment");
            } catch (\Stripe\Error\Base $e) {
                // Display a very generic error to the user, and maybe send
                // yourself an email
                $this->addFlash("error", "ticketing.paymentPage.stripe.errorMessage");
                return $this->redirectToRoute("ticketing_payment");
            } catch (\Exception $e) {
                // Something else happened, completely unrelated to Stripe
                $this->addFlash("error", "ticketing.paymentPage.stripe.errorMessage");
                return $this->redirectToRoute("ticketing_payment");
            }


    }




}
