<?php

namespace App\Model;

use App\Entity\SavedItems;

class DisplayModel
{
    private ?string $item = null;
    private ?string $title = null;
    private ?SavedItems $savedItems = null;
    private ?string $image = null;

    public function getItem(): ?string
    {
        return $this->item;
    }

    public function setItem(?string $item): DisplayModel
    {
        $this->item = $item;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): DisplayModel
    {
        $this->title = $title;
        return $this;
    }

    public function getSavedItems(): ?SavedItems
    {
        return $this->savedItems;
    }

    public function setSavedItems(?SavedItems $savedItems): DisplayModel
    {
        $this->savedItems = $savedItems;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): DisplayModel
    {
        $this->image = $image;
        return $this;
    }

}