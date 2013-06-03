<?php

namespace Site\UtilisateurBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Equipe
 *
 * @ORM\Table(name="equipe")
 * @ORM\Entity
 */
class Equipe
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=5)
     */
    private $tag;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var string $logo
     * 
     * @ORM\Column(name="logo", type="string", length=255)
     */
    private $logo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=255,  nullable=true)
     */
    private $site;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=60)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var boolean
     *
     * @ORM\Column(name="banni", type="boolean")
     */
    private $banni;
    
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
    
    /**
     * @ORM\ManyToOne(targetEntity="Site\UtilisateurBundle\Entity\Utilisateur")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chef;
    
    public function __construct()
    {
        $this->banni = false;
        $this->logo = 'defaut.png';
        $this->dateCreation = new \DateTime('now');
        $this->token = md5(uniqid(null, true));
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
     * Set nom
     *
     * @param string $nom
     * @return Equipe
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    
        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return Equipe
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    
        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Equipe
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
    
        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Equipe
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
     * Set token
     *
     * @param string $token
     * @return Equipe
     */
    public function setToken($token)
    {
        $this->token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set banni
     *
     * @param boolean $banni
     * @return Equipe
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
     * Set chef
     *
     * @param \Site\UtilisateurBundle\Entity\Utilisateur $chef
     * @return Equipe
     */
    public function setChef(\Site\UtilisateurBundle\Entity\Utilisateur $chef)
    {
        $this->chef = $chef;
    
        return $this;
    }

    /**
     * Get chef
     *
     * @return \Site\UtilisateurBundle\Entity\Utilisateur 
     */
    public function getChef()
    {
        return $this->chef;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Equipe
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set site
     *
     * @param string $site
     * @return Equipe
     */
    public function setSite($site)
    {
        $this->site = $site;
    
        return $this;
    }

    /**
     * Get site
     *
     * @return string 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return Equipe
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    
        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set nbVictoire
     *
     * @param integer $nbVictoire
     * @return Equipe
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
     * @return Equipe
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
     * @return Equipe
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
     * @return Equipe
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