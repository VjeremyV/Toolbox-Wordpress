<?php

namespace App\Entity;

use App\Repository\FichierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FichierRepository::class)]
class Fichier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_pour_utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_bdd = null;



    #[ORM\ManyToOne(inversedBy: 'fichiers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\OneToMany(mappedBy: 'fichier', targetEntity: Elements::class, orphanRemoval: true)]
    private Collection $elements;

    #[ORM\ManyToOne(inversedBy: 'fichiers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Outils $outils = null;

    #[ORM\ManyToOne(inversedBy: 'fichiers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Date $date = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPourUtilisateur(): ?string
    {
        return $this->nom_pour_utilisateur;
    }

    public function setNomPourUtilisateur(string $nom_pour_utilisateur): self
    {
        $this->nom_pour_utilisateur = $nom_pour_utilisateur;

        return $this;
    }

    public function getNomBdd(): ?string
    {
        return $this->nom_bdd;
    }

    public function setNomBdd(string $nom_bdd): self
    {
        $this->nom_bdd = $nom_bdd;

        return $this;
    }

    

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection<int, elements>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(elements $element): self
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
            $element->setFichier($this);
        }

        return $this;
    }

    public function removeElement(elements $element): self
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getFichier() === $this) {
                $element->setFichier(null);
            }
        }

        return $this;
    }

    public function getOutils(): ?Outils
    {
        return $this->outils;
    }

    public function setOutils(?Outils $outils): self
    {
        $this->outils = $outils;

        return $this;
    }

    public function getDate(): ?Date
    {
        return $this->date;
    }

    public function setDate(?Date $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
