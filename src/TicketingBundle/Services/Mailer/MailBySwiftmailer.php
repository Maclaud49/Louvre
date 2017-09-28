<?php

namespace TicketingBundle\Services\Mailer;

use Symfony\Component\Translation\DataCollectorTranslator;
use TicketingBundle\Entity\Order;

class MailBySwiftmailer{

    private $mailer;
    private $twig;
    private $from;
    private $translator;

    public function  __construct(\Swift_Mailer $mailer,\Twig_Environment $twig,String $from, \Symfony\Component\Translation\TranslatorInterface $translator){

        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from =$from;
        $this->translator = $translator;
    }

    public function mailTickets(Order $order, String $recipient, String $locale)
    {

        $body = $this->renderTemplateHTML($order, $locale);
        $part = $this->renderTemplateText($order, $locale);
        $subject = $this->translator->trans('ticketing.email.subject');
        $logo = $this->translator->trans('ticketing.email.logo');
        

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->from)
            ->setTo($recipient)
            ->attach(\Swift_Attachment::fromPath('C:\xampp2\htdocs\www\Symfony\web\images\louvre_logo.jpg')->setFilename($logo))
            ->setBody($body,'text/html'
            )
            ->addPart($part,'text/plain');
        $this->mailer->send($message);


        //For 1&1 server
        /*$headers = "From: tickets@museedulouvre.fr\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
        mail($recipient, $subject, $body, $headers);*/



    }

    public function renderTemplateHTML(Order $order, String $locale)
    {
        return $this->twig->render(
            'TicketingBundle:Emails:Etickets.html.twig',
            array(
                'order' => $order,
                'locale' => $locale
            )
        );
    }

    public function renderTemplateText(Order $order, String $locale)
    {
        return $this->twig->render(
            'TicketingBundle:Emails:Etickets.text.twig',
            array(
                'order' => $order,
                'locale' => $locale
            )
        );
    }

}