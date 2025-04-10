<?php

namespace App\Repository;

use App\Entity\Resultat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Resultat>
 *
 * @method Resultat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resultat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resultat[]    findAll()
 * @method Resultat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resultat::class);
    }

    public function save(Resultat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Resultat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find results by patient
     */
    public function findByPatient($patient)
    {
        return $this->createQueryBuilder('r')
            ->join('r.prestation', 'p')
            ->where('p.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('r.dateResultat', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find recent results for a patient
     */
    public function findRecentResultsForPatient($patient, $limit = 5)
    {
        return $this->createQueryBuilder('r')
            ->join('r.prestation', 'p')
            ->where('p.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('r.dateResultat', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
} 