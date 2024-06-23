<?php

namespace App\Entity;

use App\Repository\EquipementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipementsRepository::class)]
class Equipements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $icon = null;

    #[ORM\ManyToMany(targetEntity: Annonce::class, inversedBy: 'equipements')]

    private Collection $annonce;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return Collection<int, Annonce>
     */
    public function getAnnonce(): Collection
    {
        return $this->annonce;
    }

    public function addAnnonce(Annonce $annonce): static
    {
        if (!$this->annonce->contains($annonce)) {
            $this->annonce->add($annonce);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): static
    {
        $this->annonce->removeElement($annonce);

        return $this;
    }

    public function __toString():string
{
    return $this->nom;
}
}
