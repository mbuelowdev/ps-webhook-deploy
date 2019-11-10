<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SecretRepository")
 */
class Secret
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $deployment_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $secret_key;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $secret_value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeploymentName(): ?string
    {
        return $this->deployment_name;
    }

    public function setDeploymentName(string $deployment_name): self
    {
        $this->deployment_name = $deployment_name;

        return $this;
    }

    public function getSecretKey(): ?string
    {
        return $this->secret_key;
    }

    public function setSecretKey(string $secret_key): self
    {
        $this->secret_key = $secret_key;

        return $this;
    }

    public function getSecretValue(): ?string
    {
        return $this->secret_value;
    }

    public function setSecretValue(?string $secret_value): self
    {
        $this->secret_value = $secret_value;

        return $this;
    }
}
