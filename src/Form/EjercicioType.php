<?php

namespace App\Form;

use App\Entity\Ejercicio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class EjercicioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('descripcion', TextareaType::class)
            //->add('fecha_creacion')
            //->add('revisado')
            //->add('fecha_revision')
            //->add('aceptado')
            ///->add('disponible')
            ->add('documento', CKEditorType::class)
            ->add('imagen', FileType::class)
            ->add('niveles_disponibles', IntegerType::class, ['label' => 'Número de niveles disponibles'])
          //  ->add('autor')
            ->add('enviar', SubmitType::class, ['label' => 'Añadir']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ejercicio::class,
        ]);
    }
}
