<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelfieType extends AbstractType
{
    public $question;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('photo', FileType::class, [
                'label' => 'Selfie',
                'attr' => [
                    'accept' => "image/*",
                    'class' => "inputfile"
                ]
            ])
            ->add('response', TextType::class, array(
                'label' => $this->question,
                'attr' => [
                    'class' => "form-control"
                ]
            ))
            ->add('send', SubmitType::class, array(
                'label' => 'Envoyer',
                'attr' => [
                    'class' => "btn btn-primary btn-lg btn-block"
                ]
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Post'
        ));
    }
}
