<?php

namespace Site\UtilisateurBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UtilisateurAvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar', 'file', array('label' => 'Format : jpg/png Taille : >15Ko <1MO'));
    }

    public function getName()
    {
        return 'utilisateuravatartype';
    }
}

?>