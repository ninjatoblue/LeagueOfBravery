<?php

namespace Site\CommentaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site\CommentaireBundle\Entity\CommentaireUtilisateur
 *
 * @ORM\Table(name="commentaire_utilisateur")
 * @ORM\Entity
 */
class CommentaireUtilisateur
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
     * @var string
     * 
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var boolean
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
    private $utilisateur;
    
    public function __construct()
    {
        $this->date = new \DateTime('now');
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
     * Set contenu
     *
     * @param string $contenu
     * @return CommentaireUtilisateur
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
     * Set date
     *
     * @param \DateTime $date
     * @return CommentaireUtilisateur
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set supprimer
     *
     * @param boolean $supprimer
     * @return CommentaireUtilisateur
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
     * @param \Site\UtilisateurBundle\Entity\Utilisateur $auteur
     * @return CommentaireUtilisateur
     */
    public function setAuteur(\Site\UtilisateurBundle\Entity\Utilisateur $auteur)
    {
        $this->auteur = $auteur;
    
        return $this;
    }

    /**
     * Get auteur
     *
     * @return \Site\UtilisateurBundle\Entity\Utilisateur 
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set utilisateur
     *
     * @param \Site\UtilisateurBundle\Entity\Utilisateur $utilisateur
     * @return CommentaireUtilisateur
     */
    public function setUtilisateur(\Site\UtilisateurBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateur = $utilisateur;
    
        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \Site\UtilisateurBundle\Entity\Utilisateur 
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }
}