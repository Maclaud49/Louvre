<?php

namespace TicketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TicketingBundle\Entity\Order;
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

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Type et qty validés.');

            return $this->redirectToRoute('ticketing_paiement');
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
        return $this->render('TicketingBundle:BookingProcess:paiement.html.twig');
    }

    public function summaryAction()
    {
        return $this->render('TicketingBundle:BookingProcess:summary.html.twig');
    }

    public function mailOrderAction()
    {
        return $this->render('TicketingBundle:BookingProcess:mailOrder.html.twig');
    }


}
