<?php

namespace App\Service;

use App\Model\DisplayModel;
use App\Repository\SavedItemsRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class APIService implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly SavedItemsRepository $savedItemsRepository,
    )
    {
    }

    public function fetch(?string $query, ?int $limit = 100): array
    {
        $results = [];


        try {
            if(!$query) {
                $query = 'the+lord+of+the+rings';
            }
            $uri = sprintf('https://openlibrary.org/search.json?title=%s&limit=%d', $query, $limit);
            $response = $this->client->request('GET', $uri);
            $resultsContainer = json_decode($response->getContent(), true);

            foreach($resultsContainer['docs'] as $doc){
                $model = (new DisplayModel())
                    ->setTitle($doc['title'])
                    ->setItem($doc['key']);

                if(array_key_exists('cover_edition_key', $doc)){
                    $model->setImage('https://covers.openlibrary.org/b/olid/' . $doc['cover_edition_key'] . '-L.jpg');
                }
                $saved = $this->savedItemsRepository->findOneBy(['item' => $model->getItem()]);

                if($saved){
                    $model->setSavedItems($saved);
                }

                $results[] = $model;
            };
        } catch (TransportExceptionInterface|ClientExceptionInterface $e) {
            $this->logger->error('API Transport Error', [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ]
            );
        }

        return $results;
    }
}