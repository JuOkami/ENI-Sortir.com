<?php

namespace App\Entity;

use App\Repository\SortieFiltreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


class SortieFiltre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Site $site = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateMin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateMax = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isOrganisateur = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isInscrit = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isPasse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDateMin(): ?\DateTimeInterface
    {
        return $this->dateMin;
    }

    public function setDateMin(?\DateTimeInterface $dateMin): static
    {
        $this->dateMin = $dateMin;

        return $this;
    }

    public function getDateMax(): ?\DateTimeInterface
    {
        return $this->dateMax;
    }

    public function setDateMax(?\DateTimeInterface $dateMax): static
    {
        $this->dateMax = $dateMax;

        return $this;
    }

    public function isIsOrganisateur(): ?bool
    {
        return $this->isOrganisateur;
    }

    public function setIsOrganisateur(?bool $isOrganisateur): static
    {
        $this->isOrganisateur = $isOrganisateur;

        return $this;
    }

    public function isIsInscrit(): ?bool
    {
        return $this->isInscrit;
    }

    public function setIsInscrit(?bool $isInscrit): static
    {
        $this->isInscrit = $isInscrit;

        return $this;
    }

    public function isIsPasse(): ?bool
    {
        return $this->isPasse;
    }

    public function setIsPasse(?bool $isPasse): static
    {
        $this->isPasse = $isPasse;

        return $this;
    }
}
