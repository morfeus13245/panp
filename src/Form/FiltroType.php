<?php

namespace App\Form;

use App\Entity\Lugar;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lugar',EntityType::class,array('class'=>Lugar::class,'choice_label'=>'nombre','choice_value'=>'id','required' => false,
            'placeholder' => 'Todos','empty_data' => null,))
            ->add('cliente',TextType::class,array('label'=>'Cliente','empty_data'=>null,'required'=> false))
            ->add('submit',SubmitType::class,array('label'=>'Buscar clientes'))
        ;
    }

    
}
