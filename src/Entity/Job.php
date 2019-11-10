<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JobRepository")
 */
class Job
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
    private $daten;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $erledigt_am;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDaten(): ?string
    {
        return $this->daten;
    }

    public function setDaten(string $daten): self
    {
        $this->daten = $daten;

        return $this;
    }

    public function getErledigtAm(): ?\DateTimeInterface
    {
        return $this->erledigt_am;
    }

    public function setErledigtAm(?\DateTimeInterface $erledigt_am): self
    {
        $this->erledigt_am = $erledigt_am;

        return $this;
    }
}
