<?php

namespace BlueMedia\TaskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction() {
        return $this->render('BlueMediaTaskBundle:Default:index.html.twig', array());
    }

}
