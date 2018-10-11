<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamsRepository")
 */
class Teams
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $skill_mu;

    /**
     * @ORM\Column(type="float")
     */
    private $skill_sigma;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSkillMu(): ?float
    {
        return $this->skill_mu;
    }

    public function setSkillMu(float $skill_mu): self
    {
        $this->skill_mu = $skill_mu;

        return $this;
    }

    public function getSkillSigma(): ?float
    {
        return $this->skill_sigma;
    }

    public function setSkillSigma(float $skill_sigma): self
    {
        $this->skill_sigma = $skill_sigma;

        return $this;
    }
}
