<?php

namespace App\Repository;

use App\Entity\Secret;
use App\Helper\Texts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;

/**
 * @method Secret|null find($id, $lockMode = null, $lockVersion = null)
 * @method Secret|null findOneBy(array $criteria, array $orderBy = null)
 * @method Secret[]    findAll()
 * @method Secret[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecretRepository extends ServiceEntityRepository
{
    /**
     * SecretRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Secret::class);
    }

    /**
     * @param array $secrets
     * @param string $deploymentName
     * @throws \Exception
     */
    public function insertSecrets(array $secrets, string $deploymentName)
    {
        try {
            foreach ($secrets as $secret) {
                $s = new Secret();
                $s->setDeploymentName($deploymentName);
                $s->setSecretKey($secret[0]);
                $s->setSecretValue($secret[1]);
                $this->getEntityManager()->persist($s);
            }
            $this->getEntityManager()->flush();
        } catch (ORMException $e) {
            throw new \Exception(Texts::EXCEPTION_FAILED_TO_PERSIST, 500);
        }
    }

    /**
     * @param string $deploymentName
     * @throws \Exception
     */
    public function purgeSecrets(string $deploymentName)
    {
        try {
            $secrets = $this->findBy(array(
                'deployment_name' => $deploymentName
            ));

            foreach ($secrets as $secret) {
                $this->getEntityManager()->remove($secret);
            }

            $this->getEntityManager()->flush();
        } catch (ORMException $e) {
            throw new \Exception(Texts::EXCEPTION_FAILED_TO_REMOVE, 500);
        }
    }

    public function getSecretByName(string $deploymentName, string $secretKey)
    {
        try {
            $secrets = $this->findBy(array(
                'deployment_name' => $deploymentName,
                'secret_key' => $secretKey
            ));

            if (empty($secrets)) {
                $secret = new Secret();
                $secret->setSecretKey($secretKey);
                $secret->setSecretValue('({' . $secretKey . '})');

                return $secret;
            }

            return $secrets[0];

        } catch (ORMException $e) {
            throw new \Exception('TODO', 500);
        }
    }
}
