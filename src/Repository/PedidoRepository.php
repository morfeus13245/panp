<?php

namespace App\Repository;

use App\Entity\Pedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pedido|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pedido|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pedido[]    findAll()
 * @method Pedido[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PedidoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pedido::class);
    }


    /**
     * devuelve los pedidos de los clientes
     */

     public function findpedidosjoined($iduser){
         $em=$this->getEntityManager();
         $dql= "SELECT cli.nombre as clientenombre,cli.apellido,lug.nombre as lugarnombre,prod.nombre as productonombre,pedi.cantidad,prod.valor,pedi.id,pedi.fiado FROM App:pedido pedi
         JOIN pedi.producto prod
         JOIN pedi.cliente cli
         JOIN cli.lugar lug
         JOIN cli.user usr
         WHERE usr.id=:iduser ORDER BY cli.nombre ASC
         "
         ;
         $pedidos = $em->createQuery($dql)->setParameter('iduser',$iduser)->getResult();
         return $pedidos;
     }



     /**
     * devuelve los pedidos segun el cliente y el lugar
     */

    public function findbylugarcliente($lugar,$cliente,$iduser){
        

        //print_r($lugar->getId().$cliente,$iduser);exit();
        
        $em=$this->getEntityManager();

        if($lugar == NULL) {
            //print_r($iduser);exit();
            $dql1= "SELECT cli.nombre as clientenombre,cli.apellido,lug.nombre as lugarnombre,prod.nombre as productonombre,pedi.cantidad,prod.valor,pedi.id,pedi.fiado FROM App:pedido pedi
            JOIN pedi.producto prod
            JOIN pedi.cliente cli
            JOIN cli.lugar lug
            JOIN cli.user usr
            WHERE (cli.nombre LIKE :cliente OR cli.apellido LIKE :cliente) AND usr.id=:iduser  ORDER BY cli.nombre ASC
            "
            ;
            //print_r($dql);exit();
            $pedidosfiltro1 = $em->createQuery($dql1)->setParameters(array('cliente'=>'%'.$cliente.'%','iduser'=>$iduser))->getResult();        
            //print_r($dql);exit();
            return $pedidosfiltro1;


        } elseif ($cliente == NULL) {
            //print_r($lugar->getId());exit();
            $dql2= "SELECT cli.nombre as clientenombre,cli.apellido,lug.nombre as lugarnombre,prod.nombre as productonombre,pedi.cantidad,prod.valor,pedi.id,pedi.fiado FROM App:pedido pedi
            JOIN pedi.producto prod
            JOIN pedi.cliente cli
            JOIN cli.lugar lug
            JOIN cli.user usr
            WHERE (lug.id =:lugar) AND (usr.id=:iduser)  ORDER BY cli.nombre ASC
            "
            ;
            
            $pedidosfiltro2 = $em->createQuery($dql2)->setParameters(array('lugar'=>$lugar->getId(),'iduser'=>$iduser))->getResult();
            //print_r('nolla');exit();
            return $pedidosfiltro2;
        }
 

        $dql3= "SELECT cli.nombre as clientenombre,cli.apellido,lug.nombre as lugarnombre,prod.nombre as productonombre,pedi.cantidad,prod.valor,pedi.id,pedi.fiado FROM App:pedido pedi
        JOIN pedi.producto prod
        JOIN pedi.cliente cli
        JOIN cli.lugar lug
        JOIN cli.user usr
        WHERE (lug.id =:lugar OR cli.nombre LIKE :cliente) AND usr.id=:iduser  ORDER BY cli.nombre ASC
        "
        ;
        $pedidosfiltro3 = $em->createQuery($dql3)->setParameters(array('lugar'=>$lugar->getId(),'cliente'=>'%'.$cliente.'%','iduser'=>$iduser))->getResult();


        return $pedidosfiltro3;
    }


    /**
     * devuelve los pedidos que un cliente a hecho, en criterio del usuario que lo creo por supuesto
     */

    public function findpedidosbyCliente($idcliente, $iduser){
        $em=$this->getEntityManager();
        $dql="SELECT prod.nombre,pedi.cantidad,prod.valor,pedi.fiado,pedi.id FROM App:Pedido pedi
        JOIN pedi.producto prod
        JOIN pedi.cliente cli
        JOIN cli.user usr
        WHERE cli.id =:idcliente AND usr.id=:iduser ORDER BY cli.nombre ASC
         ";
        $pedidoscliente = $em->createQuery($dql)->setParameters(array('idcliente'=>$idcliente,'iduser'=>$iduser))->getResult();          
        return $pedidoscliente;
    }


     /**
     * devuelve el reporte de los pedidos echos ordenados segun lugar
     */
    public function reporte($iduser){
        $em=$this->getEntityManager();
        $dql="SELECT cli.nombre as clientenombre,cli.apellido,lug.nombre as lugarnombre,pedi.cantidad,pedi.fiado,prod.valor FROM App:Pedido pedi
        JOIN pedi.cliente cli
        JOIN pedi.producto prod
        JOIN cli.user usr
        JOIN cli.lugar lug
        WHERE usr.id=:iduser ORDER BY lug.nombre ASC 
        ";
        $pedidoslugar = $em->createQuery($dql)->setParameter('iduser',$iduser)->getResult();
        
        return $pedidoslugar;


    }

    // /**
    //  * @return Pedido[] Returns an array of Pedido objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pedido
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
