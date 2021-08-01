<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UsuarioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('label'=> 'Login: ',
                'attr' => array('class' => 'form-control',
                    'placeholder' => 'Indique un email',
                    'tooltip' => 'Escriba un email de usuario',
                    'required'   => true)))
//            ->add('roles', ChoiceType::class, array(
//                'choices'  => array('ADMINISTRADOR' => 'ADMINISTRADOR',
//                    'JEFE' => 'JEFE',
//                    'OPERADOR' => 'OPERADOR'),
//                'choices_as_values' => false,
//                'attr' => array('class' => 'form-control',
//                    'required' => true,
//                    'placeholder'=>'Indique una opción'),
//            ))
            ->add('password', PasswordType::class, array('label'=> 'Clave: ',
                'attr' => array('class' => 'form-control',
                    'placeholder' => 'Indique una clave',
                    'tooltip' => 'Escriba una clave',
                    'required'   => true)))
            ->add('cedula', NumberType::class, array('label'=> 'Cédula: ',
                'attr' => array('class' => 'form-control',
                    'placeholder' => '0000000',
                    'tooltip' => 'Escribe la cédula del usuario',
                    'required'   => true)))
            ->add('nombre', TextType::class, array('label'=> 'Nombre: ',
                'attr' => array('class' => 'form-control',
                    'placeholder' => 'Indique su nombre',
                    'tooltip' => 'Escribe el nombre',
                    'required' => true,
                    'maxlength' => 100)))
            ->add('apellido', TextType::class, array('label'=> 'Apellido: ',
                'attr' => array('class' => 'form-control',
                    'placeholder' => 'Indique su apellido',
                    'tooltip' => 'Escribe el apellido',
                    'required' => true,
                    'maxlength' => 100)))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
