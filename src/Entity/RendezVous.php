<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heure = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVous')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVous')]
    private ?Medecin $medecin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isConsultation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isPrestation = null;

    #[ORM\OneToOne(mappedBy: 'rendezVous', cascade: ['persist', 'remove'])]
    private ?Consultation $consultation = null;

    #[ORM\OneToOne(mappedBy: 'rendezVous', cascade: ['persist', 'remove'])]
    private ?Prestation $prestation = null;

    public function __construct()
    {
        $this->statut = 'demande';
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

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(\DateTimeInterface $heure): static
    {
        $this->heure = $heure;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;

        return $this;
    }

    public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin): static
    {
        $this->medecin = $medecin;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): static
    {
        $this->motif = $motif;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isIsConsultation(): ?bool
    {
        return $this->isConsultation;
    }

    public function setIsConsultation(?bool $isConsultation): static
    {
        $this->isConsultation = $isConsultation;

        return $this;
    }

    public function isIsPrestation(): ?bool
    {
        return $this->isPrestation;
    }

    public function setIsPrestation(?bool $isPrestation): static
    {
        $this->isPrestation = $isPrestation;

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
            $this->consultation->setRendezVous(null);
        }

        // set the owning side of the relation if necessary
        if ($consultation !== null && $consultation->getRendezVous() !== $this) {
            $consultation->setRendezVous($this);
        }

        $this->consultation = $consultation;

        return $this;
    }

    public function getPrestation(): ?Prestation
    {
        return $this->prestation;
    }

    public function setPrestation(?Prestation $prestation): static
    {
        // unset the owning side of the relation if necessary
        if ($prestation === null && $this->prestation !== null) {
            $this->prestation->setRendezVous(null);
        }

        // set the owning side of the relation if necessary
        if ($prestation !== null && $prestation->getRendezVous() !== $this) {
            $prestation->setRendezVous($this);
        }

        $this->prestation = $prestation;

        return $this;
    }

    public function peutEtreAnnule(): bool
    {
        $maintenant = new \DateTime();
        $dateRdv = (clone $this->date)->setTime(
            (int) $this->heure->format('H'),
            (int) $this->heure->format('i')
        );
        
        // Calculer la différence en heures
        $interval = $maintenant->diff($dateRdv);
        $heures = $interval->days * 24 + $interval->h;
        
        // Le RDV peut être annulé s'il reste plus de 48h
        return $heures > 48;
    }
} 