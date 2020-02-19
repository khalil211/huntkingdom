<?php

namespace BlogBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class CommentReportForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('reason',ChoiceType::class, array(
               'choices' => array(
                   'Inappropriate Content' => 'Inappropriate Content',
                   'Spam' => 'Spam',
                   'Racism' => 'Racism',
                   'Nudity' => 'Nudity',
                   'Other' => 'Other',
               )))
             ->add('message', TextareaType::class, [
        'constraints' => [
            new Assert\Length(['min' => 10]),
            new Assert\NotBlank(),
        ],
    ]);
    }

    public function getBlockPrefix()
    {
        return 'report_comment_form';
    }
}