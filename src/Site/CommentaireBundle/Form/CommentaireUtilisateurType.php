<?php

namespace Site\CommentaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentaireUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contenu', 'textarea', array('label' => 'Votre commentaire (1000 caractères max) :'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Site\CommentaireBundle\Entity\CommentaireUtilisateur'
        ));
    }

    public function getName()
    {
        return 'commentairetype';
    }
}
