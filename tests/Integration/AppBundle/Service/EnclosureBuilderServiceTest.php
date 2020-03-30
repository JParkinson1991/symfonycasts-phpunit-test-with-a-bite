<?php
/**
 * @file
 * EnclosureBuilderServiceIntegrationTest.php
 */

namespace Tests\Integration\AppBundle\Service;

use AppBundle\Entity\Dinosaur;
use AppBundle\Entity\Security;
use AppBundle\Factory\DinosaurFactory;
use AppBundle\Service\EnclosureBuilderService;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class EnclosureBuilderServiceIntegrationTest
 *
 * @package Tests\AppBundle\Service
 */
class EnclosureBuilderServiceTest extends KernelTestCase
{
    /**
     * Set up test cases within this class
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp()
    {
        self::bootKernel();
        $this->truncateEntities();
    }

    /**
     * Tests an enclosure is built via request quantities of security measures
     * and dinosaurs.
     *
     * @throws \AppBundle\Exception\DinosaursRunningRampantException
     * @throws \AppBundle\Exception\NotABuffetException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testItBuildsEnclosureByQuantity()
    {
        $expectedSecurities = 1;
        $expectedDinosaurs = 3;

        // Pull the complete enclosure builder service from symfony's container
        // This is probably recommended in this instance as we are testing the
        // actual functionality of how the service persists enclosures to the
        // database using it's dependencies.
        //
        // However, another thought is that we are actually only testing if the
        // service is able to add what we expect to the database, that is, we
        // we expect to create x number of security entities, and y number of
        // dinosaur entities. Are we really concerned that these entities are
        // created correctly by factory dependencies etc? I mean, after all we
        // have already created unit tests to check for this.
        //
        // To truely isolate the checks for database persistence in this class
        // we can mock the dinosaur factory to simply give us a dinosaur
        // entity which we can save to the database as part of the enclosure.
        // This method (or partial mock) is uncommented below and used for this
        // test.
        /* @var \AppBundle\Service\EnclosureBuilderService $enclosureBuilderService */
        //$enclosureBuilderService = self::$kernel->getContainer()
        //    ->get('test.'.EnclosureBuilderService::class);

        $dinoFactory = $this->createMock(DinosaurFactory::class);
        $dinoFactory->expects($this->any())
            ->method('growFromSpecification')
            ->willReturnCallback(function($specification) {
                // Ensure a new dinosaur object is returned for each call
                // If using the willReturn() method, the same value is returned
                // for every call to this method, result, the same dinosaur
                // object being returned every time. Doctrine recognises this
                // dinosaur object is the same and will only persist it to the
                // database once, thus the tests checking for the expected
                // amount of dinosaurs if > 1 will always fail.

                return new Dinosaur();
            });

        $enclosureBuilderService = new EnclosureBuilderService(
            $this->getEntityManager(),
            $dinoFactory
        );

        $enclosureBuilderService->buildEnclosureByQuantity($expectedSecurities, $expectedDinosaurs);

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getEntityManager();

        $countSecurities = (int) $em->getRepository(Security::class)
            ->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertSame($expectedSecurities, $countSecurities);

        $countDinosaurs = (int) $em->getRepository(Dinosaur::class)
            ->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertSame($expectedDinosaurs, $countDinosaurs);
    }

    /**
     * Tests an enclosure is built properly using verbose specification of
     * both security measures and dinosaurs.
     *
     *
     * @throws \AppBundle\Exception\DinosaursRunningRampantException
     * @throws \AppBundle\Exception\EnclosureBuilderAddDinosaurException
     * @throws \AppBundle\Exception\NotABuffetException
     */
    public function testIfBuildsEnclosureVerbosely()
    {
        $securitySpecifications = [
            'Fence',
            'Guard',
            'Spotlight'
        ];

        // Remember not to mix herbivores and carnivores
        // This is not a buffet
        $dinosaurSpecifications = [
            'Large carnivorous dinosaur',
            'small carnivore'
        ];

        /* @var EnclosureBuilderService $enclosureBuilderService */
        $enclosureBuilderService = self::$kernel->getContainer()
            ->get('test.'.EnclosureBuilderService::class);

        $enclosureBuilderService->buildEnclosureVerbose(
            $securitySpecifications,
            $dinosaurSpecifications
        );

        $em = $this->getEntityManager();

        $securityNameResults = $em->getRepository(Security::class)
            ->createQueryBuilder('s')
            ->select('s.name')
            ->getQuery()
            ->getScalarResult();

        $securityNames = array_column($securityNameResults, "name");

        // Ensures the names of the created securities match those specified
        $this->assertEquals($securitySpecifications, $securityNames);

        $dinosaurResults = $em->getRepository(Dinosaur::class)
            ->findAll();

        // Ensure the number of dinosaurs created match the number of
        // specifications provided
        $this->assertSame(count($dinosaurSpecifications), count($dinosaurResults));

        // Check created dinosaurs attempt to match each to provided specification
        $largeCarnivoreCreated = false;
        $smallCarnivoreCreated = false;
        /* @var Dinosaur $dinosaur */
        foreach($dinosaurResults as $dinosaur) {
            if ($dinosaur->getLength() >= Dinosaur::LARGE && $dinosaur->isCarnivorous()) {
                $largeCarnivoreCreated = true;
                continue;
            }

            if ($dinosaur->getLength() < Dinosaur::LARGE && $dinosaur->isCarnivorous()) {
                $smallCarnivoreCreated = true;
                continue;
            }
        }

        $this->assertTrue($largeCarnivoreCreated, 'Failed to create \'Large carnivorous dinosaur\'');
        $this->assertTrue($smallCarnivoreCreated, 'Failed to create \'small carnivore\'');
    }

    /**
     * Truncates data tables for the passed entity classes
     *
     * @param array $entities
     *     The class names of the entities to truncate data for
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function truncateEntities()
    {
        /**
         * Old hat method
         *
         * This can be ignored in favour of using doctrine/data-fixtures
         * as demonstrated below
         *
        $connection = $this->getEntityManager()->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
        }

        foreach ($entities as $entity) {
            $query = $databasePlatform->getTruncateTableSQL(
                $this->getEntityManager()->getClassMetadata($entity)->getTableName()
            );

            $connection->executeUpdate($query);
        }

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
        }
        */

        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();
    }

    /**
     * Returns the doctrine entity manager
     *
     * @return \Doctrine\Common\Persistence\ObjectManager|\Doctrine\ORM\EntityManager
     */
    private function getEntityManager(): EntityManager
    {
        /* @var \Doctrine\ORM\EntityManager $em */
        $em = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        return $em;
    }
}