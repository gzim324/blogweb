<?php

namespace Zima\BlogwebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zima\BlogwebBundle\Entity\Comments;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Zima\BlogwebBundle\Form\CommentType;

/**
 * @Security("has_role('ROLE_USER')")
 */
class CommentController extends Controller
{
    /**
     * @Route("/delete/comment/{id}", name="comment_delete")
     * @param Comments $comments
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_USER')")
     */
    public function deletedCommentAction(Comments $comments) {

        if($this->getUser() !== $comments->getOwner()) {
            throw new AccessDeniedException();
        }

        $comments->setDeleted(Comments::STATUS_DELETED_TRUE);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comments);
        $entityManager->flush();

        $this->addFlash('warning', "The comment has been deleted");
        return $this->redirectToRoute('post_content', ['id' => $comments->getPosts()->getId()]);
    }

    /**
     * @Route("/edit/comment/{id}", name="comment_edit")
     * @Template("@ZimaBlogweb/Comment/edit.html.twig")
     * @param Request $request
     * @param Comments $comments
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request, Comments $comments) {

        if($comments->isDeleted() === Comments::STATUS_DELETED_TRUE) {
            $this->addFlash("error", "This contents does not exist");
            return $this->redirectToRoute("user_board", ["username" => $this->getUser()]);
        }

        if($this->getUser() !== $comments->getOwner()) {
            throw new AccessDeniedException();
        }

        $formComment = $this->createForm(CommentType::class, $comments);
        if($request->isMethod('POST')) {
            $formComment->handleRequest($request);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comments);
            $entityManager->flush();

            $this->addFlash("success", "The comment has been updated");

            return $this->redirectToRoute("post_content", ['id' => $comments->getPosts()->getId()]);
        }

        return array(
            'formComment' => $formComment->createView()
        );
    }
}