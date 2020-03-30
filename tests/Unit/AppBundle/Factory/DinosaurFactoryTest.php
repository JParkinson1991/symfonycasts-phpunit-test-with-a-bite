<?php
/**
 * @file
 * DinosaurFactoryTest.php
 */

namespace Tests\Factory\AppBundle\Factory;

use AppBundle\Entity\Dinosaur;
use AppBundle\Factory\DinosaurFactory;
use AppBundle\Service\DinosaurLengthDeterminator;
use PHPUnit\Framework\TestCase;

/**
 * Class DinosaurFactoryTest
 *
 * @package Tests\AppBundle\Factory
 */
class DinosaurFactoryTest extends TestCase
{
    /**
     * @var DinosaurFactory
     */
    private $factory;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\AppBundle\Service\DinosaurLengthDeterminator
     */
    private $lengthDeterminator;

    /**
     * Sets up this class prior to running each test method.
     *
     * Uses to set a fresh dinosaur factory for each test method.
     */
    public function setUp()
    {
        $this->lengthDeterminator = $this->createMock(DinosaurLengthDeterminator::class);
        $this->factory = new DinosaurFactory($this->lengthDeterminator);
    }

    /**
     * Tests the factory can grow a Velociraptor correctly.
     */
    public function testItGrowsAVelociraptor()
    {
        $dinosaur = $this->factory->growVelociraptor(5);

        $this->assertInstanceOf(Dinosaur::class, $dinosaur);
        $this->assertInternalType('string', $dinosaur->getGenus());
        $this->assertSame('Velociraptor', $dinosaur->getGenus());
        $this->assertSame(5, $dinosaur->getLength());
    }

    /**
     * Tests the factory can grow a Triceratops correctly.
     *
     * Currently incomplete.
     */
    public function testItGrowsATriceratops()
    {
        $this->markTestIncomplete('Waiting for confirmation from GenLab.');
    }

    /**
     * Tests the factory can grow a baby Velociraptor correctly.
     */
    public function testItGrowsABabyVelociraptor()
    {
        if (!class_exists('Nanny')) {
            $this->markTestSkipped('Nobody to look after the baby raptor');
        }

        $dinosaur = $this->factory->growVelociraptor(1);
        $this->assertSame(1, $dinosaur->getLength());
    }

    /**
     * Tests that factory is able to parse and grow a dinosaur from a provided
     * specification.
     *
     * The data fed to this method is provided by dataProvider
     *
     * @param string $specification
     *     The specification of the dinosaur
     * @param bool $expectedIsLarge
     *     Whether the specification expects a large dinosaur to be returned
     * @param bool $expectedIsCarnivorous
     *     Whether the specification expects a carnivore to be returned.
     *
     * @dataProvider getSpecificationTestData()
     *
     * @throws \Exception
     */
    public function testItGrowsADinosaurFromASpecification(string $specification, bool $expectedIsCarnivorous)
    {
        // Everytime we use the length determinator service object (factory
        // dependency) for this test, it should return 20. Set this up to
        // enable certainty tests that the factory is in fact using the
        // determinator service to get the length value rather than (for
        // example) hardcoded within the factory builder methods.
        $this->lengthDeterminator->expects($this->once())
            ->method('getLengthFromSpecification')
            ->with($specification)
            ->willReturn(20);

        $dinosaur = $this->factory->growFromSpecification($specification);
        $this->assertSame($expectedIsCarnivorous, $dinosaur->isCarnivorous());

        // If this assertation fails, we can assume that the dinosaur factory is
        // not using the length determinator when creating dinosaurs from a
        // specification string
        $this->assertSame(20, $dinosaur->getLength());
    }

    /**
     * Data provider for the specification tests
     * 
     * @return array
     */
    public function getSpecificationTestData()
    {
        return [
            // Specification, is carnivorous
            'large carnivore' => ['large carnivorous dinosaur', true],
            'default response' => ['give me all the cookies!!', false],
            'large herbivore' => ['large herbivore', false],
        ];
    }
}