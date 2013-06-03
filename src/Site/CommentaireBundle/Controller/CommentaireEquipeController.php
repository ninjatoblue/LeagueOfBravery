<?php

namespace Site\CommentaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommentaireEquipeController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SiteCommentaireBundle:Default:index.html.twig', array('name' => $name));
    }
}
