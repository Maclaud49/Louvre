<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class CoreController extends Controller
{

  public function indexAction()
  {
      return $this->redirectToRoute("ticketing_booking");
  }
}
