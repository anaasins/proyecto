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
            ->add('nombre', TextType::class, ['label' => 'Título del ejercicio'])
            ->add('descripcion', TextareaType::class, [ 'label' => 'Descripción',
                                                        'help' => 'Una breve descripcion donde explicas en que consiste el juego, como ayuda al entrenamiento de la vista, etc.'
                                                      ])
            ->add('instrucciones', TextareaType::class, ['help' => 'Explica como empezar el juego, como se ganan puntos, si se puede subir de nivel, etc.'])
            //->add('fecha_creacion')
            //->add('revisado')
            //->add('fecha_revision')
            //->add('aceptado')
            ///->add('disponible')
            ->add('documento', FileType::class, [ 'label' => 'Documento principal',
                                                  'multiple' => false,
                                                  'help_html' => true,
                                                  'help' => 'Aquí debes subir la plantilla (index.html.twig) que te has descargado actualizada con tu ejercicio implementado.
                                                              <br>
                                                             Asegurate de haber seguido las instrucciones en ella durante la implementación del ejercicio.
                                                            '
                                                ])
            ->add('imagen', FileType::class, ['label' => 'Imagen de portada',
                                              'multiple' => false
                                             ])
             ->add('documentosExtra', FileType::class, [ 'label' => 'Archivos extra',
                                                         'multiple' => true,
                                                         'required' => false,
                                                         'help_html' => true,
                                                         'help' => 'Aquí puedes subir otros archivos o imagenes necesarios para le funcionamiento del ejercicio.
                                                                     <br>
                                                                    Comprueba antes que los enlaces (src) en el código siguen las indicaciones de la plantilla descargada.
                                                                   '
                                                       ])
            ->add('niveles_disponibles', IntegerType::class, ['label' => 'Número de niveles disponibles',
                                                              'required' => false,
                                                              'help' => 'Si la dificultad del ejercicio se rige por niveles, indica aquí de cuantos dispone.'
                                                             ])
          //  ->add('autor')
            ->add('enviar', SubmitType::class, ['label' => 'Añadir ejercicio']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ejercicio::class,
        ]);
    }
}
