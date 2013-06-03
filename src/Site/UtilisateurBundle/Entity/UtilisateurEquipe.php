<?php

namespace Site\UtilisateurBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UtilisateurEquipe
 *
 * @ORM\Table(name="utilisateur_equipe")
 * @ORM\Entity
 */
class UtilisateurEquipe
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
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime")
     */
    private $datetime;

    /**
     * @var string
     *
     * @ORM\Column(name="rang", type="string", length=255)
     */
    private $rang;
    
    /**
     * @ORM\ManyToOne(targetEntity="Site\UtilisateurBundle\Entity\Utilisateur")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;
    
    /**
     * @ORM\ManyToOne(targetEntity="Site\UtilisateurBundle\Entity\Equipe")
     * @ORM\JoinColumn(nullable=false)
     */
    private $equipe;
    
    public function __construct()
    {
        $this->datetime = new \DateTime('now');
        $this->rang = 'Joueur';
        $this->poste = 'Non précisé';
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
     * Set datetime
     *
     * @param \DateTime $datetime
     * @return UtilisateurEquipe
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    
        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime 
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set rang
     *
     * @param string $rang
     * @return UtilisateurEquipe
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
     * Set utilisateur
     *
     * @param \Site\UtilisateurBundle\Entity\Utilisateur $utilisateur
     * @return UtilisateurEquipe
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

    /**
     * Set equipe
     *
     * @param \Site\UtilisateurBundle\Entity\Equipe $equipe
     * @return UtilisateurEquipe
     */
    public function setEquipe(\Site\UtilisateurBundle\Entity\Equipe $equipe)
    {
        $this->equipe = $equipe;
    
        return $this;
    }

    /**
     * Get equipe
     *
     * @return \Site\UtilisateurBundle\Entity\Equipe 
     */
    public function getEquipe()
    {
        return $this->equipe;
    }
}