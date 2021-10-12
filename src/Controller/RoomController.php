<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/room")
 */
class RoomController extends AbstractController
{
    /**
     * @Route("/", name="room_index", methods={"GET"})
     */
    public function index(RoomRepository $roomRepository): Response
    {
        return $this->render('room/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    /**
     * Mark a room in the panier in the user's session
     * 
     * @Route("/cart", name="room_cart", methods={"GET"})
     */
    public function showCart(RoomRepository $roomRepository): Response
    {
        $panier = $this->get('session')->get('panier');
        if(!is_array($panier))
        {
            $panier = array();
        }

        $allRooms = $roomRepository->findAll();
        $rooms = array();
        foreach($allRooms as $room)
        {
            if(in_array($room->getId(), $panier))
            {
                $rooms[] = $room;
            }
        }

        return $this->render('room/cart.html.twig', [
            'rooms' => $rooms
        ]);
    }

    /**
     * @Route("/new", name="room_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /* Change content-type according to image's
            $imagefile = $room->getImageFile();
            if($imagefile) {
                $mimetype = $imagefile->getMimeType();
                $room->setContentType($mimetype);
            }*/

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="room_show", methods={"GET"})
     */
    public function show(Room $room): Response
    {
        return $this->render('room/show.html.twig', [
            'room' => $room,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="room_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Room $room): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="room_delete", methods={"POST"})
     */
    public function delete(Request $request, Room $room): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('room_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Mark a room in the panier in the user's session
     * 
     * @Route("/mark/{id}", name="room_mark", requirements={ "id": "\d+"}, methods={"GET"})
     */
    public function markAction(Room $room): Response
    {
        dump($room);

        $id = $room->getId();

        $panier = $this->get('session')->get('panier');
        if(!is_array($panier))
        {
            $panier = array();
        }
        
        if (!in_array($id, $panier) ) 
        {
            $panier[] = $id;
        }
        else
        {
            $panier = array_diff($panier, array($id));
        }

        $this->get('session')->set('panier', $panier);

        return $this->redirectToRoute('room_show', 
        ['id' => $room->getId()]);
    }
}
