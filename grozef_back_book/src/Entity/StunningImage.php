<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\StunningImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StunningImageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class StunningImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $authorFirstName = null;

    #[ORM\Column(length: 255)]
    private ?string $authorLastName = null;

    #[ORM\Column(nullable: true)]
    private ?int $rating = null;

    #[ORM\Column]
    private ?bool $isPublic = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $publishedDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $publisher = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filepath = null;

    /**
     * @var Collection<int, FiercePublisher>
     */
    #[ORM\ManyToMany(targetEntity: FiercePublisher::class, mappedBy: 'stunningImages')]
    private Collection $fiercePublishers;

    public function __construct()
    {
        $this->fiercePublishers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getAuthorFirstName(): ?string
    {
        return $this->authorFirstName;
    }

    public function setAuthorFirstName(string $authorFirstName): static
    {
        $this->authorFirstName = $authorFirstName;
        return $this;
    }

    public function getAuthorLastName(): ?string
    {
        return $this->authorLastName;
    }

    public function setAuthorLastName(string $authorLastName): static
    {
        $this->authorLastName = $authorLastName;
        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;
        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getPublishedDate(): ?\DateTime
    {
        return $this->publishedDate;
    }

    public function setPublishedDate(?\DateTime $publishedDate): static
    {
        $this->publishedDate = $publishedDate;
        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): static
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function getFilepath(): ?string
    {
        return $this->filepath;
    }

    public function setFilepath(?string $filepath): static
    {
        $this->filepath = $filepath;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * @return Collection<int, FiercePublisher>
     */
    public function getFiercePublishers(): Collection
    {
        return $this->fiercePublishers;
    }

    public function addFiercePublisher(FiercePublisher $fiercePublisher): static
    {
        if (!$this->fiercePublishers->contains($fiercePublisher)) {
            $this->fiercePublishers->add($fiercePublisher);
            $fiercePublisher->addStunningImage($this);
        }
        return $this;
    }

    public function removeFiercePublisher(FiercePublisher $fiercePublisher): static
    {
        if ($this->fiercePublishers->removeElement($fiercePublisher)) {
            $fiercePublisher->removeStunningImage($this);
        }
        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTime();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updated_at = new \DateTime();
    }
}
