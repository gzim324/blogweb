<?php

namespace Zima\BlogwebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zima\BlogwebBundle\Entity\Comments;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_USER')")
 */
class CommentController extends Controller
{
    /**
     * @Route("/delete/comment/{id}", name="comment_delete")
     * @param Comments $comments
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletedCommentAction(Comments $comments) {

        $this->denyAccessUnlessGranted("ROLE_USER");

        if($this->getUser() !== $comments->getOwner()) {
            throw new AccessDeniedException();
        }
        $comments->setDeleted(Comments::STATUS_DELETED_TRUE);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comments);
        $entityManager->flush();

        $this->addFlash('warning', "The comment has been deleted");
        return $this->redirectToRoute('post_all');
    }
}