<?php

namespace App\Entity;

use App\Entity\Tricks;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImagesRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Tricks $idTrick = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTrick(): ?Tricks
    {
        return $this->idTrick;
    }

    public function setIdTrick(?Tricks $idTrick): self
    {
        $this->idTrick = $idTrick;

        return $this;
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
}
