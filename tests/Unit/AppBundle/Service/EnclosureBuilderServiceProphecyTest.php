<?php
/**
 * @file
 * EnclosureBuilderServiceProphecyTest.php
 */

namespace Tests\Unit\AppBundle\Service;

use AppBundle\Entity\Dinosaur;
use AppBundle\Entity\Enclosure;
use AppBundle\Factory\DinosaurFactory;
use AppBundle\Service\EnclosureBuilderService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * Class EnclosureBuilderServiceProphecyTest
 *
 * @package Tests\AppBundle\Service
 */
class EnclosureBuilderServiceProphecyTest extends TestCase
{
    /**
     * Tests the enclosure builder correctly builds, populates and persists
     * enclosure models.
     *
     * @throws \Exception
     */
    public function testItBuildsAndPersistsEnclosure()
    {
        $em = $this->prophesize(EntityManagerInterface::class);
        $em->persist(Argument::type(Enclosure::class))
            ->shouldBeCalledTimes(1);

        $em->flush()
            ->shouldBeCalled();

        $dinosaurFactory = $this->prophesize(DinosaurFactory::class);
        $dinosaurFactory->growFromSpecification(Argument::type('string'))
            ->shouldBeCalledTimes(2)
            ->willReturn(new Dinosaur());

        $builder = new EnclosureBuilderService($em->reveal(), $dinosaurFactory->reveal());
        $enclosure = $builder->buildEnclosureByQuantity(1, 2);

        $this->assertCount(1, $enclosure->getSecurities(), 'Securities not added as expected');
        $this->assertCount(2, $enclosure->getDinosaurs(), 'Dinosaurs not added as expected');
    }
}