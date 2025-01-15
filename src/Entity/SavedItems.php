<?php

namespace App\Entity;

use App\Repository\SavedItemsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SavedItemsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SavedItems
{

    #[ORM\PrePersist]
    public function persist(): void
    {
        $now = new \DateTimeImmutable();
        if (is_null($this->getDateCreated())) {
            $this->setDateCreated($now);
        }
    }
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $item = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItem(): ?string
    {
        return $this->item;
    }

    public function setItem(string $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }
}
