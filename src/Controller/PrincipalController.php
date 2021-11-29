<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Form\ClienteType;
use App\Form\FiltroType;
use App\Form\PedidoEditarType;
use App\Form\PedidoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PrincipalController extends AbstractController
{
    /**
     * @Route("/principal", name="principal")
     */
    public function principal(): Response
    {
        return $this->render('principal/principal.html.twig');
    }

        /**
     * @Route("/imprimir", name="imprimir")
     */
    public function imprimir(): Response
    {
        $em=$this->getDoctrine()->getManager();
        
        $pedidos = $em->getRepository('App:Pedido')->reporte($this->getUser()->getId());
        return $this->render('principal/imprimir.html.twig',[
            'pedidos'=>$pedidos,
        ]);
    }

     /**
     * @Route("/crearpedido/", name="crearpedido")
     */
    public function crearpedido(Request $request): Response
    {//print_r($id);exit();
        $cliente= new Cliente();
        $form = $this->createForm(ClienteType::class,$cliente);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $cliente->setUser($this->getUser());
            
            $em->persist($cliente);
            $em->flush();
           
            $lugar=$em->getRepository('App:Cliente')->lugarByIdCliente($cliente->getId());
            ////flash mensaje cliente creado ahora cree su pedido
            return $this->render('cliente/clientemenu.html.twig',
            [
            'idcliente'=>$cliente->getId(),
            'nombrecliente'=>$cliente->getNombre(),
            'lugar'=>$lugar,
        
            ]);

        }

        return $this->render('cliente/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verpedido/", name="verpedido")
     */
    public function verpedido(Request $request): Response
    {
        
        $em = $this->getDoctrine()->getManager();
        $pedidos = $em->getRepository('App:Pedido')->findpedidosjoined($this->getUser()->getId());
        $form=$this->createForm(FiltroType::class);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //print_r('lsjdf');exit();
            
            $lugar= $form->get("lugar")->getData();
            $cliente= $form->get("cliente")->getData();
  
            $pedidosfiltrados= $em->getRepository('App:Pedido')->findbylugarcliente($lugar,$cliente,$this->getUser()->getId());
            return $this->render('cliente/verpedido.html.twig', [
                'pedidos' => $pedidosfiltrados,
                'form' => $form->createView(),
            ]);

        }



        return $this->render('cliente/verpedido.html.twig', [
            'pedidos' => $pedidos,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editarpedido/{idpedido}", name="editarpedido")
     */
    public function editarpedido($idpedido,Request $request){

        $em =$this->getDoctrine()->getManager();
        $pedido = $em->getRepository('App:Pedido')->find($idpedido);
        $form=$this->createForm(PedidoEditarType::class,$pedido);

        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){

            $em->persist($pedido);
            $em->flush();
            $formfiltro = $this->createForm(FiltroType::class);
            $pedidos = $em->getRepository('App:Pedido')->findpedidosjoined($this->getUser()->getId());
            return $this->render('cliente/verpedido.html.twig',[
                'form'=>$formfiltro->createView(),
                'pedidos'=>$pedidos,
                
                ]);

        }



        return $this->render('cliente/editarpedido.html.twig', [            
            'nombrecliente'=>$em->getRepository('App:Cliente')->findclientebyidpedido($idpedido),
            'form' => $form->createView(),
        ]);
    }
   
}
