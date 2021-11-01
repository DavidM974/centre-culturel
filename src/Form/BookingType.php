<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateTimeType::class, [
                'attr'=> ['class' => 'form-control js-datepicker'],
                'label'=> 'Date de debut',
                'required'=> true,
                'widget' => 'single_text',
                'html5' => false,
                'format'=>'dd/MM/yyyy H:mm'
            ])
            ->add('endDate', DateTimeType::class, [
                'attr'=> ['class' => 'form-control js-datepicker2'],
                'label'=> 'Date de fin',
                'required'=> true,
                'widget' => 'single_text',
                'html5' => false,
                'format'=>'dd/MM/yyyy H:mm'
            ])
            ->add('comment')
            ->add('User')
            ->add('Computer')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
