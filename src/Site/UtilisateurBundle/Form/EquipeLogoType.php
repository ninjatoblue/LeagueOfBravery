<?php

namespace Site\UtilisateurBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EquipeLogoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('logo', 'file', array('label' => 'Format : jpg/png Taille : >15Ko <1MO'));
    }

    public function getName()
    {
        return 'equipelogotype';
    }
}

?>