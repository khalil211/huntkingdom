<?php

namespace GestionProduitBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, array('label' => 'Image(JPG)', 'data_class'=>null))
            ->add('nomProd')
            ->add('prixProd')
            ->add('descriptionProd')
            ->add('quantiteProd')
            ->add('categorie',EntityType::class,['class' =>'GestionProduitBundle\Entity\CategorieProduit',
                'choice_label' =>'nomCat',
                'multiple' => false,

            ]);;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GestionProduitBundle\Entity\Produit'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gestionproduitbundle_produit';
    }


}
