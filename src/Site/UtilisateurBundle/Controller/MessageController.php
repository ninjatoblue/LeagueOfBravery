<?php

namespace Site\UtilisateurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\UtilisateurBundle\Entity\Utilisateur;
use Site\UtilisateurBundle\Entity\Message;

class MessageController extends Controller
{

    public function nouveauAction(Utilisateur $receveur = null)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getEntityManager();
        if($session->has('id')){
            $auteur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $session->get('id')));
            $message = new Message();
            if(isset($receveur)){
                $form = $this->createFormBuilder($message)
                    ->add('titre', 'text', array('label' => 'Sujet : '))
                    ->add('receveur', 'entity', array(
                        'class' => 'SiteUtilisateurBundle:Utilisateur',
                        'property' => 'pseudo',
                        'preferred_choices' => array($receveur),
                        'label' => 'Destinataire :',
                    ))
                    ->add('contenu', 'textarea', array('attr' => array('class' => 'ckeditor'), 'required' => false, 'label' => 'Votre message : '))
                    ->getForm();
            }else{
                $form = $this->createFormBuilder($message)
                    ->add('titre', 'text', array('label' => 'Sujet : '))
                    ->add('receveur', 'entity', array(
                        'class' => 'SiteUtilisateurBundle:Utilisateur',
                        'property' => 'pseudo',
                        'empty_value' => '',
                        'label' => 'Destinataire :',
                    ))
                    ->add('contenu', 'textarea', array('attr' => array('class' => 'ckeditor'), 'required' => false, 'label' => 'Votre message : '))
                    ->getForm();
            }
            if($request->getMethod() == 'POST'){
                $form->bind($request);
                $message->setAuteur($auteur);
                $em->persist($message);
                $em->flush();
                // EMAIL
                if($message->getReceveur()->getEmailAuth() == true){
                    $adresse = $this->generateUrl('mp_reception', array(), true);
                    $mail = \Swift_Message::newInstance()
                        ->setSubject('league-of-bravery.com | Un nouveau message privé est arrivé')
                        ->setFrom(array('contact@league-of-bravery.com' => 'league-of-bravery.com'))
                        ->setTo($message->getReceveur()->getEmail())
                        ->setBody($this->renderView('SiteUtilisateurBundle:mail:mp.html.twig', array('message' => $message, 'adresse' => $adresse)), 'text/html');
                    $this->get('mailer')->send($mail);
                }
                $session->getFlashBag()->add('success', 'Message envoyé avec succès !');
                return $this->redirect($this->generateUrl('mp_reception'));
            }
        }else{
            if(isset($receveur)){
                $session->set('referer', $this->generateUrl('mp_nouveau', array('id' => $receveur->getId()), true));
            }else{
                $session->set('referer', $this->generateUrl('mp_nouveau', array(), true));
            }
            return $this->redirect($this->generateUrl('utilisateur_connexion'));
        }
        return $this->render('SiteUtilisateurBundle:message:nouveau.html.twig', array(
                'form' => $form->createView(),
                'utilisateur' => $receveur,
            ));
    }

    public function lireAction(Message $mp)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getEntityManager();
        if($session->has('pseudo')){
            if($mp->getReceveur()->getId() == $session->get('id')){
                if($mp->getEtat() == false){
                    $mp->setEtat(true);
                    $mp->setDateOuverture(new \DateTime('now'));
                    $em->persist($mp);
                    $em->flush();
                }
            }elseif($mp->getAuteur()->getId() == $session->get('id')){
                
            }else{
                $session->getFlashBag()->add('error', "Vous n'avez pas la permission de faire cela !");
                return $this->redirect($this->generateUrl('index'));
            }
        }else{
            $session->set('referer', $this->generateUrl('mp_lire', array('id' => $mp->getId()), true));
            return $this->redirect($this->generateUrl('utilisateur_connexion'));
        }
        return $this->render('SiteUtilisateurBundle:message:lire.html.twig', array(
                'mp' => $mp,
            ));
    }

    public function receptionAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getEntityManager();
        if($session->has('pseudo')){
            $utilisateur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $session->get('id')));
            $messagesNonLu = $em->getRepository('SiteUtilisateurBundle:Message')->findBy(array('etat' => false, 'supprimer' => false, 'receveur' => $utilisateur));
            $messagesLu = $em->getRepository('SiteUtilisateurBundle:Message')->findBy(array('etat' => true, 'supprimer' => false, 'receveur' => $utilisateur));
            $messagesEnvoye = $em->getRepository('SiteUtilisateurBundle:Message')->findBy(array('auteur' => $utilisateur));

            return $this->render('SiteUtilisateurBundle:message:reception.html.twig', array(
                    'messagesNonLu' => $messagesNonLu,
                    'messagesLu' => $messagesLu,
                    'messagesEnvoye' => $messagesEnvoye,
                ));
        }else{
            $session->set('referer', $this->generateUrl('mp_reception', array(), true));
            return $this->redirect($this->generateUrl('utilisateur_connexion'));
        }
    }

    public function envoyesAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getEntityManager();
        if($session->has('id')){
            $utilisateur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $session->get('id')));
            $messagesEnvoye = $em->getRepository('SiteUtilisateurBundle:Message')->findBy(array('auteur' => $utilisateur));

            return $this->render('SiteUtilisateurBundle:message:envoyes.html.twig', array(
                    'messagesEnvoye' => $messagesEnvoye,
                ));
        }else{
            $session->set('referer', $this->generateUrl('mp_reception', array(), true));
            return $this->redirect($this->generateUrl('utilisateur_connexion'));
        }
    }

    public function supprimerAction(Message $mp)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getEntityManager();
        if($session->has('pseudo')){
            if($mp->getReceveur()->getId() == $session->get('id')){
                $mp->setSupprimer(true);
                $em->persist($mp);
                $em->flush();
                $session->getFlashBag()->add('success', 'Message supprimé avec succès !');
            }else{
                $session->getFlashBag()->add('error', "Vous n'avez pas la permission de faire cela !");
                return $this->redirect($this->generateUrl('index'));
            }
        }else{
            $session->set('referer', $this->generateUrl('mp_supprimer', array('id' => $mp->getId()), true));
            return $this->redirect($this->generateUrl('utilisateur_connexion'));
        }
        return $this->redirect($this->generateUrl('mp_reception'));
    }

}

?>
