<?php

namespace Site\UtilisateurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\UtilisateurBundle\Entity\UtilisateurEquipe;
use Site\UtilisateurBundle\Entity\Utilisateur;
use Site\UtilisateurBundle\Entity\Equipe;
use Site\UtilisateurBundle\Entity\Message;
use Site\UtilisateurBundle\Form\EquipeType;
use Site\UtilisateurBundle\Form\EquipeMotDePasseType;
use Site\UtilisateurBundle\Form\EquipeInformationType;
use Site\UtilisateurBundle\Form\EquipeLogoType;

class EquipeController extends Controller
{
    public function creationAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        if($session->has('id')){

            $equipe = new Equipe();
            $form = $this->createForm(new EquipeType, $equipe);

            if($request->getMethod() == "POST"){
                $form->bind($request);
                $chef = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $session->get('id')));
                
                $equipe->setChef($chef);
                $equipe->setPassword(sha1($equipe->getPassword()));
                $ue = new UtilisateurEquipe();
                $utilisateur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $session->get('id')));
                $listeEquipes = $em->getRepository('SiteUtilisateurBundle:UtilisateurEquipe')->findBy(array('utilisateur' => $utilisateur));
                if(count($listeEquipes) > 3){
                    $session->getFlashBag()->add('error', 'Le nombre d\'équipe maximum est de 3');
                    return $this->redirect($this->generateUrl('equipe_gestion'));
                }
                $ue->setEquipe($equipe);
                $ue->setUtilisateur($utilisateur);
                $ue->setRang("Chef");
                $em->persist($equipe);
                $em->persist($ue);
                $em->flush();
                $session->getFlashBag()->add('success','Votre équipe a été créer avec succès !');

                return $this->redirect($this->generateUrl('equipe_gestion'));
            }

            return $this->render('SiteUtilisateurBundle:Equipe:creation.html.twig',array(
                    'form' => $form->createView()
                ));
            
        }else{
            $session->set('referer', $this->generateUrl('equipe_creation', array (), true));
            return $this->redirect($this->generateUrl('utilisateur_connexion'));
        }
    }
    
    public function gestionAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        
        if($session->has('id')){
            $utilisateurEquipe = new UtilisateurEquipe();
            $utilisateurEquipe = $em->getRepository('SiteUtilisateurBundle:UtilisateurEquipe')->findBy(array('utilisateur' => $session->get('id')));
        }else{
            $session->set('referer', $this->generateUrl('equipe_gestion', array (), true));
            return $this->redirect($this->generateUrl('utilisateur_connexion'));
        }
        
        return $this->render('SiteUtilisateurBundle:Equipe:gestion.html.twig', array(
            'utilisateurequipe' => $utilisateurEquipe
        ));
    }
    
    public function listeAction($page)
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
            ->add('motcle', 'text', array('label' => 'motcle'))
            ->getForm();

        if($request->getMethod() == 'POST'){
            $form->bind($request);
            $data = $form->getData();
            $motcle = $data['motcle'];
            $user_per_page = '100000';
            $users = $em->createQueryBuilder()
                ->select('u')
                ->from('SiteUtilisateurBundle:Equipe', 'u')
                ->where("u.nom LIKE :motcle OR u.tag LIKE :motcle")
                ->orderBy('u.nom ', 'ASC')
                ->setParameter('motcle', '%'.$motcle.'%')
                ->setFirstResult(($page * $user_per_page) - $user_per_page)
                ->setMaxResults($user_per_page)
                ->getQuery()
                ->getResult();
            $total_users = count($users);
            $last_page = '1';
            $previous_page = $page > 1 ? $page - 1 : 1;
            $next_page = $page < $last_page ? $page + 1 : $last_page;
        }else{
            $equipe = new Equipe();
            $equipe = $em->getRepository('SiteUtilisateurBundle:Equipe')->findAll();
            foreach($equipe as $k => $v) {
                if($v->getNbDefaite() != 0){
                    $ratio = $v->getNbVictoire() / $v->getNbDefaite();
                }else{
                    $ratio = $v->getNbVictoire();
                }
                $ratio = \round($ratio, 2);
                $v->setRatio($ratio);
                $em->persist($v);
            }
            $em->flush();
            $total = $em->getRepository('SiteUtilisateurBundle:Equipe')->createQueryBuilder('p')->getQuery()->getResult();
            $total_users = count($total);
            $user_per_page = $this->container->getParameter('max_team_on_page');
            $last_page = ceil($total_users / $user_per_page);
            $previous_page = $page > 1 ? $page - 1 : 1;
            $next_page = $page < $last_page ? $page + 1 : $last_page;
            $users = $em->getRepository('SiteUtilisateurBundle:Equipe')->createQueryBuilder('p')->orderBy('p.ratio', 'DESC')->setFirstResult(($page * $user_per_page) - $user_per_page)->setMaxResults($this->container->getParameter('max_team_on_page'))->getQuery()->getResult();
        }
        return $this->render('SiteUtilisateurBundle:equipe:liste.html.twig', array(
                'teams' => $users,
                'last_page' => $last_page,
                'previous_page' => $previous_page,
                'current_page' => $page,
                'next_page' => $next_page,
                'total_users' => $total_users,
                'user_per_page' => $user_per_page,
                'form' => $form->createView()
            ));
    }
    
    public function profilAction(Equipe $equipe)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        
        $utilisateurEquipe = new UtilisateurEquipe();
        $utilisateurEquipe = $em->getRepository('SiteUtilisateurBundle:UtilisateurEquipe')->findBy(array('equipe' => $equipe->getId()));
        
        if($equipe->getNbDefaite() != 0){
            $ratio = $equipe->getNbVictoire() / $equipe->getNbDefaite();
        }else{
            $ratio = $equipe->getNbVictoire();
        }
        $ratio = \round($ratio, 2);
        $equipe->setRatio($ratio);
        $em->persist($equipe);
        $em->flush();
        
        $verif = $em->getRepository('SiteUtilisateurBundle:UtilisateurEquipe')->findOneBy(array('utilisateur' => $session->get('id'), 'equipe' => $equipe->getId()));
        if($verif != NULL)
            $verif = true;
        else
           $verif = false;
        
        return $this->render('SiteUtilisateurBundle:equipe:profil.html.twig', array(
            'utilisateurequipe' => $utilisateurEquipe,
            'equipe' => $equipe,
            'verif' => $verif,
        ));
    }
    
    public function modifierAction(Equipe $equipe)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        
        if($session->get('id') == $equipe->getChef()->getId()){
            $formInfo = $this->createForm(new EquipeInformationType, $equipe);
            $formMdp = $this->createForm(new EquipeMotDePasseType);
            $formAvatar = $this->createForm(new EquipeLogoType);
            if($request->getMethod() == "POST"){
                if($request->request->get('equipeinformationtype')){
                     // INFORMATION
                    $formInfo->bind($request);
                    $data = $request->request->get('equipeinformationtype');
                    $equipe->setTag($data['tag']);
                    $equipe->setNom($data['nom']);
                    $equipe->setSite($data['site']);
                    $equipe->setDescription($data['description']);
                    $em->persist($equipe);
                    $em->flush();
                    $session->getFlashBag()->add('success', 'Modifications enregistrées avec succès');
                }elseif($request->request->get('equipemotdepassetype')){
                    // MOT DE PASSE
                    $formMdp->bind($request);
                    $data = $request->request->get('equipemotdepassetype');
                    $ancienMdp = sha1($data['ancienmdp']);
                    $newMdp = sha1($data['newmdp']);
                    $renewMdp = sha1($data['renewmdp']);
                    if($ancienMdp == $equipe->getPassword()){
                        if($newMdp == $renewMdp){
                            if(strlen($data['newmdp']) >= 5 AND strlen($data['newmdp'] <= 50)){
                                $equipe->setPassword($newMdp);
                                $em->persist($equipe);
                                $em->flush();
                                $session->getFlashBag()->add('success', 'Mot de passe modifié avec succès');
                            }else{
                                $session->getFlashBag()->add('error' ,'Le mot de passe doit contenir entre 5 et 50 caractères !');
                            }
                        }else{
                            $session->getFlashBag()->add('error', 'Confirmation inégale au nouveau mot de passe !');
                        }
                    }else{
                        $session->getFlashBag()->add('error', 'Ancien mot de passe incorrect !');
                    }
                }else{
                    // AVATAR
                    $formAvatar->bind($request);
                    $data = $formAvatar->getData();
                    $avatar = $data['logo'];
                    $extension = $avatar->getClientMimeType();
                    if($extension == 'image/jpeg' OR $extension == 'image/png'){
                        if($extension == 'image/jpeg'){
                            $extension = 'jpg';
                        }elseif($extension == 'image/png'){
                            $extension = 'png';
                        }
                        if($avatar->getClientSize() >= 15360 AND $avatar->getClientSize() <= 1048576){
                            $name = uniqid().'.'.$extension;
                            $avatar->move('logo',$name);
                            $equipe->setLogo($name);
                            $em->persist($equipe);
                            $em->flush();
                            $session->getFlashBag()->add('success', 'Logo modifié avec succès');
                        }else{
                            $session->getFlashBag()->add('error','La taille de l\'image doit être entre 15Ko et 1MO !');
                            return $this->redirect($this->generateUrl('equipe_modification', array('id' => $equipe->getId())));
                        }
                    }else{
                        $session->getFlashBag()->add('error','L\'image doit être en format .jpg ou .png !');
                        return $this->redirect($this->generateUrl('equipe_modification', array('id' => $equipe->getId())));
                    }
                }
            }
        }else{
            $session->getFlashBag()->add('error', 'Vous n\'avez pas le droit de faire cela !');
            return $this->redirect($this->generateUrl('index'));
        }
        
        return $this->render('SiteUtilisateurBundle:equipe:modification.html.twig',array(
                    'equipe' => $equipe,
                    'formInfo' => $formInfo->createView(),
                    'formMdp' => $formMdp->createView(),
                    'formAvatar' => $formAvatar->createView(),
                ));
    }
    
    public function rejoindreAction(Equipe $equipe)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        if(!$session->has('id')){
            return $this->redirect($this->generateUrl('index'));
        }
        $form = $this->createFormBuilder()
            ->add('password', 'password', array('label' => 'Mot de passe :'))
            ->getForm();
        if($request->getMethod() == "POST"){
            $form->bind($request);
            $verif = new UtilisateurEquipe();
            $verif = $em->getRepository('SiteUtilisateurBundle:UtilisateurEquipe')->findOneBy(array('equipe' => $equipe->getId(), 'utilisateur' => $session->get('id')));
            if($verif == NULL){
                if($equipe->getBanni() == false){
                    $nbMembres = $em->getRepository('SiteUtilisateurBundle:UtilisateurEquipe')->findOneBy(array('equipe' => $equipe->getId()));
                    if(count($nbMembres) < 7){
                        $data = $form->getData();
                        $verifmdp = $em->getRepository('SiteUtilisateurBundle:Equipe')->findOneBy(array('password' => sha1($data['password'])));
                        if($verifmdp){
                            $utilisateur = new Utilisateur();
                            $utilisateur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $session->get('id')));
                            $ue = new UtilisateurEquipe();
                            $ue->setEquipe($equipe);
                            $ue->setUtilisateur($utilisateur);
                            $em->persist($ue);
                            $session->getFlashBag()->add('success', 'Vous venez de rejoindre l\'équipe '.$equipe->getNom().'. Félicitation !');
                            // MP
                            $admin = new Utilisateur();
                            $admin = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('pseudo' => 'Administrateur'));
                            $message = new Message();
                            $message->setAuteur($admin);
                            $message->setReceveur($ue->getEquipe()->getChef());
                            $message->setTitre($utilisateur->getPseudo().' a rejoint votre équipe.');
                            $message->setContenu($utilisateur->getPseudo().' a rejoint votre équipe "'.$ue->getEquipe()->getNom().'" le '.$ue->getDatetime()->format('d/m/Y à G\hi').'<br /><br />
                                Ceci est un message automatique, merci de ne pas y répondre.');
                            $em->persist($message);
                            $em->flush();
                            // EMAIL
                            if($ue->getEquipe()->getChef()->getEmailAuth() == true){
                                $mail = \Swift_Message::newInstance()
                                    ->setSubject('Un membre a rejoint votre équipe | league-of-bravery.com')
                                    ->setFrom(array('webmaster@league-of-bravery.com' => 'league-of-bravery.com'))
                                    ->setTo($ue->getEquipe()->getChef()->getEmail())
                                    ->setBody($this->renderView('SiteUtilisateurBundle:mail:nouveauMembre.html.twig', array(
                                        'ue' => $ue,
                                        'utilisateur' => $utilisateur,
                                    )),'text/html');
                                $this->get('mailer')->send($mail);
                            }
                        }else{
                            $session->getFlashBag()->add('error', 'Le mot de passe est incorrect !');
                        }
                    }else{
                        $session->getFlashBag()->add('error', 'L\'équipe contient le maximum de membres possible !');
                    }
                }else{
                    $session->getFlashBag()->add('error', 'Vous ne pouvez pas rentrer dans une équipe banni !');
                }
            }else{
                $session->getFlashBag()->add('error', 'Vous faites déjà partie de cette équipe !');
            }
            return $this->redirect($this->generateUrl('equipe_profil', array('id' => $equipe->getId())));
        }
        return $this->render('SiteUtilisateurBundle:equipe:rejoindre.html.twig', array(
            'equipe' => $equipe,
            'form' => $form->createView(),
        ));
    }
    
    public function quitterAction(Equipe $equipe)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        if($equipe->getChef()->getId() != $session->get('id')){
            if($equipe->getBanni() == false){
                $ue = new UtilisateurEquipe();
                $ue = $em->getRepository('SiteUtilisateurBundle:UtilisateurEquipe')->findOneBy(array('equipe' => $equipe->getId(), 'utilisateur' => $session->get('id')));
                $session->getFlashBag()->add('success', 'Vous venez de quitter l\'équipe "'.$equipe->getNom().'" avec succès !');
                // MP
                $admin = new Utilisateur();
                $admin = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('pseudo' => 'Administrateur'));
                $message = new Message();
                $message->setAuteur($admin);
                $message->setReceveur($ue->getEquipe()->getChef());
                $message->setTitre($ue->getUtilisateur()->getPseudo().' a quitté votre équipe.');
                $datetime = new \DateTime('now');
                $message->setContenu($ue->getUtilisateur()->getPseudo().' a quitté votre équipe "'.$ue->getEquipe()->getNom().'" le '.$datetime->format('d/m/Y à G\hi').'<br /><br />
                    Ceci est un message automatique, merci de ne pas y répondre.');
                // EMAIL
                if($ue->getEquipe()->getChef()->getEmailAuth() == true){
                    $mail = \Swift_Message::newInstance()
                        ->setSubject('Un membre a quitté votre équipe | league-of-bravery.com')
                        ->setFrom(array('webmaster@league-of-bravery.com' => 'league-of-bravery.com'))
                        ->setTo($ue->getEquipe()->getChef()->getEmail())
                        ->setBody($this->renderView('SiteUtilisateurBundle:mail:perduMembre.html.twig', array(
                            'datetime' => $datetime,
                            'ue' => $ue,
                        )),'text/html');
                    $this->get('mailer')->send($mail);
                }
                $em->remove($ue);
                $em->persist($message);
                $em->flush();
            }else{
                $session->getFlashBag()->add('error', 'Vous ne pouvez pas quitter une équipe qui est banni !');
            }
        }else{
            $session->getFlashBag()->add('error', 'Le chef ne peux pas quitter sont équipe !');
        }
        
        return $this->redirect($this->generateUrl('equipe_profil', array('id' => $equipe->getId())));
    }
    
    public function virerAction($idUtilisateur, $idEquipe)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        
        $utilisateur = new Utilisateur();
        $utilisateur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $idUtilisateur));
        $equipe = new Equipe();
        $equipe = $em->getRepository('SiteUtilisateurBundle:Equipe')->findOneBy(array('id' => $idEquipe));
        
        if($equipe->getChef()->getId() == $session->get('id')){
            $ue = new UtilisateurEquipe();
            $ue = $em->getRepository('SiteUtilisateurBundle:UtilisateurEquipe')->findOneBy(array('utilisateur' => $utilisateur->getId(), 'equipe' => $equipe->getId()));
            if($ue){
                // MP
                $admin = new Utilisateur();
                $admin = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('pseudo' => 'Administrateur'));
                $message = new Message();
                $message->setAuteur($admin);
                $message->setReceveur($ue->getUtilisateur());
                $message->setTitre($ue->getEquipe()->getChef()->getPseudo().' vous a viré.');
                $datetime = new \DateTime('now');
                $message->setContenu($ue->getEquipe()->getChef()->getPseudo().' vous a viré de l\'équipe "'.$ue->getEquipe()->getNom().'" le '.$datetime->format('d/m/Y à G\hi').'<br /><br />
                    Ceci est un message automatique, merci de ne pas y répondre.');
                // EMAIL
                if($ue->getUtilisateur()->getEmailAuth() == true){
                    $mail = \Swift_Message::newInstance()
                        ->setSubject('Quelqu\'un vous a viré | league-of-bravery.com')
                        ->setFrom(array('webmaster@league-of-bravery.com' => 'league-of-bravery.com'))
                        ->setTo($ue->getUtilisateur()->getEmail())
                        ->setBody($this->renderView('SiteUtilisateurBundle:mail:virerMembre.html.twig', array(
                            'datetime' => $datetime,
                            'ue' => $ue,
                        )),'text/html');
                    $this->get('mailer')->send($mail);
                }
                $em->persist($message);
                $em->remove($ue);
                $em->flush();
                $session->getFlashBag()->add('success', 'Membre exclu avec succès !');
            }else{
                $session->getFlashBag()->add('error', 'Membre introuvable');
            }
        }else{
            $session->getFlashBag()->add('error', 'Vous n\'avez pas le droit de faire cela');
            return $this->redirect($this->generateUrl('index'));
        }
        
        return $this->redirect($this->generateUrl('equipe_profil', array('id' => $equipe->getId())));
    }
    
    public function supressionAction(Equipe $equipe)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        
        if($equipe->getChef()->getId() == $session->get('id')){
            if($equipe->getBanni() == false){
                $utilisateurequipe = new UtilisateurEquipe();
                $utilisateurequipe = $em->getRepository('SiteUtilisateurBundle:UtilisateurEquipe')->findBy(array('equipe' => $equipe->getId()));
                foreach($utilisateurequipe as $k => $v){
                    $em->remove($v);
                }
                $em->remove($equipe);
                $em->flush();
                $session->getFlashBag()->add('success', 'Votre équipe a été supprimé avec succès !');
            }else{
                $session->getFlashBag()->add('error', 'Votre équipe est banni, vous ne pouvez pas la supprimer');
            }
        }else{
            $session->getFlashBag()->add('error', 'Vous n\'êtes pas le chef de l\'équipe !');
        }
        return $this->redirect($this->generateUrl('equipe_gestion'));
    }
    
    public function bannirAction(Equipe $equipe)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        if($equipe->getChef()->getId() != $session->get('id') AND $session->get('rang') == "Administrateur"){
            if($equipe->getBanni() == false){
                $equipe->setBanni(true);
            }else{
                $equipe->setBanni(false);
            }
            $em->persist($equipe);
            $em->flush();
            return $this->redirect($this->generateUrl('equipe_profil', array(
                'id' => $equipe->getId(),
            )));
        }else{
            $session->getFlashBag()->add('error', 'Vous n\'avez pas la permission de faire cela !');
            return $this->redirect($this->generateUrl('index'));
        }
    }
}

?>
