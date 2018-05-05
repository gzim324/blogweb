<?php

namespace Zima\BlogwebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zima\BlogwebBundle\Entity\Post;
use Zima\BlogwebBundle\Entity\User;
use Zima\BlogwebBundle\Form\SettingsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_USER')")
 */
class UserController extends Controller
{
    /**
     * @Route("/user/{username}", name="user_board")
     * @Template()
     * @param User $user
     * @param Request $request
     * @return array
     */
    public function userBlogAction(User $user, Request $request) {

        $Repo = $this->getDoctrine()->getManager();
        $find_contents = $Repo->getRepository(Post::class)->findcontents($user);
        $info_about_user = $Repo->getRepository(User::class)->findInfo($user);

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $find_contents,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 14)
        );

        return array(
            'findContents' => $result,
            'infoAboutUser' => $info_about_user,
        );
    }

    /**
     * @Route("/user/settings/{id}", name="user_settings")
     * @param User $user
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Template()
     */
    public function settingsAction(Request $request, User $user) {

        if($this->getUser() != $user->getUsername()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(SettingsType::class, $user);
        if($request->isMethod('POST')) {
            $form->handleRequest($request);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'The information has been added');
            return $this->redirectToRoute("user_board", ["username" => $this->getUser()]);
        }


        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/delete/account/{id}", name="user_delete_account")
     * @param User $user
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAccountAction(User $user) {

        if($this->getUser() != $user->getUsername()) {
            throw new AccessDeniedException();
        }

        $user->setEnabled(false);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('danger', "Your account has deleted");
        return $this->redirectToRoute('fos_user_security_logout');
    }

    /**
     * @Route("/search/users", name="user_search")
     * @param Request $request
     * @return array
     * @Template()
     */
    public function searchUsersAction(Request $request) {

        $search_users = $this->getDoctrine()->getManager()->getRepository(User::class)->searchUsers($request);

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $search_users,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 14)
        );
        return array(
            'result' => $result
        );
    }
}