<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(targetEntity:"App\Entity\Dishe", mappedBy:"category")]
    private $dishe;

    public function __construct()
    {
        $this->dishe = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Dishe>
     */
    public function getDishe(): Collection
    {
        return $this->dishe;
    }

    public function addDishe(Dishe $dishe): self
    {
        if (!$this->dishe->contains($dishe)) {
            $this->dishe[] = $dishe;
            $dishe->setCategory($this);
        }

        return $this;
    }

    public function removeDishe(Dishe $dishe): self
    {
        if ($this->dishe->removeElement($dishe)) {
            // set the owning side to null (unless already changed)
            if ($dishe->getCategory() === $this) {
                $dishe->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
