<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\DistanceEnum;
use App\Repository\RaceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: RaceRepository::class)]
#[ApiResource]
class Race
{

    private $mediumFinishTime;
    private $longFinishTIme;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(length: 10)]
    // #[Assert\NotBlank]
    private ?string $date = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    private ?string $finishTIme = null;

    #[ORM\Column(length: 255)]
    private ?string $distance = null;

    #[ORM\Column(length: 255)]
    private ?int $overallPlacement = null;

    #[ORM\Column(length: 255)]
    private ?int $ageCategoryPlacement = null;

    #[ORM\Column(length: 255)]
    private ?string $age_category = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $created_at;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $updated_at;

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

    public function getDistance(): ?string
    {
        return $this->distance;
    }

    public function setDistance(string $distance): self
    {
        if (!in_array($distance, DistanceEnum::getAllowedValues(), true)) {
            throw new \InvalidArgumentException("Invalid status");
        }

        $this->distance = $distance;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getFinishTIme(): ?string
    {
        return $this->finishTIme;
    }

    public function setFinishTIme(string $finishTIme): static
    {
        $this->finishTIme = $finishTIme;

        return $this;
    }

    public function getOverallPlacement(): ?string
    {
        return $this->overallPlacement;
    }

    public function setOverallPlacement(string $overallPlacement): static
    {
        $this->overallPlacement = $overallPlacement;

        return $this;
    }

    public function getAgeCategoryPlacement(): ?string
    {
        return $this->ageCategoryPlacement;
    }

    public function setAgeCategoryPlacement(string $ageCategoryPlacement): static
    {
        $this->ageCategoryPlacement = $ageCategoryPlacement;

        return $this;
    }

    public function getAgeCategory(): ?string
    {
        return $this->age_category;
    }

    public function setAgeCategory(string $age_category): static
    {
        $this->age_category = $age_category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->created_at = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updated_at = $updatedAt;
        return $this;
    }

    #[ORM\PrePersist]
    public function prePersistTimestamps(): void
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function preUpdateTimestamp(): void
    {
        $this->updated_at = new \DateTime();
    }

    public function getMediumDistanceFinishTime(): ?string
    {
        if ($this->getDistance() === 'medium') {
            return $this->finishTIme; 
        }

        return null;
    }

    public function getLongDistanceFinishTime(): ?string
    {
        if ($this->getDistance() === 'long') {
            return $this->finishTIme;
        }

        return null;
    }

    public function calculateTimeDifference(): ?string
    {
        $mediumFinishTime = $this->getMediumDistanceFinishTimeAsTimestamp();
        $longFinishTime = $this->getLongDistanceFinishTimeAsTimestamp();

        if ($mediumFinishTime && $longFinishTime) {
            $timeDifference = $longFinishTime - $mediumFinishTime;
            return gmdate('H:i:s', $timeDifference); // Format the time difference
        }

        return null;
    }

    private function convertTimeToTimestamp(?string $time): ?int
    {
        if ($time) {
            $time = \DateTime::createFromFormat('H:i:s', $time);
            if ($time instanceof \DateTime) {
                return $time->getTimestamp();
            }
        }

        return null;
    }

    public function getMediumDistanceFinishTimeAsTimestamp(): ?int
    {
        return $this->convertTimeToTimestamp($this->getMediumDistanceFinishTime());
    }

    public function getLongDistanceFinishTimeAsTimestamp(): ?int
    {
        return $this->convertTimeToTimestamp($this->getLongDistanceFinishTime());
    }

}
