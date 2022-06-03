<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom du produit est requis.")
     * @Assert\Length(
     *  min=4,
     *  max=200,
     *  minMessage="Le nom doit contenir au moins {{ limit }} caractères.",
     *  maxMessage="Le nom ne doit pas faire plus de {{ limit }} caractères."
     * )
     */
    private ?string $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Il est nécessaire de renseigner un prix.")
     */
    private ?int $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url(message="Ce champ doit contenir une URL valide.")
     * @Assert\NotBlank(message="La photo principale est requise.")
     */
    private $mainPicture;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Une description courte est requise.")
     * @Assert\Length(
     *  min=20,
     *  max=255,
     *  minMessage="La description courte doit faire au moins {{ limit }} caractères.",
     *  maxMessage="La description courte doit faire au plus {{ limit }} caractères."
     * )
     */
    private $shortDescription;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUppercaseName(): string
    {
        return strtoupper($this->name);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getMainPicture(): ?string
    {
        return $this->mainPicture;
    }

    public function setMainPicture(?string $mainPicture): self
    {
        $this->mainPicture = $mainPicture;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }
}
