<?php

namespace Site\UtilisateurBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Site\UtilisateurBundle\Entity\Utilisateur
 *
 * @ORM\Table(name="utilisateur")
 * @UniqueEntity(fields="pseudo", message="Ce pseudo est déjà utilisé !")
 * @UniqueEntity(fields="email", message="Cet email est déjà utilisé !")
 * @ORM\Entity
 */
class Utilisateur
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string $pseudo
     *
     * @ORM\Column(name="pseudo", type="string", length=15)
     * @Assert\NotBlank(message = "Le pseudo ne peux pas être vide")
     * @Assert\MinLength(limit = 3, message = "Le pseudo doit être supérieur à {{ limit }} caractères")
     * @Assert\MaxLength(limit = 15, message = "Le pseudo doit être inférieur à {{ limit }} caractères")
     */
    private $pseudo;
    
    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=50)
     * @Assert\NotBlank(message = "Le mot de passe ne peux pas être vide")
     * @Assert\MinLength(limit = 5, message = "Le mot de passe doit être supérieur à {{ limit }} caractères")
     * @Assert\MaxLength(limit = 50, message = "Le mot de passe doit être inférieur à {{ limit }} caractères")
     */
    private $password;
    
    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=50)
     * @Assert\NotBlank(message = "L'email ne peux pas être vide")
     */
    private $email;
    
    /**
     * @var string $sexe
     * 
     * @ORM\Column(name="sexe", type="string", length=255)
     */
    private $sexe;
    
     /**
     * @var string $poste
     *
     * @ORM\Column(name="poste", type="string", length=30)
     */
    private $poste;

    /**
     * @var string $rang
     *
     * @ORM\Column(name="rang", type="string", length=30)
     */
    private $rang;

    /**
     * @var string $avatar
     * 
     * @ORM\Column(name="avatar", type="string", length=255)
     */
    private $avatar;

    /**
     * @var string $dateInscription
     *
     * @ORM\Column(name="dateInscription", type="datetime")
     */
    private $dateInscription;

    /**
     * @var string $dateConnexion
     *
     * @ORM\Column(name="dateConnexion", type="datetime", nullable=true)
     */
    private $dateConnexion;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=50)
     */
    private $salt;

    /**
     * @var integer $emailAuth
     *
     * @ORM\Column(name="emailAuth", type="boolean")
     */
    private $emailAuth;

    /**
     * @var integer $emailHide
     *
     * @ORM\Column(name="emailHide", type="boolean")
     */
    private $emailHide;

    /**
     * @var string $ipMembre
     *
     * @ORM\Column(name="ipMembre", type="string", length=20)
     */
    private $ipMembre;

    /**
     * @var integer $banni
     *
     * @ORM\Column(name="banni", type="boolean")
     */
    private $banni;
    
    /**
     * @var integer $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nbVictoire", type="integer")
     */
    private $nbVictoire;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nbDefaite", type="integer")
     */
    private $nbDefaite;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nbNul", type="integer")
     */
    private $nbNul;
    
    /**
     * @var float
     *
     * @ORM\Column(name="ratio", type="float")
     */
    private $ratio;
    
    public function __construct()
    {
        $this->active = false;
        $this->banni = false;
        $this->dateInscription = new \DateTime('now');
        $this->avatar = 'defaut.png';
        $this->rang = 'Membre';
        $this->salt = md5(uniqid(null, true));
        $this->emailAuth = true;
        $this->emailHide = false;
        $this->nbDefaite = 0;
        $this->nbNul = 0;
        $this->nbVictoire = 0;
        $this->ratio = 0;
    }
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pseudo
     *
     * @param string $pseudo
     * @return Utilisateur
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
    
        return $this;
    }

    /**
     * Get pseudo
     *
     * @return string 
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Utilisateur
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Utilisateur
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set rang
     *
     * @param string $rang
     * @return Utilisateur
     */
    public function setRang($rang)
    {
        $this->rang = $rang;
    
        return $this;
    }

    /**
     * Get rang
     *
     * @return string 
     */
    public function getRang()
    {
        return $this->rang;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return Utilisateur
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    
        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set dateInscription
     *
     * @param \DateTime $dateInscription
     * @return Utilisateur
     */
    public function setDateInscription($dateInscription)
    {
        $this->dateInscription = $dateInscription;
    
        return $this;
    }

    /**
     * Get dateInscription
     *
     * @return \DateTime 
     */
    public function getDateInscription()
    {
        return $this->dateInscription;
    }

    /**
     * Set dateConnexion
     *
     * @param \DateTime $dateConnexion
     * @return Utilisateur
     */
    public function setDateConnexion($dateConnexion)
    {
        $this->dateConnexion = $dateConnexion;
    
        return $this;
    }

    /**
     * Get dateConnexion
     *
     * @return \DateTime 
     */
    public function getDateConnexion()
    {
        return $this->dateConnexion;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Utilisateur
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set emailAuth
     *
     * @param boolean $emailAuth
     * @return Utilisateur
     */
    public function setEmailAuth($emailAuth)
    {
        $this->emailAuth = $emailAuth;
    
        return $this;
    }

    /**
     * Get emailAuth
     *
     * @return boolean 
     */
    public function getEmailAuth()
    {
        return $this->emailAuth;
    }

    /**
     * Set emailHide
     *
     * @param boolean $emailHide
     * @return Utilisateur
     */
    public function setEmailHide($emailHide)
    {
        $this->emailHide = $emailHide;
    
        return $this;
    }

    /**
     * Get emailHide
     *
     * @return boolean 
     */
    public function getEmailHide()
    {
        return $this->emailHide;
    }

    /**
     * Set ipMembre
     *
     * @param string $ipMembre
     * @return Utilisateur
     */
    public function setIpMembre($ipMembre)
    {
        $this->ipMembre = $ipMembre;
    
        return $this;
    }

    /**
     * Get ipMembre
     *
     * @return string 
     */
    public function getIpMembre()
    {
        return $this->ipMembre;
    }

    /**
     * Set banni
     *
     * @param boolean $banni
     * @return Utilisateur
     */
    public function setBanni($banni)
    {
        $this->banni = $banni;
    
        return $this;
    }

    /**
     * Get banni
     *
     * @return boolean 
     */
    public function getBanni()
    {
        return $this->banni;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Utilisateur
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set sexe
     *
     * @param string $sexe
     * @return Utilisateur
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;
    
        return $this;
    }

    /**
     * Get sexe
     *
     * @return string 
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set poste
     *
     * @param string $poste
     * @return Utilisateur
     */
    public function setPoste($poste)
    {
        $this->poste = $poste;
    
        return $this;
    }

    /**
     * Get poste
     *
     * @return string 
     */
    public function getPoste()
    {
        return $this->poste;
    }

    /**
     * Set nbVictoire
     *
     * @param integer $nbVictoire
     * @return Utilisateur
     */
    public function setNbVictoire($nbVictoire)
    {
        $this->nbVictoire = $nbVictoire;
    
        return $this;
    }

    /**
     * Get nbVictoire
     *
     * @return integer 
     */
    public function getNbVictoire()
    {
        return $this->nbVictoire;
    }

    /**
     * Set nbDefaite
     *
     * @param integer $nbDefaite
     * @return Utilisateur
     */
    public function setNbDefaite($nbDefaite)
    {
        $this->nbDefaite = $nbDefaite;
    
        return $this;
    }

    /**
     * Get nbDefaite
     *
     * @return integer 
     */
    public function getNbDefaite()
    {
        return $this->nbDefaite;
    }

    /**
     * Set nbNul
     *
     * @param integer $nbNul
     * @return Utilisateur
     */
    public function setNbNul($nbNul)
    {
        $this->nbNul = $nbNul;
    
        return $this;
    }

    /**
     * Get nbNul
     *
     * @return integer 
     */
    public function getNbNul()
    {
        return $this->nbNul;
    }

    /**
     * Set ratio
     *
     * @param float $ratio
     * @return Utilisateur
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;
    
        return $this;
    }

    /**
     * Get ratio
     *
     * @return float 
     */
    public function getRatio()
    {
        return $this->ratio;
    }
}