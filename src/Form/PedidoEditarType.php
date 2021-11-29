<?php

namespace App\Form;

use App\Entity\Pedido;
use App\Entity\Producto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PedidoEditarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('fecha') se autogenera
            ->add('cantidad',NumberType::class,['label'=>'cantidad'])
            ->add('producto',EntityType::class,['class'=>Producto::class,'choice_label'=>'nombre','choice_value'=>'id'])
            ->add('fiado', CheckboxType::class, ['label'=>'Fiado?','required'=>false])
            ->add('submit', SubmitType::class, array('label'=>'Editar','attr'=>['onclick'=>'funcion();']))
            //->add('cliente') se autogenera 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pedido::class,
        ]);
    }
}
