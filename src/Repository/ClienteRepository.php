<?php

namespace App\Repository;

use App\Entity\Cliente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cliente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cliente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cliente[]    findAll()
 * @method Cliente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClienteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cliente::class);
    }



  /**
     * encuentra todas las comidas que tiene el pedido
     */
    
     public function lugarByIdCliente($idcliente){

        //print_r($idcliente);exit();
        $em=$this->getEntityManager();
        $dql="SELECT lug.nombre FROM App:Cliente cli
            JOIN cli.lugar lug
            WHERE cli.id = :idcliente"
            ;
        $lugar = $em->createQuery($dql)->setParameter('idcliente',$idcliente)->getSingleResult();
        return $lugar['nombre'];

     }


      /**
     * devuelve el cliente segun el iddel pedido
     */

    public function findclientebyidpedido($idpedido){
        $em=$this->getEntityManager();
        $dql = "SELECT cli.nombre,cli.apellido FROM App:Pedido pedi
        JOIN pedi.cliente cli
        WHERE pedi.id =:idpedido
        ";
        $nombrecliente = $em->createQuery($dql)->setParameter('idpedido',$idpedido)->getSingleResult();
        return $nombrecliente['nombre']. " ".$nombrecliente['apellido'] ;
    }


    /**
     * devuelve los los clientes registrados pur el usario logeado
     */

    public function findclientesjoined($iduser){
        $em=$this->getEntityManager();
        $dql= "SELECT cli.nombre,cli.apellido,lug.nombre as lugarnombre,cli.id FROM App:cliente cli      
        JOIN cli.user usr
        JOIN cli.lugar lug
        WHERE usr.id=:iduser  ORDER BY cli.nombre ASC
        "
        ;
        $clientes = $em->createQuery($dql)->setParameter('iduser',$iduser)->getResult();
        return $clientes;
    }





    /**
     * devuelve los clientes segun el lugar y su nombre segun el usuario que los creo
     */

    public function findbylugarcliente($lugar,$cliente,$iduser){
        

        //print_r($lugar->getId().$cliente,$iduser);exit();
        
        $em=$this->getEntityManager();

        if($lugar == NULL) {
            //print_r($iduser);exit();
            $dql1= "SELECT cli.nombre,cli.apellido,lug.nombre as lugarnombre, cli.id  FROM App:cliente cli
            JOIN cli.lugar lug
            JOIN cli.user usr
            WHERE (cli.nombre LIKE :cliente OR cli.apellido LIKE :cliente) AND usr.id=:iduser ORDER BY cli.nombre ASC
            "
            ;
            //print_r($dql);exit();
            $clientesfiltro1 = $em->createQuery($dql1)->setParameters(array('cliente'=>'%'.$cliente.'%','iduser'=>$iduser))->getResult();        
            //print_r($dql);exit();
            return $clientesfiltro1;


        } elseif ($cliente == NULL) {
            //print_r($lugar->getId());exit();
            $dql2= "SELECT cli.nombre,cli.apellido,lug.nombre as lugarnombre, cli.id  FROM App:cliente cli
            JOIN cli.lugar lug
            JOIN cli.user usr
            WHERE (lug.id =:lugar) AND (usr.id=:iduser) ORDER BY cli.nombre ASC
            "
            ;
            
            $clientesfiltro2 = $em->createQuery($dql2)->setParameters(array('lugar'=>$lugar->getId(),'iduser'=>$iduser))->getResult();
            //print_r('nolla');exit();
            return $clientesfiltro2;
        }
 

        $dql3= "SELECT cli.nombre,cli.apellido,lug.nombre as lugarnombre, cli.id FROM App:cliente cli
        JOIN cli.lugar lug
        JOIN cli.user usr
        WHERE (lug.id =:lugar OR cli.nombre LIKE :cliente) AND usr.id=:iduser ORDER BY cli.nombre ASC
        "
        ;
        $clientesfiltro3 = $em->createQuery($dql3)->setParameters(array('lugar'=>$lugar->getId(),'cliente'=>'%'.$cliente.'%','iduser'=>$iduser))->getResult();


        return $clientesfiltro3;
    }

    


    // /**
    //  * @return Cliente[] Returns an array of Cliente objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cliente
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
