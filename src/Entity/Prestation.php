<?php

namespace App\Entity;

use App\Repository\PrestationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrestationRepository::class)]
class Prestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'prestations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $resultats = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'prestations')]
    private ?Consultation $consultation = null;

    #[ORM\ManyToOne(inversedBy: 'prestations')]
    private ?ResponsablePrestation $responsable = null;

    #[ORM\OneToOne(inversedBy: 'prestation', cascade: ['persist', 'remove'])]
    private ?RendezVous $rendezVous = null;
    
    #[ORM\Column(type: "float", nullable: true)]
    private ?float $prix = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $laboratoire = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $technicien = null;
    
    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;
    
    #[ORM\Column(type: "text", nullable: true)]
    private ?string $instructions = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $service = null;
    
    #[ORM\OneToOne(mappedBy: 'prestation', cascade: ['persist', 'remove'])]
    private ?Resultat $resultat = null;

    public function __construct()
    {
        $this->statut = 'programmee';
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function getResultats(): ?string
    {
        return $this->resultats;
    }

    public function setResultats(?string $resultats): static
    {
        $this->resultats = $resultats;

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

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): static
    {
        $this->consultation = $consultation;

        return $this;
    }

    public function getResponsable(): ?ResponsablePrestation
    {
        return $this->responsable;
    }

    public function setResponsable(?ResponsablePrestation $responsable): static
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function getRendezVous(): ?RendezVous
    {
        return $this->rendezVous;
    }

    public function setRendezVous(?RendezVous $rendezVous): static
    {
        $this->rendezVous = $rendezVous;

        return $this;
    }
    
    public function getPrix(): ?float
    {
        return $this->prix;
    }
    
    public function setPrix(?float $prix): static
    {
        $this->prix = $prix;
        
        return $this;
    }
    
    public function getLaboratoire(): ?string
    {
        return $this->laboratoire;
    }
    
    public function setLaboratoire(?string $laboratoire): static
    {
        $this->laboratoire = $laboratoire;
        
        return $this;
    }
    
    public function getTechnicien(): ?string
    {
        return $this->technicien;
    }
    
    public function setTechnicien(?string $technicien): static
    {
        $this->technicien = $technicien;
        
        return $this;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        
        return $this;
    }
    
    public function getInstructions(): ?string
    {
        return $this->instructions;
    }
    
    public function setInstructions(?string $instructions): static
    {
        $this->instructions = $instructions;
        
        return $this;
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
    
    public function getResultat(): ?Resultat
    {
        return $this->resultat;
    }
    
    public function setResultat(?Resultat $resultat): static
    {
        $this->resultat = $resultat;
        
        return $this;
    }
}