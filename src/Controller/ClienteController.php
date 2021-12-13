<?php

namespace App\Controller;

use App\Entity\Pedido;
use App\Form\FiltroType;
use App\Form\PedidoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Cliente;
use App\Form\ClienteEditaType;
class ClienteController extends AbstractController
{
    /**
     * @Route("/areacliente/{idcliente}/{nombrecliente}", name="areacliente")
     */
    public function areacliente($idcliente,$nombrecliente)
    {//print_r($idcliente);exit();
        $em=$this->getDoctrine()->getManager();
        $lugar = $em->getRepository('App:Cliente')->lugarByIdCliente($idcliente);
        //print_r($lugar);exit();
        return $this->render('cliente/clientemenu.html.twig', [
            'idcliente' => $idcliente,
            'nombrecliente' => $nombrecliente,
            'lugar' => $lugar,
        ]);
    }


    /**
     * @Route("/clientepedido/{id}", name="clientepedido")
     */
    public function clientepedido($id,Request $request): Response
    {
        //print_r($id); exit();
        $pedido = New Pedido();
        $form=$this->createForm(PedidoType::class, $pedido);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $em=$this->getDoctrine()->getManager();
            $pedido->setCliente($em->getRepository('App:Cliente')->find($id));
            $pedido->setFecha(New \DateTime());
            
            $em->persist($pedido);
            $em->flush();
            return $this->redirectToRoute('principal');

        }

        ///print_r($idcliente);exit();
        return $this->render('cliente/clientepedido.html.twig', [
            'form'=>$form->createView()
                       
        ]);
    }




    /**
     * @Route("/vercliente/", name="vercliente")
     */
    public function vercliente(Request $request): Response
    {
        
        $em = $this->getDoctrine()->getManager();
        $clientes = $em->getRepository('App:Cliente')->findclientesjoined($this->getUser()->getId());
        $form=$this->createForm(FiltroType::class);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //print_r('lsjdf');exit();
            
            $lugar= $form->get("lugar")->getData();
            $cliente= $form->get("cliente")->getData();
  
            $clientesfiltrados= $em->getRepository('App:Cliente')->findbylugarcliente($lugar,$cliente,$this->getUser()->getId());
            return $this->render('cliente/vercliente.html.twig', [
                'clientes' => $clientesfiltrados,
                'form' => $form->createView(),
            ]);

        }

// print_r(';slkdf');exit();

        return $this->render('cliente/vercliente.html.twig', [
            'clientes' => $clientes,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/pedidoscliente/{idcliente}/", name="pedidoscliente")
     */
    public function pedidoscliente($idcliente)
    {
        //print_r($idcliente);exit();
        $em=$this->getDoctrine()->getManager();
        $pedidoscliente = $em->getRepository('App:Pedido')->findpedidosbyCliente($idcliente,$this->getUser()->getId());
        //print_r($lugar);exit();
        return $this->render('cliente/pedidoscliente.html.twig', [
            'pedidoscliente' => $pedidoscliente,

        ]);
    }

    /**
     * @Route("/editarcliente/{idcliente}", name="editarcliente")
     */
    public function editarcliente($idcliente, Request $request): Response
    {//print_r($id);exit();
        $em=$this->getDoctrine()->getManager();
        $cliente= $em->getRepository('App:Cliente')->find($idcliente);
        $form = $this->createForm(ClienteEditaType::class,$cliente);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //print_r($cliente->getLugar()->getId());exit();
            // $cliente->setUser($this->getUser());
            
            $em->persist($cliente);
            $em->flush();
           
            //$lugar=$em->getRepository('App:Cliente')->lugarByIdCliente($cliente->getId());
            ////flash mensaje cliente creado ahora cree su pedido
            return $this->redirectToRoute('vercliente');

        }

        return $this->render('cliente/clienteedita.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/borrarpedido/{idpedido}", name="borrarpedido")
     */
    public function borrarpedido($idpedido){

        $em =$this->getDoctrine()->getManager();
        $pedido = $em->getRepository('App:Pedido')->find($idpedido);
        //print_r($pedido->getCliente()->getId());exit();
        //$pedidoscliente = $em->getRepository('App:Pedido')->findpedidosbyCliente($idcliente,$this->getUser()->getId());     
        $em->remove($pedido);
        $em->flush();

        $pedidoscliente = $em->getRepository('App:Pedido')->findpedidosbyCliente($pedido->getCliente()->getId(),$this->getUser()->getId());
        //print_r($lugar);exit();
        return $this->render('cliente/pedidoscliente.html.twig', [
            'pedidoscliente' => $pedidoscliente,

        ]);
    }
}
