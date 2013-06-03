<?php

namespace Site\UtilisateurBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\Collection;

class EquipeInformationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text', array('label' => 'Nom de la team :'))
            ->add('tag', 'text', array('label' => 'Tag de la team :'))
            ->add('description', 'textarea', array('label' => 'Description :', 'required' => false))
            ->add('site', 'text', array('label' => 'Site web :', 'required' => false)
        );
    }

    public function getName()
    {
        return 'equipeinformationtype';
    }
}

?>