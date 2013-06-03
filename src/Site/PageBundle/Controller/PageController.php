<?php

namespace Site\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\PageBundle\Entity\Theme;

class PageController extends Controller
{
    public function indexAction()
    {
        return $this->render('SitePageBundle::index.html.twig');
    }
    
    public function mentionsAction()
    {
        return $this->render('SitePageBundle::mentions.html.twig');
    }
    
    public function aproposAction()
    {
        return $this->render('SitePageBundle::apropos.html.twig');
    }
    
    public function themesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $themes = $em->getRepository('SitePageBundle:Theme')->findAll();
        
        return $this->render('SitePageBundle:include:themes.html.twig', array(
            'themes' => $themes,
        ));
    }
    
    public function themeAction(Theme $theme = null)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        if($theme){
            $session->set('theme', $theme->getNom());
        }else{
            return $this->render('SitePageBundle::composants.html.twig');
        }
        return $this->redirect($this->generateUrl('theme'));
    }

    public function livredorAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $allLivre = $em->getRepository('SitePageBundle:Livre')->findBy(array('supprimer' => false));
        $form = $this->createFormBuilder()
            ->add('pseudo', 'text', array('label' => 'Votre pseudo : '))
            ->add('contenu', 'textarea', array('label' => 'Message : '))
            ->getForm();
        if($request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $livre = new Livre();
                $data = $form->getData();
                $livre->setPseudo($data['pseudo']);
                $livre->setContenu($data['contenu']);
                $em->persist($livre);
                $em->flush();
                $session->getFlashBag()->add('success', 'Message posté avec succès, merci.');
                return $this->redirect($this->generateUrl('livredor'));
            }
        }
        return $this->render('SitePageBundle::livredor.html.twig', array(
            'form' => $form->createView(),
            'livre' => $allLivre,
        ));
    }
    
    public function contactAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $form = $this->createFormBuilder()
            ->add('nom', 'text', array('label' => 'Votre nom : '))
            ->add('prenom', 'text', array('label' => 'Votre prénom : '))
            ->add('email', 'email', array('label' => 'Votre email : '))
            ->add('telephone', 'text', array('label' => 'Votre téléphone : '))
            ->add('sujet', 'text', array('label' => 'Sujet : '))
            ->add('contenu', 'textarea', array('label' => 'Message : '))
            ->getForm();
        if($request->getMethod() == 'POST'){
            $form->bind($request);
            $data = $form->getData();
            $mail = \Swift_Message::newInstance()
                ->setSubject('Contact | webenox.fr')
                ->setFrom(array('contact@webenox.fr' => 'webenox.fr'))
                ->setTo('contact@webenox.fr')
                ->setBody('<strong>Sujet</strong> : '.$data['sujet'].'<br /><strong>Message</strong> : '.$data['contenu'].'<br />--------------------------------------------------------------------------------------<br />('.$request->getClientIp().')<br />Nom : '.$data['nom'].'<br />Prénom : '.$data['prenom'].'<br />Email : '.$data['email'].'<br />Téléphone : '.$data['telephone'], 'text/html');
            $this->get('mailer')->send($mail);
            $session->getFlashBag()->add('success', 'Message envoyé avec succès, une réponse vous sera envoyée dans les prochaines 24h si nécessaire.');
            return $this->redirect($this->generateUrl('index'));
        }
        return $this->render('SitePageBundle::contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
