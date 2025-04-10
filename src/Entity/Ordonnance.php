<?php

namespace App\Entity;

use App\Repository\OrdonnanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdonnanceRepository::class)]
class Ordonnance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToOne(mappedBy: 'ordonnance', cascade: ['persist', 'remove'])]
    private ?Consultation $consultation = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: "json")]
    private array $medicaments = [];

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): static
    {
        // unset the owning side of the relation if necessary
        if ($consultation === null && $this->consultation !== null) {
            $this->consultation->setOrdonnance(null);
        }

        // set the owning side of the relation if necessary
        if ($consultation !== null && $consultation->getOrdonnance() !== $this) {
            $consultation->setOrdonnance($this);
        }

        $this->consultation = $consultation;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMedicaments(): array
    {
        return $this->medicaments;
    }

    public function setMedicaments(array $medicaments): static
    {
        $this->medicaments = $medicaments;

        return $this;
    }

    public function addMedicament(string $code, string $nom, string $posologie): static
    {
        $this->medicaments[] = [
            'code' => $code,
            'nom' => $nom,
            'posologie' => $posologie
        ];

        return $this;
    }

    public function removeMedicament(int $index): static
    {
        if (isset($this->medicaments[$index])) {
            unset($this->medicaments[$index]);
            $this->medicaments = array_values($this->medicaments);
        }

        return $this;
    }
}