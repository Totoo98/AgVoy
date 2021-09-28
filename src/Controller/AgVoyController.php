<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgVoyController extends AbstractController
{
    /**
     * @Route("/ag/voy", name="ag_voy")
     */
    public function index(): Response
    {
        return $this->render('ag_voy/index.html.twig', [
            'controller_name' => 'AgVoyController',
        ]);
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

        //$res = '...';
        //...

        //$res .= '<p/><a href="' . $this->generateUrl('films_index') . '">Back</a>';

        $res = '<p/>' . $room->getSummary() . '</p>';

        return new Response('<html><body>'. $res . '</body></html>');
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

        $htmlpage = '<!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>Region list!</title>
            </head>
            <body>
                <h1>Room list</h1>
                <p>Here are all the rooms of the region:</p>
                <ul>';
        
        $roomRepo = $this->getDoctrine()->getRepository('App:Room');
        $rooms = $roomRepo->findAll();

        if (!$rooms) {
        throw $this->createNotFoundException('The rooms does not exist');
        }

        foreach($rooms as $room) {
            if($room->getRegions()->contains($region))
            {
                $htmlpage .= '<li>
                 <a href="/room/'.$room->getId().'">'.$room->getSummary().'</a></li>';
            }
        }
        $htmlpage .= '</ul>';

        $htmlpage .= '</body></html>';
        
        return new Response(
            $htmlpage,
            Response::HTTP_OK,
            array('content-type' => 'text/html')
            );
    }
}
