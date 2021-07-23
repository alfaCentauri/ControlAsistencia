<?php

namespace App\Form;

use App\Entity\Empleado;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmpleadoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cedula', NumberType::class, array('label'=> 'Cédula: ',
                'attr' => array('class' => 'form-control',
                    'placeholder' => '0000000',
                    'tooltip' => 'Escribe la cédula del usuario',
                    'required'   => true)))
            ->add('nombre', TextType::class, array('label'=> 'Nombre: ',
                'attr' => array('class' => 'form-control',
                    'placeholder' => 'Indique su nombre',
                    'tooltip' => 'Escribe el nombre',
                    'required'   => true)))
            ->add('apellido', TextType::class, array('label'=> 'Apellido: ',
                'attr' => array('class' => 'form-control',
                    'placeholder' => 'Indique su apellido',
                    'tooltip' => 'Escribe el apellido',
                    'required'   => true)))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Empleado::class,
        ]);
    }
}
