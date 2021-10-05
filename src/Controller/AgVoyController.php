<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgVoyController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * Show a room
     * 
     * @Route("/room/{id}", name="room_show", requirements={"id"="\d+"})
     *    note that the id must be an integer, above
     *    
     * @param Integer $id
     */
    public function room_show($id)
    {
        $roomRepo = $this->getDoctrine()->getRepository('App:Room');
        $room = $roomRepo->find($id);

        if (!$room) {
        throw $this->createNotFoundException('The room does not exist');
        }

        return $this->render('room/room_show.html.twig',
        [ 'room' => $room ]
        );
    }



    /**
     * Show a film
     * 
     * @Route("/region/{id}", name="region_show", requirements={"id"="\d+"})
     *    note that the id must be an integer, above
     *    
     * @param Integer $id
     */
    public function region_show($id)
    {
        $regionRepo = $this->getDoctrine()->getRepository('App:Region');
        $region = $regionRepo->find($id);

        if (!$region) {
        throw $this->createNotFoundException('The region does not exist');
        }
        
        $roomRepo = $this->getDoctrine()->getRepository('App:Room');
        $rooms = $roomRepo->findAll();

        if (!$rooms) {
        throw $this->createNotFoundException('The rooms does not exist');
        }

        $roomsInRegion = array();

        foreach($rooms as $room) {
            if($room->getRegions()->contains($region))
            {
                $roomsInRegion[] = $room;
            }
        }
        
        return $this->render('region/region_show.html.twig',
        ['rooms' => $roomsInRegion]
        );
    }
}
