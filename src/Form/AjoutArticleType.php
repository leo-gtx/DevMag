<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\MotsCles;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AjoutArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class)
            ->add('overview', TextType::class)
            ->add('preview', TextType::class,[
                'required'=> false
            ])
            ->add('featured_image', FileType::class, [
                'label' => 'Uploads image',
                'data_class'=> null,
                'required' => true
            ])
            ->add('contenu', CKEditorType::class)
            ->add('vip', CheckboxType::class,[
                'required' => true
            ])
            ->add('isFormation', CheckboxType::class,[
                'required' => true
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('mots_cles', EntityType::class, [
                'class' => MotsCles::class,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('prev', EntityType::class, [
                'class' => Articles::class,
            ])
            ->add('next', EntityType::class, [
                'class' => Articles::class
            ])
            ->add('published', CheckboxType::class,[
                'required' => false
            ])
            ->add('Publier', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
