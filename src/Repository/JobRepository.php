<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Repository;

use App\Entity\Job;
use App\Helper\Texts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * @return Job[]
     */
    public function getAllUnfinishedJobs() : array
    {
        $jobs = $this->findBy(array(
            'erledigt_am' => null
        ));

        return $jobs;
    }

    /**
     * @param string $strData
     * @throws \Exception
     */
    public function insertNewJob(string $strData)
    {
        $job = new Job();
        $job->setDaten($strData);

        $this->persistAndFlush($job);
    }

    /**
     * @param $mixed mixed
     * @throws \Exception
     */
    private function persistAndFlush($mixed)
    {
        try {
            $this->getEntityManager()->persist($mixed);
            $this->getEntityManager()->flush();
        } catch (ORMException $e) {
            throw new \Exception(
                Texts::EXCEPTION_FAILED_TO_PERSIST,
                500,
                $e
            );
        }
    }

    /**
     * @param Job $job
     * @throws \Exception
     */
    public function remove(Job $job)
    {
        try {
            $this->getEntityManager()->remove($job);
            $this->getEntityManager()->flush();
        } catch (ORMException $e) {
            throw new \Exception(
                Texts::EXCEPTION_FAILED_TO_REMOVE,
                500,
                $e
            );
        }
    }

    /**
     * @param Job $job
     * @throws \Exception
     */
    public function setFinished(Job $job)
    {
        try {
            $date = new \DateTime('now', new \DateTimeZone('Europe/Berlin'));
            $job->setErledigtAm($date);
            $this->getEntityManager()->persist($job);
            $this->getEntityManager()->flush();
        } catch (ORMException $e) {
            throw new \Exception(
                Texts::EXCEPTION_FAILED_TO_PERSIST,
                500,
                $e
            );
        }
    }
}
