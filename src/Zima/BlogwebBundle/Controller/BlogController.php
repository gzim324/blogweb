<?php

namespace Zima\BlogwebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zima\BlogwebBundle\Entity\Comments;
use Zima\BlogwebBundle\Entity\Post;
use Zima\BlogwebBundle\Entity\User;
use Zima\BlogwebBundle\Form\PostType;
use Zima\BlogwebBundle\Form\SettingsType;
use Zima\BlogwebBundle\Form\CommentType;


class BlogController extends Controller
{
    /**
     * @Route("/", name="blog_other")
     * @Template()
     * @return array
     */
    public function otherAction(Request $request)
    {
        //kazdy ma dostęp
//
        $rows = $this->getDoctrine()->getManager()->getRepository(Post::class)->findUndeletedPost();


        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $rows,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 14)
        );

        return array(
            'rows' => $result
        );
    }

    /**
     * @Route("/user/{username}", name="blog_user")
     * @Template()
     * @return array
     * @param User $user
     * @param Request $request
     */
    public function userBlogAction(User $user, Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER"); //tylko zalogowany

        $rows = $this->getDoctrine()->getManager()->getRepository(Post::class)->findcontents($user);
        $rows1 = $this->getDoctrine()->getManager()->getRepository(User::class)->findInfo($user);

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $rows,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 14)
        );

        return array(
            'rows' => $result,
            'rows1' => $rows1
        );
    }

    /**
     * @Route("/friends/contents", name="blog_home")
     * @Template()
     * @return array
     * @param Request $request
     */
    public function friendsContentsAction(Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER"); //tylko zalogowany

        $rows = $this->getDoctrine()->getManager()->getRepository(Post::class)->findUndeletedPost();


        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $rows,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 14)
        );

        return array(
            'rows' => $result
        );
    }

    /**
     * @Route("/addcontents", name="blog_addcontents")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Template()
     */
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if($request->isMethod('POST')) {
            if($form->isValid()){
                $post->setDeleted(Post::STATUS_DELETED_FALSE) //ustawiam usunięty na FALSE
                ->setOwner($this->getUser());  //ustawiam autora wpisu

                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();

                $this->addFlash('success', "The contents has been added");
                return $this->redirectToRoute("blog_content", ['id' => $post->getId()]);
            }else{
                $this->addFlash('error', "The contents cannot be added");
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/mycontents/{id}", name="blog_mycontent")
     * @Template()
     * @param Post $post
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function contentsUserAction(Post $post, Request $request) {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if($post->getDeleted() == Post::STATUS_DELETED_TRUE) {
            $this->addFlash("error", "This contents does not exist");
            return $this->redirectToRoute("blog_user", ["username" => $this->getUser()]);
        }

        if($this->getUser() !== $post->getOwner()) {
            return $this->redirectToRoute("blog_content", ['id' => $post->getId()]);
        }

        //////add comment
        $comments = new Comments();
        $comments->setOwner($this->getUser());  //ustawiam autora wpisu
        $comments->setPosts($post);
        $commentForm = $this->createForm(CommentType::class, $comments);

        if($request->isMethod('POST')) {
            $commentForm->handleRequest($request);
            if($commentForm->isValid()){
                $comments->setDeleted(Comments::STATUS_DELETED_FALSE);
                $em = $this->getDoctrine()->getManager();
                $em->persist($comments);
                $em->flush();

                $this->addFlash('success', "The comment has been added");
                return $this->redirectToRoute("blog_content", ['id' => $post->getId()]);
            }else{
                $this->addFlash('error', "The comment cannot be added");
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
     * @Route("/contents/{id}", name="blog_content")
     * @param Post $post
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @return Response
     * @Template()
     */
    public function contentsAction(Post $post, Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if($post->getDeleted() == Post::STATUS_DELETED_TRUE) {
            $this->addFlash("error", "This contents does not exist");
            return $this->redirectToRoute("blog_user", ["username" => $this->getUser()]);
        }

        if($this->getUser() === $post->getOwner()) {
            return $this->redirectToRoute("blog_mycontent", ['id' => $post->getId()]);
        }

        //////add comment
        $comments = new Comments();
        $comments->setOwner($this->getUser());  //ustawiam autora wpisu
        $comments->setPosts($post);
        $commentForm = $this->createForm(CommentType::class, $comments);

        if($request->isMethod('POST')) {
            $commentForm->handleRequest($request);
            if($commentForm->isValid()){
                $comments->setDeleted(Comments::STATUS_DELETED_FALSE);
                $em = $this->getDoctrine()->getManager();
                $em->persist($comments);
                $em->flush();

                $this->addFlash('success', "The comment has been added");
                return $this->redirectToRoute("blog_content", ['id' => $post->getId()]);
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
     * @Route("/edit/contents/{id}", name="blog_edit")
     * @param Request $request
     * @param Post $post
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Template()
     */
    public function editAction(Request $request, Post $post)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if($post->getDeleted() === Post::STATUS_DELETED_TRUE) {
            $this->addFlash("error", "This contents does not exist");
            return $this->redirectToRoute("blog_user", ["username" => $this->getUser()]);
        }

        if($this->getUser() !== $post->getOwner()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(PostType::class, $post);
        if($request->isMethod('POST')) {
            $form->handleRequest($request);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash("success", "The contents has been updated");

            return $this->redirectToRoute("blog_content", ['id' => $post->getId()]);
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/delete/contents/{id}", name="blog_delete")
     * @return Response
     */
    public function deletedAction(Post $post)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if($this->getUser() !== $post->getOwner()) {
            throw new AccessDeniedException();
        }

        $post->setDeleted(Post::STATUS_DELETED_TRUE);

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        $this->addFlash('warning', "The contents has been deleted");

        return $this->redirectToRoute('blog_user', ["username" => $this->getUser()]);
    }

    /**
     * @Route("/delete/comment/{id}", name="blog_del_comment")
     * @return Response
     */
    public function deletedCommentAction(Comments $comments)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if($this->getUser() !== $comments->getOwner()) {
            throw new AccessDeniedException();
        }

        $comments->setDeleted(Comments::STATUS_DELETED_TRUE);

        $em = $this->getDoctrine()->getManager();
        $em->persist($comments);
        $em->flush();

        $this->addFlash('warning', "The comment has been deleted");

        return $this->redirect($this->generateUrl("blog_content", ['id' => $comments->getPosts()]));
    }

    /**
     * @Route("/user/settings/{id}", name="blog_settings")
     * @param User $user
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Template()
     */
    public function settingsAction(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if(!$this->getUser()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(SettingsType::class, $user);
        if($request->isMethod('POST')) {
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

            $this->addFlash('success', 'The information has been added');
            return $this->redirectToRoute("blog_user", ["username" => $this->getUser()]);
        }


        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/search", name="blog_search")
     * @param Request $request
     * @return array
     * @Template()
     */
    public function searchContentsAction(Request $request) {

        $rows = $this->getDoctrine()->getManager()->getRepository('ZimaBlogwebBundle:Post')->searchContents($request);

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $rows,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 14)
        );
        return array(
            'result' => $result
        );
    }
}