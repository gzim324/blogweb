<?php

namespace Zima\BlogwebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Zima\BlogwebBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_USER')")
 */
class FriendController extends Controller
{
    /**
     * @Route("/friends/{username}", name="friend_select")
     * @Template()
     * @param Request $request
     * @param User $user
     * @return array
     */
    public function tabFriendsAction(Request $request, User $user) {

        $selectFriends = $this->getDoctrine()->getManager()->getRepository(User::class)->selectFriends($user);

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $selectFriends,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 14)
        );

        return array(
            'selectFriends' => $result
        );
    }

    /**
     * @Route("/add/friend/{id}", name="friend_add")
     * @param User $user
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addFriendAction(User $user, $id) {

        $entityManager = $this->getDoctrine()->getManager();
        $friend = $entityManager->getRepository(User::class)->find($id);
        $owner = $this->getUser();
        $user->addFriends($owner, $friend);

        $entityManager->persist($user);
        $entityManager->flush();


        return $this->redirectToRoute("friend_select", ['username' => $this->getUser()]);
    }

    /**
     * @Route("/delete/friend/{id}", name="friend_delete")
     * @param User $user
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteFriendAction(User $user, $id) {

        $entityManager = $this->getDoctrine()->getManager();
        $friend = $entityManager->getRepository(User::class)->find($id);
        $owner = $this->getUser();
        $user->removeFriends($owner, $friend);

        $entityManager->persist($user);
        $entityManager->flush();

        // return $this->redirect($this->generateUrl('post_all'));
        return $this->redirectToRoute("friend_select", ['username' => $this->getUser()]);
    }
}