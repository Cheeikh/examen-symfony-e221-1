<?php

namespace App\Repository;

use App\Entity\RendezVous;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RendezVous>
 *
 * @method RendezVous|null find($id, $lockMode = null, $lockVersion = null)
 * @method RendezVous|null findOneBy(array $criteria, array $orderBy = null)
 * @method RendezVous[]    findAll()
 * @method RendezVous[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

    public function save(RendezVous $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RendezVous $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByPatient($patient)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByMedecin($medecin)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.medecin = :medecin')
            ->setParameter('medecin', $medecin)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDate(\DateTime $date)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->orderBy('r.heure', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find upcoming consultations for a patient
     */
    public function findUpcomingConsultations($patient, $offset = null, $limit = null)
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.patient = :patient')
            ->andWhere('r.date >= :today')
            ->andWhere('r.isConsultation = :isConsultation')
            ->andWhere('r.statut != :canceled')
            ->setParameter('patient', $patient)
            ->setParameter('today', $today)
            ->setParameter('isConsultation', true)
            ->setParameter('canceled', 'annule')
            ->orderBy('r.date', 'ASC')
            ->addOrderBy('r.heure', 'ASC');
        
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }
        
        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Count upcoming consultations for a patient
     */
    public function countUpcomingConsultations($patient)
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.patient = :patient')
            ->andWhere('r.date >= :today')
            ->andWhere('r.isConsultation = :isConsultation')
            ->andWhere('r.statut != :canceled')
            ->setParameter('patient', $patient)
            ->setParameter('today', $today)
            ->setParameter('isConsultation', true)
            ->setParameter('canceled', 'annule')
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    /**
     * Find all consultations for a patient
     */
    public function findAllConsultations($patient, $offset = null, $limit = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.patient = :patient')
            ->andWhere('r.isConsultation = :isConsultation')
            ->setParameter('patient', $patient)
            ->setParameter('isConsultation', true)
            ->orderBy('r.date', 'DESC')
            ->addOrderBy('r.heure', 'DESC');
        
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }
        
        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Count all consultations for a patient
     */
    public function countAllConsultations($patient)
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.patient = :patient')
            ->andWhere('r.isConsultation = :isConsultation')
            ->setParameter('patient', $patient)
            ->setParameter('isConsultation', true)
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    /**
     * Find cancelled consultations for a patient
     */
    public function findCancelledConsultations($patient, $offset = null, $limit = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.patient = :patient')
            ->andWhere('r.isConsultation = :isConsultation')
            ->andWhere('r.statut = :status')
            ->setParameter('patient', $patient)
            ->setParameter('isConsultation', true)
            ->setParameter('status', 'annule')
            ->orderBy('r.date', 'DESC')
            ->addOrderBy('r.heure', 'DESC');
        
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }
        
        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Count cancelled consultations for a patient
     */
    public function countCancelledConsultations($patient)
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.patient = :patient')
            ->andWhere('r.isConsultation = :isConsultation')
            ->andWhere('r.statut = :status')
            ->setParameter('patient', $patient)
            ->setParameter('isConsultation', true)
            ->setParameter('status', 'annule')
            ->getQuery()
            ->getSingleScalarResult();
    }
} 