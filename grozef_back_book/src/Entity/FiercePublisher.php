<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\FiercePublisherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiercePublisherRepository::class)]
#[ORM\HasLifecycleCallbacks]
class FiercePublisher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at;

    /**
     * @var Collection<int, AstonishingVideo>
     */
    #[ORM\ManyToMany(targetEntity: AstonishingVideo::class, inversedBy: 'fiercePublishers')]
    private Collection $astonishingVideos;

    /**
     * @var Collection<int, StunningImage>
     */
    #[ORM\ManyToMany(targetEntity: StunningImage::class, inversedBy: 'fiercePublishers')]
    private Collection $stunningImages;

    /**
     * @var Collection<int, WonderfullBook>
     */
    #[ORM\ManyToMany(targetEntity: WonderfullBook::class, inversedBy: 'fiercePublishers')]
    private Collection $wonderfullBooks;

    public function __construct()
    {
        $this->astonishingVideos = new ArrayCollection();
        $this->stunningImages = new ArrayCollection();
        $this->wonderfullBooks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;
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
     * @return Collection<int, AstonishingVideo>
     */
    public function getAstonishingVideos(): Collection
    {
        return $this->astonishingVideos;
    }

    public function addAstonishingVideo(AstonishingVideo $astonishingVideo): static
    {
        if (!$this->astonishingVideos->contains($astonishingVideo)) {
            $this->astonishingVideos->add($astonishingVideo);
        }
        return $this;
    }

    public function removeAstonishingVideo(AstonishingVideo $astonishingVideo): static
    {
        $this->astonishingVideos->removeElement($astonishingVideo);
        return $this;
    }

    /**
     * @return Collection<int, StunningImage>
     */
    public function getStunningImages(): Collection
    {
        return $this->stunningImages;
    }

    public function addStunningImage(StunningImage $stunningImage): static
    {
        if (!$this->stunningImages->contains($stunningImage)) {
            $this->stunningImages->add($stunningImage);
        }
        return $this;
    }

    public function removeStunningImage(StunningImage $stunningImage): static
    {
        $this->stunningImages->removeElement($stunningImage);
        return $this;
    }

    /**
     * @return Collection<int, WonderfullBook>
     */
    public function getWonderfullBooks(): Collection
    {
        return $this->wonderfullBooks;
    }

    public function addWonderfullBook(WonderfullBook $wonderfullBook): static
    {
        if (!$this->wonderfullBooks->contains($wonderfullBook)) {
            $this->wonderfullBooks->add($wonderfullBook);
        }
        return $this;
    }

    public function removeWonderfullBook(WonderfullBook $wonderfullBook): static
    {
        $this->wonderfullBooks->removeElement($wonderfullBook);
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
