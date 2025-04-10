<?php

namespace App\Entity;

use App\Repository\ResponsablePrestationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponsablePrestationRepository::class)]
class ResponsablePrestation extends User
{
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $service = null;

    #[ORM\OneToMany(mappedBy: 'responsable', targetEntity: Prestation::class)]
    private Collection $prestations;

    public function __construct()
    {
        parent::__construct();
        $this->prestations = new ArrayCollection();
        $this->setRoles(['ROLE_RESPONSABLE']);
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

    /**
     * @return Collection<int, Prestation>
     */
    public function getPrestations(): Collection
    {
        return $this->prestations;
    }

    public function addPrestation(Prestation $prestation): static
    {
        if (!$this->prestations->contains($prestation)) {
            $this->prestations->add($prestation);
            $prestation->setResponsable($this);
        }

        return $this;
    }

    public function removePrestation(Prestation $prestation): static
    {
        if ($this->prestations->removeElement($prestation)) {
            // set the owning side to null (unless already changed)
            if ($prestation->getResponsable() === $this) {
                $prestation->setResponsable(null);
            }
        }

        return $this;
    }
} 