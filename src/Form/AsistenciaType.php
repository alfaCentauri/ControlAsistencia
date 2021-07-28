<?php

namespace App\Form;

use App\Entity\Asistencia;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AsistenciaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', DateType::class, array(
                'mapped' => false,
                'empty_data' => array('year' => 'Año', 'month' => 'Mes', 'day' => 'Día'),
                'years'=> range(2021,2025),
                'attr' => array('class' => 'form-control',
                    'placeholder' => 'Indique una fecha',
                    'tooltip' => 'Escriba una fecha',
                    'readonly'   => true)))
            ->add('horaEntrada', TimeType::class, array('label' => 'Hora de Entrada: ',
                'attr' => array(
                'placeholder' => 'Seleccione una hora',
                'tooltip' => 'Seleccione una hora',
                'required'   => true )))
            ->add('horaSalida', TimeType::class, array(
                'label'=> 'Hora de salida:',
                'attr' => array('class' => 'form-control',
                    'placeholder' => 'Seleccione una hora',
                    'tooltip' => 'Seleccione una hora',
                    'required'   => true )))
            //Muestra todos los empleados
            ->add('empleadoId', EntityType::class, array(
                'mapped' => false,
                'class' => 'App:Empleado',
                'choice_value' => function ($empleado){
                    return ( $empleado ? $empleado->getId() : 0 );
                },
                'choice_label' => function ($empleado){
                    return strtoupper( $empleado->getNombre()." ".$empleado->getApellido() );
                },
                'placeholder' => 'Seleccione un empleado',
                'required' => true,
                'attr' => ['class' => 'form-control',
                    'readonly' => true],
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Asistencia::class,
        ]);
    }

    /**
     * Método para indicar el nombre del formulario.
     * @return String Nombre del formulario.
     */
    public function getName(){
        return 'modificar_asistencia';
    }
}
