<?php

namespace Site\UtilisateurBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site\UtilisateurBundle\Entity\Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity
 */
class Message
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
     * @var string $titre
     *
     * @ORM\Column(name="titre", type="string", length=100)
     */
    private $titre;

    /**
     * @var string $contenu
     *
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

    /**
     * @var \DateTime $dateCreation
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime $dateOuverture
     *
     * @ORM\Column(name="dateOuverture", type="datetime", nullable=true)
     */
    private $dateOuverture;

    /**
     * @var boolean $etat
     *
     * @ORM\Column(name="etat", type="boolean")
     */
    private $etat;

    /**
     * @var boolean $supprimer
     *
     * @ORM\Column(name="supprimer", type="boolean")
     */
    private $supprimer;
    
    /**
     * @ORM\ManyToOne(targetEntity="Site\UtilisateurBundle\Entity\Utilisateur")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;
    
    /**
     * @ORM\ManyToOne(targetEntity="Site\UtilisateurBundle\Entity\Utilisateur")
     * @ORM\JoinColumn(nullable=false)
     */
    private $receveur;
    
    public function __construct()
    {
        $this->dateCreation = new \DateTime('now');
        $this->etat = false;
        $this->supprimer = false;
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
     * Set titre
     *
     * @param string $titre
     * @return Message
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    
        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return Message
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    
        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Message
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
     * Set dateOuverture
     *
     * @param \DateTime $dateOuverture
     * @return Message
     */
    public function setDateOuverture($dateOuverture)
    {
        $this->dateOuverture = $dateOuverture;
    
        return $this;
    }

    /**
     * Get dateOuverture
     *
     * @return \DateTime 
     */
    public function getDateOuverture()
    {
        return $this->dateOuverture;
    }

    /**
     * Set etat
     *
     * @param boolean $etat
     * @return Message
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    
        return $this;
    }

    /**
     * Get etat
     *
     * @return boolean 
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set supprimer
     *
     * @param boolean $supprimer
     * @return Message
     */
    public function setSupprimer($supprimer)
    {
        $this->supprimer = $supprimer;
    
        return $this;
    }

    /**
     * Get supprimer
     *
     * @return boolean 
     */
    public function getSupprimer()
    {
        return $this->supprimer;
    }

    /**
     * Set auteur
     *
     * @param Site\UtilisateurBundle\Entity\Utilisateur $auteur
     * @return Message
     */
    public function setAuteur(\Site\UtilisateurBundle\Entity\Utilisateur $auteur)
    {
        $this->auteur = $auteur;
    
        return $this;
    }

    /**
     * Get auteur
     *
     * @return Site\UtilisateurBundle\Entity\Utilisateur 
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set receveur
     *
     * @param Site\UtilisateurBundle\Entity\Utilisateur $receveur
     * @return Message
     */
    public function setReceveur(\Site\UtilisateurBundle\Entity\Utilisateur $receveur)
    {
        $this->receveur = $receveur;
    
        return $this;
    }

    /**
     * Get receveur
     *
     * @return Site\UtilisateurBundle\Entity\Utilisateur 
     */
    public function getReceveur()
    {
        return $this->receveur;
    }
}