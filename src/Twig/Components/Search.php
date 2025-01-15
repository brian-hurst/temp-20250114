<?php

namespace App\Twig\Components;

use App\Service\APIService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Search
{
    use DefaultActionTrait;

    #[LiveProp(writable: true, url: true)]
    public ?string $query = null;

    public function __construct(private readonly APIService $api)
    {
    }

    public function getResults(): array
    {
        return $this->api->fetch($this->query);
    }
}
