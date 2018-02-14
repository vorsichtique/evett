<?php

namespace App\Controller;

use App\Provider\ParisApiProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EventController extends Controller
{

    /**
     * @Route("/", name="home")
     */
    public function index(ParisApiProvider $paris)
    {

        $events = $paris->get();
        usort($events, function($a, $b){
            if($a->getStart() > $b->getStart()){
                return 1;
            }
            if($a->getStart() < $b->getStart()){
                return -1;
            }
            return 0;
        });

        return $this->render('index.html.twig',
            [
                'events' => $events
            ]);
    }
}