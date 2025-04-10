<?php

namespace App\Entity;

use App\Repository\SecretaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecretaireRepository::class)]
class Secretaire extends User
{
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $service = null;

    public function __construct()
    {
        parent::__construct();
        $this->setRoles(['ROLE_SECRETAIRE']);
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(?string $service): static
    {
        $this->service = $service;

        return $this;
    }
} 