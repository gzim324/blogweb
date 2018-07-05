<?php

namespace Zima\BlogwebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zima\BlogwebBundle\Entity\Comments;
use Zima\BlogwebBundle\Entity\Post;
use Zima\BlogwebBundle\Entity\User;
use Zima\BlogwebBundle\Form\PostType;
use Zima\BlogwebBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PostController extends Controller
{
    /**
     * @Route("/", name="post_all")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function otherAction(Request $request) {
        //everyone has access

        $find_undeleted_post = $this->getDoctrine()->getManager()->getRepository(Post::class)->findUndeletedPost();

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $find_undeleted_post,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 14)
        );

        return array(
            'selectUndeletedPost' => $result
        );
    }

    /**
     * @Route("/friends/contents/{username}", name="post_friends")
     * @Template()
     * @param Request $request
     * @param User $user
     * @return array
     * @Security("has_role('ROLE_USER')")
     */
    public function friendsContentsAction(Request $request, User $user) {

        $entityManager = $this->getDoctrine()->getManager();

        $select_friends_post = $entityManager->getRepository(Post::class)->selectFriendsPost($user);

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $select_friends_post,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 14)
        );

        return array(
            'selectFriendsPost' => $result
        );
    }

    /**
     * @Route("/addcontents", name="post_add")
     * @Template()
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_USER')")
     */
    public function addAction(Request $request) {

        $post = new Post();
        $formPost = $this->createForm(PostType::class, $post);

        $formPost->handleRequest($request);
        if($request->isMethod('POST')) {
            if($formPost->isValid()){
                $post->setDeleted(Post::STATUS_DELETED_FALSE) //I set deleted on FALSE
                ->setOwner($this->getUser());  //I set the author of the content

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($post);
                $entityManager->flush();

                $this->addFlash('success', "The contents has been added");
                return $this->redirectToRoute("post_content", ['id' => $post->getId()]);
            }else{
                $this->addFlash('error', "The contents cannot be added");
            }
        }

        return array(
            'formPost' => $formPost->createView()
        );
    }

    /**
     * @Route("/contents/{id}", name="post_content")
     * @Template()
     * @param Post $post
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function contentsAction(Post $post, Request $request) {

        if($post->getDeleted() == Post::STATUS_DELETED_TRUE) {
            $this->addFlash("error", "This contents does not exist");
            return $this->redirectToRoute("user_board", ["username" => $this->getUser()]);
        }

        //////add comment
        $comments = new Comments();
        $comments->setOwner($this->getUser());  //I set the author of the comment
        $comments->setPosts($post);
        $commentForm = $this->createForm(CommentType::class, $comments);

        if($request->isMethod('POST')) {
            $commentForm->handleRequest($request);
            if($commentForm->isValid()){
                $comments->setDeleted(Comments::STATUS_DELETED_FALSE);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comments);
                $entityManager->flush();

                $this->addFlash('success', "The comment has been added");
                return $this->redirectToRoute("post_content", ['id' => $post->getId()]);
            }else{
                $this->addFlash('error', "The comment cannot be deleted");
            }
        }

        $selectComments = $this->getDoctrine()->getManager()->getRepository(Comments::class)->selectComment($comments);

        return array(
            'post' => $post,
            'commentForm' => $commentForm->createView(),
            'selectcomments' => $selectComments
        );
    }

    /**
     * @Route("/edit/contents/{id}", name="post_edit")
     * @Template()
     * @param Request $request
     * @param Post $post
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request, Post $post) {

        if($post->getDeleted() === Post::STATUS_DELETED_TRUE) {
            $this->addFlash("error", "This contents does not exist");
            return $this->redirectToRoute("user_board", ["username" => $this->getUser()]);
        }

        if($this->getUser() !== $post->getOwner()) {
            throw new AccessDeniedException();
        }

        $formPost = $this->createForm(PostType::class, $post);
        if($request->isMethod('POST')) {
            $formPost->handleRequest($request);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash("success", "The contents has been updated");

            return $this->redirectToRoute("post_content", ['id' => $post->getId()]);
        }

        return array(
            'formPost' => $formPost->createView()
        );
    }

    /**
     * @Route("/delete/contents/{id}", name="post_delete")
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_USER')")
     */
    public function deletedAction(Post $post) {

        if($this->getUser() !== $post->getOwner()) {
            throw new AccessDeniedException();
        }

        $post->setDeleted(Post::STATUS_DELETED_TRUE);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        $this->addFlash('warning', "The contents has been deleted");

        return $this->redirectToRoute('user_board', ["username" => $post->getId()]);
    }

    /**
     * @Route("/search/contents", name="post_search")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function searchContentsAction(Request $request) {

        $search_contents = $this->getDoctrine()->getManager()->getRepository(Post::class)->searchContents($request);

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $search_contents,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 14)
        );
        return array(
            'result' => $result
        );
    }
}