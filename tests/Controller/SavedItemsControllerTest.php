<?php

namespace App\Tests\Controller;

use App\Entity\SavedItems;
use App\Repository\SavedItemsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SavedItemsControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $savedItemRepository;
    private string $path = '/saved/items/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->savedItemRepository = $this->manager->getRepository(SavedItems::class);

        foreach ($this->savedItemRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Search');
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));
        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'saved_items[item]' => 'Testing',
        ]);

        self::assertResponseRedirects('/saved/items');

        self::assertSame(1, $this->savedItemRepository->count([]));
    }

    public function testShow(): void
    {
        $fixture = new SavedItems();
        $fixture->setItem('Test');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Search');

    }

    public function testEdit(): void
    {
        $fixture = new SavedItems();
        $fixture->setItem('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'saved_items[item]' => 'Something New',
        ]);

        self::assertResponseRedirects('/saved/items');

        $fixture = $this->savedItemRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getItem());
    }

    public function testRemove(): void
    {
        $fixture = new SavedItems();
        $fixture->setItem('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/saved/items');
        self::assertSame(0, $this->savedItemRepository->count([]));
    }
}
