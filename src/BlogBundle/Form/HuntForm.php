<?php

namespace BlogBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class HuntForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('outlook',ChoiceType::class, array(
                'choices' => array(
                    'Sunny' => 1,
                    'Overcast' => 2,
                    'Rainy' => 3,
                )))
            ->add('temperature',ChoiceType::class, array(
                'choices' => array(
                    'Hot' => 1,
                    'Cool' => 2,
                    'Mild' => 3,
                )))
            ->add('humidity',ChoiceType::class, array(
                'choices' => array(
                    'High' => 1,
                    'Normal' => 0,
                )))
            ->add('windy',ChoiceType::class, array(
                'choices' => array(
                    'True' => 1,
                    'False' => 0,
                )));
    }

    public function getBlockPrefix()
    {
        return 'hunt_form';
    }
}