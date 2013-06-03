<?php

namespace Site\UtilisateurBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\Collection;

class UtilisateurInformationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('poste', 'choice', array('label' =>'Poste favori : ', 'choices' => array('Aucun' => 'Aucun', 'Top' => 'Top', 'Jungle' => 'Jungle', 'Mid' => 'Mid', 'AD' => 'AD', 'Support' => 'Support')))
            ->add('emailAuth', 'checkbox', array('label' => 'Me prévenir par e-mail quand je reçois un message privé', 'required' => false))
            ->add('emailHide', 'checkbox', array('label' => 'Cacher mon email', 'required' => false));
    }

    public function getName()
    {
        return 'utilisateurinformationtype';
    }
}

?>