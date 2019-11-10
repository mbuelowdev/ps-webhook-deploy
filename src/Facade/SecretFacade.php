<?php
/**
 * @author 42Pollux
 * @since 2019-11-03
 */

namespace App\Facade;


use App\Dto\Request\AddSecretsActionDto;
use App\Dto\Request\PurgeSecretsActionDto;
use App\Entity\Secret;
use App\Repository\SecretRepository;
use Doctrine\ORM\EntityManagerInterface;

class SecretFacade
{
    /**
     * @var SecretRepository
     */
    private $secretRepository;

    /**
     * SecretFacade constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->secretRepository = $entityManager->getRepository(Secret::class);
    }

    /**
     * @param AddSecretsActionDto $dto
     * @throws \Exception
     */
    public function setSecrets(AddSecretsActionDto $dto)
    {
        $this->secretRepository->insertSecrets($dto->getSecrets(), $dto->getDeploymentName());
    }

    /**
     * @param PurgeSecretsActionDto $dto
     * @throws \Exception
     */
    public function purgeSecrets(PurgeSecretsActionDto $dto)
    {
        $this->secretRepository->purgeSecrets($dto->getDeploymentName());
    }
}