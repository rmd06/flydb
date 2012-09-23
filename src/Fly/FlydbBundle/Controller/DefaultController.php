<?php

namespace Fly\FlydbBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FlyFlydbBundle:Default:index.html.twig', array('name' => $name));
    }
}
