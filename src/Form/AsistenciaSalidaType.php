<?php

namespace App\Form;

use App\Entity\Asistencia;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AsistenciaSalidaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('horaSalida', TimeType::class, array(
                'label'=> 'Hora de salida:',
                'attr' => array('class' => 'form-control',
                    'placeholder' => 'Seleccione una hora',
                    'tooltip' => 'Seleccione una hora',
                    'required'   => true )))
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
        return 'agregar_salida_asistencia';
    }
}
