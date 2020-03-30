<?php
/**
 * @file
 * EnclosureBuilderServiceTest.php
 */

namespace Tests\Unit\AppBundle\Service;

use AppBundle\Entity\Dinosaur;
use AppBundle\Entity\Enclosure;
use AppBundle\Exception\DinosaursRunningRampantException;
use AppBundle\Factory\DinosaurFactory;
use AppBundle\Service\EnclosureBuilderService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class EnclosureBuilderServiceTest
 *
 * @package Tests\AppBundle\Service
 */
class EnclosureBuilderServiceTest extends TestCase
{
    /**
     * Tests the enclosure builder correctly builds, populates and persists
     * enclosure models.
     *
     */
    public function testItBuildsAndPersistsEnclosure()
    {
        $dinosaurFactory = $this->buildMockDinosaurFactory(2);
        $em = $this->buildMockEntityManager(1, 1);

        $builder = new EnclosureBuilderService($em, $dinosaurFactory);
        $enclosure = $builder->buildEnclosureByQuantity(1, 2);

        $this->assertCount(1, $enclosure->getSecurities(), 'Securities not added as expected');
        $this->assertCount(2, $enclosure->getDinosaurs(), 'Dinosaurs not added as expected');
    }

    /**
     * Tests that an enclosure can be built verbosely with specifications.
     *
     * @throws \AppBundle\Exception\DinosaursRunningRampantException
     * @throws \AppBundle\Exception\EnclosureBuilderAddDinosaurException
     * @throws \AppBundle\Exception\NotABuffetException
     */
    public function testItBuildsAndPersistsVerboselyCreatedEnclosures()
    {
        // Creates the security and dinosaur specifications
        // Remember, this test only cares that the expected number of security
        // systems and dinosaurs are added to the enclosure, enclosure specific
        // tests handle restrictions on what can be added to them etc.
        $securitySpecifications = ['Fence', 'Gate', 'Guard'];
        $dinosaurSpecifications = ['Tyrannosaurs', 'Herbivore'];

        $dinosaurFactory = $this->buildMockDinosaurFactory(2);
        $em = $this->buildMockEntityManager(1, 1);

        $builder = new EnclosureBuilderService($em, $dinosaurFactory);
        $enclosure = $builder->buildEnclosureVerbose($securitySpecifications, $dinosaurSpecifications);

        // Ensure everything added as expected
        $this->assertCount(count($securitySpecifications), $enclosure->getSecurities());
        $this->assertCount(count($dinosaurSpecifications), $enclosure->getDinosaurs());


    }

    private function buildMockDinosaurFactory(int $expectedGrowFromSpecificationCalls)
    {
        $dinosaurFactory = $this->createMock(DinosaurFactory::class);
        $dinosaurFactory->expects($this->exactly($expectedGrowFromSpecificationCalls))
            ->method('growFromSpecification')
            ->with($this->isType('string'))
            ->willReturn(new Dinosaur());

        return $dinosaurFactory;
    }

    /**
     * @param int $expectedPersistCalls
     * @param int $expectedFlushCalls
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Doctrine\ORM\EntityManagerInterface
     */
    private function buildMockEntityManager(int $expectedPersistCalls, int $expectedFlushCalls = 1): MockObject
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->exactly($expectedPersistCalls))
            ->method('persist')
            ->with($this->isInstanceOf(Enclosure::class));
        $em->expects($this->exactly($expectedFlushCalls))
            ->method('flush');

        return $em;
    }
}