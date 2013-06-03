<?php

namespace Site\UtilisateurBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\Collection;

class UtilisateurMotDePasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ancienmdp', 'password', array('label' => 'Ancien mot de passe :'))
            ->add('newmdp', 'password', array('label' => 'Nouveau mot de passe :'))
            ->add('renewmdp', 'password', array('label' => 'Confirmation :'));
    }

    public function getName()
    {
        return 'equipemotdepassetype';
    }
}

?>