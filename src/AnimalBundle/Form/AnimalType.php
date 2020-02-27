<?php

namespace AnimalBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom')
            ->add('description')
            ->add('file',FileType::class, array('label' => 'Image(JPG)','data_class'=>null))
            ->add('zone')
            ->add('saison')
            ->add('categorie',EntityType::class,['class' =>'AnimalBundle\Entity\CategorieAnimal',
                'choice_label' => 'nom','multiple' => false, ]);;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AnimalBundle\Entity\Animal'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'animalbundle_animal';
    }


}
