<?php

namespace App\Repository;

use App\Entity\Consultation;
use App\Entity\Medecin;
use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Consultation>
 *
 * @method Consultation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Consultation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Consultation[]    findAll()
 * @method Consultation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultation::class);
    }

    public function save(Consultation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Consultation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByPatient(Patient $patient)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByMedecin(Medecin $medecin)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.medecin = :medecin')
            ->setParameter('medecin', $medecin)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDate(\DateTime $date)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function findCancelledByDate(\DateTime $date)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.date = :date')
            ->andWhere('c.statut = :statut')
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('statut', 'annulee')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find recent consultations for a patient
     */
    public function findRecentConsultations(Patient $patient, int $limit = 5)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.patient = :patient')
            ->andWhere('c.statut = :statut')
            ->setParameter('patient', $patient)
            ->setParameter('statut', 'terminee')
            ->orderBy('c.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find past consultations for a patient
     */
    public function findPastConsultations(Patient $patient, $offset = null, $limit = null)
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.patient = :patient')
            ->andWhere('c.statut = :statut')
            ->setParameter('patient', $patient)
            ->setParameter('statut', 'terminee')
            ->orderBy('c.date', 'DESC');
        
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }
        
        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Count past consultations for a patient
     */
    public function countPastConsultations(Patient $patient)
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.patient = :patient')
            ->andWhere('c.statut = :statut')
            ->setParameter('patient', $patient)
            ->setParameter('statut', 'terminee')
            ->getQuery()
            ->getSingleScalarResult();
    }
} 