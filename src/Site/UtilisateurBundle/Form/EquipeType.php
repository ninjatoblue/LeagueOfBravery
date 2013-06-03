<?php

namespace Site\UtilisateurBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text', array('label' => 'Nom de la team* :'))
            ->add('tag', 'text', array('label' => 'Tag de la team* :'))
            ->add('password', 'password', array('label' => 'Mot de passe* :'))
            ->add('description', 'textarea', array('label' => 'Description :', 'required' => false))
            ->add('site', 'text', array('label' => 'Site web :', 'required' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Site\UtilisateurBundle\Entity\Equipe'
        ));
    }

    public function getName()
    {
        return 'utilisateurequipetype';
    }
}
