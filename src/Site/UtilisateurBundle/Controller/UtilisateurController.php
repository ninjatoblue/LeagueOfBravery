<?php

namespace Site\UtilisateurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\UtilisateurBundle\Form\UtilisateurInscriptionType;
use Site\UtilisateurBundle\Form\UtilisateurEmailType;
use Site\UtilisateurBundle\Form\UtilisateurInformationType;
use Site\UtilisateurBundle\Form\UtilisateurMotDePasseType;
use Site\UtilisateurBundle\Form\UtilisateurChangeMdpType;
use Site\UtilisateurBundle\Form\UtilisateurMdpOublieType;
use Site\UtilisateurBundle\Form\UtilisateurAvatarType;
use Site\CommentaireBundle\Form\CommentaireUtilisateurType;
use Site\UtilisateurBundle\Entity\Utilisateur;
use Site\CommentaireBundle\Entity\CommentaireUtilisateur;
use Symfony\Component\HttpFoundation\Response;

class UtilisateurController extends Controller
{

    public function profilAction(Utilisateur $utilisateur)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        
        $commentaire = new CommentaireUtilisateur();
        $form = $this->createForm(new CommentaireUtilisateurType, $commentaire);
        $commentaires = $em->getRepository('SiteCommentaireBundle:CommentaireUtilisateur')->findBy(array('supprimer' => false, 'utilisateur' => $utilisateur->getId()));
        
        if($request->getMethod() == 'POST'){
            $receveur = new Utilisateur();
            $receveur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $utilisateur->getId()));
            $auteur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $session->get('id')));
            // AJOUT D'UN COMMENTAIRE
            $form->bind($request);
            if($form->isValid()){
                if(\strlen($commentaire->getContenu()) < 1000){
                    $commentaire->setUtilisateur($receveur);
                    $commentaire->setAuteur($auteur);
                    $em->persist($commentaire);
                    $em->flush();
                    $session->setFlash('success', 'Commentaire ajouté avec succès !');

                    if($utilisateur->getId() != $session->get('id')){
                        // MP
                        $admin = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('pseudo' => 'Administrateur'));
                        $message = new Message();
                        $message->setAuteur($admin);
                        $message->setReceveur($receveur);
                        $message->setTitre('Nouveau commentaire sur votre profil !');
                        $message->setContenu('<p>Un nouveau commentaire vient d\'être posté sur votre profil "<a href="'.$this->generateUrl('websio_cours_consulter', array('matiere' => $cours->getMatiere()->getSlug(), 'id' => $cours->getId(), 'slug' => $cours->getSlug())).'">'.$cours->getTitre().'</a>" par : <a href="'.$this->generateUrl('websio_utilisateur_profil', array('id' => $session->get('id'))).'">'.$session->get('pseudo').'</a></p><p>Ceci est un message automatique, merci de ne pas répondre.</p>');
                        $em->persist($message);
                        $em->flush();
                        // EMAIL
                        if($utilisateur->getEmailAuth() == true){
                            $adresse = $this->generateUrl('mp_reception', array(), true);
                            $mail = \Swift_Message::newInstance()
                                ->setSubject('League of Bravery | Un nouveau message privé est arrivé')
                                ->setFrom(array('contact@league-of-bravery.com' => 'league-of-bravery.com'))
                                ->setTo($message->getReceveur()->getEmail())
                                ->setBody($this->renderView('SiteUtilisateurBundle:mail:mp.html.twig', array('message' => $message, 'adresse' => $adresse)), 'text/html');
                            $this->get('mailer')->send($mail);
                        }
                    }                        
                    return $this->redirect($this->generateUrl('websio_cours_consulter', array('matiere' => $cours->getMatiere()->getSlug(), 'id' => $cours->getId(), 'slug' => $cours->getSlug()), true).'#commentaires');
                }
            }
        }
        
        $utilisateurequipe = $em->getRepository('SiteUtilisateurBundle:UtilisateurEquipe')->findBy(array('utilisateur' => $utilisateur->getId()));
        
        if($utilisateur->getNbDefaite() != 0){
            $ratio = $utilisateur->getNbVictoire() / $utilisateur->getNbDefaite();
        }else{
            $ratio = $utilisateur->getNbVictoire();
        }
        $ratio = \round($ratio, 2);
        $utilisateur->setRatio($ratio);
        $em->persist($utilisateur);
        $em->flush();

        return $this->render('SiteUtilisateurBundle::profil.html.twig', array(
                'utilisateur' => $utilisateur,
                'utilisateurequipe' => $utilisateurequipe,
                'commentaires' => $commentaires,
                'form' => $form->createView(),
            ));
    }

    public function preferencesAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $utilisateur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $session->get('id')));
        if($session->has('id')){
            $formInfo = $this->createForm(new UtilisateurInformationType, $utilisateur);
            $formMdp = $this->createForm(new UtilisateurMotDePasseType);
            $formAvatar = $this->createForm(new UtilisateurAvatarType);
            $formEmail = $this->createForm(new UtilisateurEmailType, $utilisateur);
            if($request->getMethod() == 'POST'){
                // INFORMATION
                if($request->request->get('utilisateurinformationtype')){
                    $formInfo->bind($request);
                    $data = $request->request->get('utilisateurinformationtype');
                    if(isset($data['emailAuth'])){
                        $utilisateur->setEmailAuth($data['emailAuth']);
                    }
                    if(isset($data['emailHide'])){
                        $utilisateur->setEmailHide($data['emailHide']);
                    }
                    $em->persist($utilisateur);
                    $em->flush();
                    $session->getFlashBag()->add('success', 'Modifications enregistrées avec succès');
                }
                // MOT DE PASSE
                if($request->request->get('utilisateurmotdepassetype')){
                    $formMdp->bind($request);
                    $data = $request->request->get('utilisateurmotdepassetype');
                    $ancienMdp = sha1($data['ancienmdp']);
                    $newMdp = sha1($data['newmdp']);
                    $renewMdp = sha1($data['renewmdp']);
                    if($ancienMdp == $utilisateur->getPassword()){
                        if($newMdp == $renewMdp){
                            if(strlen($data['newmdp']) >= 5 AND strlen($data['newmdp'] <= 50)){
                                $utilisateur->setPassword($newMdp);
                                $em->persist($utilisateur);
                                $em->flush();
                                $session->getFlashBag()->add('success', 'Mot de passe modifié avec succès');
                            }else{
                                $session->getFlashBag()->add('error', 'Le mot de passe doit contenir entre 5 et 50 caractères !');
                            }
                        }else{
                            $session->getFlashBag()->add('error', 'Confirmation inégale au nouveau mot de passe !');
                        }
                    }else{
                        $session->getFlashBag()->add('error', 'Ancien mot de passe incorrect !');
                    }
                }
                // EMAIL
                if($request->request->get('utilisateuremailtype')){
                    $formEmail->bind($request);
                    $data = $request->request->get('utilisateuremailtype');
                    if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
                        if($em->getRepository('SiteUtilisateurBundle:Utilisateur')->findBy(array('email' => $data['email'])) == false){
                            $utilisateur->setEmail($data['email']);
                            $em->persist($utilisateur);
                            $em->flush();
                            $session->getFlashBag()->add('success', 'Email modifié avec succès');
                        }else{
                            $session->getFlashBag()->add('error', 'Email déjà utilisé !');
                        }
                    }else{
                        $session->getFlashBag()->add('error', 'Email invalide !');
                    }
                }
                // AVATAR
                if($request->request->get('utilisateuravatartype')){
                    $formAvatar->bind($request);
                    $data = $formAvatar->getData();
                    $avatar = $data['avatar'];
                    $extension = $avatar->getClientMimeType();
                    if($extension == 'image/jpeg' OR $extension == 'image/png'){
                        if($extension == 'image/jpeg'){
                            $extension = 'jpg';
                        }elseif($extension == 'image/png'){
                            $extension = 'png';
                        }
                        if($avatar->getClientSize() >= 15360 AND $avatar->getClientSize() <= 1048576){
                            $name = uniqid().'.'.$extension;
                            $avatar->move('avatar', $name);
                            $utilisateur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $session->get('id')));
                            $utilisateur->setAvatar($name);
                            $em->persist($utilisateur);
                            $em->flush();
                            $session->getFlashBag()->add('success', 'Avatar modifié avec succès');
                        }else{
                            $session->getFlashBag()->add('error', 'La taille de l\'image doit être entre 15Ko et 1MO !');
                            return $this->redirect($this->generateUrl('utilisateur_preferences'));
                        }
                    }else{
                        $session->getFlashBag()->add('error', 'L\'image doit être en format .jpg ou .png !');
                        return $this->redirect($this->generateUrl('utilisateur_preferences'));
                    }
                }
            }
            $utilisateur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $session->get('id')));
            return $this->render('SiteUtilisateurBundle::preferences.html.twig', array(
                    'utilisateur' => $utilisateur,
                    'formInfo' => $formInfo->createView(),
                    'formMdp' => $formMdp->createView(),
                    'formEmail' => $formEmail->createView(),
                    'formAvatar' => $formAvatar->createView(),
                ));
        }
        $session->set('referer', $this->generateUrl('utilisateur_preferences', array(), true));
        return $this->redirect($this->generateUrl('utilisateur_connexion'));
    }

    public function inscriptionAction()
    {
        $utilisateur = new Utilisateur();
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $utilisateur->setIpMembre($request->getClientIp());
        $form = $this->createForm(new UtilisateurInscriptionType, $utilisateur);
        if($request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $mdp = $utilisateur->getPassword();
                $utilisateur->setpassword(sha1($utilisateur->getpassword()));
                $em->persist($utilisateur);
                $em->flush();
                $session->getFlashBag()->add('success', 'Félicitation, veuillez vérifier votre adresse e-mail pour finaliser votre inscription.');
                $adresse = $this->generateUrl('utilisateur_confirmationInscription', array('salt' => $utilisateur->getSalt(), 'id' => $utilisateur->getId()), true);
                $mail = \Swift_Message::newInstance()
                    ->setSubject('Confirmation de votre inscription | league-of-bravery.com')
                    ->setFrom(array('webmaster@league-of-bravery.com' => 'league-of-bravery.com'))
                    ->setTo($utilisateur->getEmail())
                    ->setBody($this->renderView('SiteUtilisateurBundle:mail:confirmationInscription.html.twig', array('mdp' => $mdp, 'adresse' => $adresse, 'utilisateur' => $utilisateur)), 'text/html');
                $this->get('mailer')->send($mail);

                return $this->redirect($this->generateUrl('index'));
            }
        }
        return $this->render('SiteUtilisateurBundle::inscription.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    public function confirmationInscriptionAction($salt, Utilisateur $utilisateur)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        if($utilisateur AND $utilisateur->getSalt() == $salt){
            $utilisateur->setActive(true);
            $utilisateur->setSalt(md5(uniqid(null, true)));
            $em->persist($utilisateur);
            $em->flush();
            $session->getFlashBag()->add('success', 'Compte activé avec succès ! Vous pouvez dès à présent vous connecter !');
        }else{
            $session->getFlashBag()->add('error', 'Utilisateur inconnu.');
        }
        return $this->redirect($this->generateUrl('index'));
    }

    public function connexionAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        if(!$session->has('id')){
            $form = $this->createFormBuilder()
                ->add('pseudo', 'text', array('label' => 'Pseudo League of legends : '))
                ->add('password', 'password', array('label' => 'Mot de passe : '))
                ->getForm();
            if($request->getMethod() == 'POST'){
                $form->bind($request);
                $data = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('pseudo' => $data['pseudo'], 'password' => sha1($data['password'])));
                if($user){
                    if($user->getActive() == '1'){
                        if($user->getBanni() == '0'){
                            $user->setIpMembre($request->getClientIp());
                            $user->setDateConnexion(new \DateTime('now'));
                            $em->flush();
                            $session->set('id', $user->getId());
                            $session->set('rang', $user->getRang());
                            $session->set('pseudo', $data['pseudo']);
                            if($session->has('referer')){
                                $referer = $session->get('referer');
                                $session->remove('referer');
                                return $this->redirect($referer);
                            }else{
                                return $this->redirect($this->generateUrl('index'));
                            }
                        }else{
                            $session->getFlashBag()->add('error', 'Vous êtes banni !');
                        }
                    }else{
                        $session->getFlashBag()->add('error', 'Votre compte n\'est pas activé, veuillez vérifier votre boite mail !');
                    }
                }else{
                    $session->getFlashBag()->add('error', 'Mauvais pseudo et/ou mot de passe');
                }
            }
        }else{
            $session->getFlashBag()->add('error', 'vous êtes déjà connecté !');
            return$this->redirect($this->generateUrl('index'));
        }

        return $this->render('SiteUtilisateurBundle::connexion.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    public function deconnexionAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        if($session->has('pseudo')){
            $session->clear();
            $session->invalidate();
            $session->getFlashBag()->add('success', 'Deconnexion effectuée avec succès !');
            return $this->redirect($this->generateUrl('index'));
        }
        $session->getFlashBag()->add('error', 'Il faut se connecter pour pouvoir se déconnecter ! ;)');
        return $this->redirect($this->generateUrl('index'));
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
                ->from('SiteUtilisateurBundle:Utilisateur', 'u')
                ->where("u.pseudo LIKE :motcle OR u.email LIKE :motcle OR u.rang LIKE :motcle")
                ->orderBy('u.pseudo ', 'ASC')
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
            $utilisateur = new Utilisateur();
            $utilisateur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findBy(array('active' => true));
            foreach($utilisateur as $k => $v) {
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
            $total = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->createQueryBuilder('p')->getQuery()->getResult();
            $total_users = count($total);
            $user_per_page = $this->container->getParameter('max_users_on_page');
            $last_page = ceil($total_users / $user_per_page);
            $previous_page = $page > 1 ? $page - 1 : 1;
            $next_page = $page < $last_page ? $page + 1 : $last_page;
            $users = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->createQueryBuilder('p')->orderBy('p.ratio', 'DESC')->setFirstResult(($page * $user_per_page) - $user_per_page)->setMaxResults($this->container->getParameter('max_users_on_page'))->getQuery()->getResult();
        }
        return $this->render('SiteUtilisateurBundle::liste.html.twig', array(
                'users' => $users,
                'last_page' => $last_page,
                'previous_page' => $previous_page,
                'current_page' => $page,
                'next_page' => $next_page,
                'total_users' => $total_users,
                'user_per_page' => $user_per_page,
                'form' => $form->createView()
            ));
    }

    public function banAction(Utilisateur $utilisateur)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        if($session->get('rang') == 'Administrateur'){
            if($utilisateur->getBanni() == false){
                $utilisateur->setBanni(true);
                $em->flush();
            }else{
                $utilisateur->setBanni(false);
                $em->flush();
            }
        }
        return $this->redirect($this->generateUrl('utilisateur_profil', array('id' => $utilisateur->getId())));
    }

    public function mdpoublieAction($salt = null, $id = null)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        if(!$session->has('pseudo')){
            if($id){
                $session->set('id', $id);
            }
            if($salt){
                $session->set('salt', $salt);
                $form = $this->createForm(new UtilisateurChangeMdpType);
            }else{
                $form = $this->createForm(new UtilisateurMdpOublieType);
            }
            if($request->request->get('utilisateurchangemdptype')){
                $form->bind($request);
                $data = $request->request->get('utilisateurchangemdptype');
                $utilisateur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $session->get('id')));
                if($utilisateur->getSalt() == $session->get('salt')){
                    if($data['newmdp'] == $data['renewmdp']){
                        $utilisateur->setPassword(\sha1($data['newmdp']));
                        $utilisateur->setSalt(md5(uniqid(null, true)));
                        $em->persist($utilisateur);
                        $em->flush();
                        $session->getFlashBag()->add('success', 'Mot de passe modifié avec succès !');
                        return $this->redirect($this->generateUrl('utilisateur_connexion'));
                    }else{
                        $session->getFlashBag()->add('error', 'Erreur dans la confirmation du mot de passe !');
                    }
                }else{
                    $session->getFlashBag()->add('error', 'Utilisateur inconnu !');
                }
            }
            if($request->request->get('utilisateurmdpoublietype')){
                $form->bind($request);
                $data = $request->request->get('utilisateurmdpoublietype');
                if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
                    $utilisateur = new Utilisateur;
                    $utilisateur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('email' => $data['email']));
                    if($utilisateur){
                        $adresse = $this->generateUrl('utilisateur_mdpoublie', array('salt' => $utilisateur->getSalt(), 'id' => $utilisateur->getId()), true);
                        $mail = \Swift_Message::newInstance()
                            ->setSubject('league-of-bravery.com | Mot de passe oublié')
                            ->setFrom(array('contact@league-of-bravery.com' => 'league-of-bravery.com'))
                            ->setTo($utilisateur->getEmail())
                            ->setBody($this->renderView('SiteUtilisateurBundle:mail:mdpoublie.html.twig', array('utilisateur' => $utilisateur, 'adresse' => $adresse)), 'text/html');
                        $this->get('mailer')->send($mail);
                        $session->getFlashBag()->add('info', 'Un e-mail de confirmation à été envoyé à l\'adresse suivante : '.$data['email']);
                        return $this->redirect($this->generateUrl('index'));
                    }else{
                        $session->getFlashBag()->add('error', 'L\'email : '.$data['email'].' n\'existe pas');
                    }
                }else{
                    $session->getFlashBag()->add('error', 'L\'email : '.$data['email'].' n\'est pas valide');
                }
            }
        }else{
            $session->getFlashBag()->add('error', 'Vous êtes déjà connecté !');
            return $this->redirect($this->generateUrl('index'));
        }
        return $this->render('SiteUtilisateurBundle::mdpoublie.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    public function panelAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $utilisateur = $em->getRepository('SiteUtilisateurBundle:Utilisateur')->findOneBy(array('id' => $session->get('id')));
        $messagesNonLu = $em->getRepository('SiteUtilisateurBundle:Message')->findBy(array('receveur' => $utilisateur, 'supprimer' => false, 'etat' => false));
        return $this->render('SiteUtilisateurBundle:include:panel.html.twig', array(
                'messagesNonLu' => $messagesNonLu,
            ));
    }
    
    public function colorisationAction(Utilisateur $utilisateur)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $rang = $utilisateur->getRang();
        $pseudo = $utilisateur->getPseudo();
        if($rang == 'Administrateur'){
            $pseudoColo = '<font color="red">'.$pseudo.'</font>';
        }elseif($rang == 'Modérateur'){
            $pseudoColo = '<font color="blue">'.$pseudo.'</font>';
        }else{
            $pseudoColo = '<font color="black">'.$pseudo.'</font>';
        }
        $pseudoComplet = '<a href="'.$this->generateUrl('utilisateur_profil', array('id' => $utilisateur->getId()), false).'">'.$pseudoColo.'</a>';
        return new Response($pseudoComplet);
    }

}

?>
