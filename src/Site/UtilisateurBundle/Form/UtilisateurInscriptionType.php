<?php

namespace Site\UtilisateurBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UtilisateurInscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', 'text', array('label' => 'Pseudo League of Legends : '))
            ->add('password', 'password', array('label' => 'Mot de passe : '))
            ->add('email', 'email', array('label' => 'Email : '))
            ->add('sexe', 'choice', array('label' =>'Sexe : ', 'choices' => array('M' => 'Joueur', 'F' => 'Joueuse')))
            ->add('poste', 'choice', array('label' =>'Poste favori : ', 'choices' => array('Aucun' => 'Aucun', 'Top' => 'Top', 'Jungle' => 'Jungle', 'Mid' => 'Mid', 'AD' => 'AD', 'Support' => 'Support')))
            ->add('emailHide', 'checkbox', array('label' => 'Cacher mon email', 'required' => false))
            ->add('emailAuth', 'checkbox', array('label' => 'Me prévenir par e-mail lors de la récéption de messages', 'required' => false, 'value' => 0))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Site\UtilisateurBundle\Entity\Utilisateur'
        ));
    }

    public function getName()
    {
        return 'utilisateurinscriptiontype';
    }
}

?>