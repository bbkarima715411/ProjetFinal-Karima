<?php

namespace App\Test\Controller;

use App\Entity\Lot;
use App\Repository\LotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LotControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private LotRepository $repository;
    private string $path = '/lot/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Lot::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Lot index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'lot[Lot]' => 'Testing',
            'lot[Categorie]' => 'Testing',
            'lot[Paiement]' => 'Testing',
            'lot[Facture]' => 'Testing',
            'lot[evenementEnchere]' => 'Testing',
        ]);

        self::assertResponseRedirects('/lot/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Lot();
        $fixture->setLot('My Title');
        $fixture->setCategorie('My Title');
        $fixture->setPaiement('My Title');
        $fixture->setFacture('My Title');
        $fixture->setEvenementEnchere('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Lot');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Lot();
        $fixture->setLot('My Title');
        $fixture->setCategorie('My Title');
        $fixture->setPaiement('My Title');
        $fixture->setFacture('My Title');
        $fixture->setEvenementEnchere('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'lot[Lot]' => 'Something New',
            'lot[Categorie]' => 'Something New',
            'lot[Paiement]' => 'Something New',
            'lot[Facture]' => 'Something New',
            'lot[evenementEnchere]' => 'Something New',
        ]);

        self::assertResponseRedirects('/lot/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getLot());
        self::assertSame('Something New', $fixture[0]->getCategorie());
        self::assertSame('Something New', $fixture[0]->getPaiement());
        self::assertSame('Something New', $fixture[0]->getFacture());
        self::assertSame('Something New', $fixture[0]->getEvenementEnchere());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Lot();
        $fixture->setLot('My Title');
        $fixture->setCategorie('My Title');
        $fixture->setPaiement('My Title');
        $fixture->setFacture('My Title');
        $fixture->setEvenementEnchere('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/lot/');
    }
}
