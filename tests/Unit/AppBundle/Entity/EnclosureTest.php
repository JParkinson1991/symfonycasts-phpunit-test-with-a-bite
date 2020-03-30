<?php
/**
 * @file
 * EnclosureTest.php
 */

namespace Tests\Unit\AppBundle\Entity;


use AppBundle\Entity\Dinosaur;
use AppBundle\Entity\Enclosure;
use AppBundle\Exception\DinosaursRunningRampantException;
use AppBundle\Exception\NotABuffetException;
use PHPUnit\Framework\TestCase;

/**
 * Class EnclosureTest
 *
 * @package Tests\AppBundle\Entity
 */
class EnclosureTest extends TestCase
{
    /**
     * Tests enclosures start without any dinosaurs
     */
    public function testItHasNoDinosaursByDefault()
    {
        $enclosure = new Enclosure();

        $this->assertEmpty($enclosure->getDinosaurs());
    }

    /**
     * Tests dinosaurs added to enclosure correctly
     *
     * @throws \AppBundle\Exception\NotABuffetException
     * @throws \AppBundle\Exception\DinosaursRunningRampantException
     */
    public function testItAddsDinosaurs()
    {
        $enclosure = new Enclosure(true);

        $enclosure->addDinosaur(new Dinosaur());
        $enclosure->addDinosaur(new Dinosaur());

        $this->assertCount(2, $enclosure->getDinosaurs());
    }

    /**
     * Tests carnivores can not be added to enclosures containing herbivores
     *
     * @throws \AppBundle\Exception\NotABuffetException
     * @throws \AppBundle\Exception\DinosaursRunningRampantException
     */
    public function testItDoesNotAllowCarnivorousDinosaursToMixWithHerbivores()
    {
        $enclosure = new Enclosure(true);
        $enclosure->addDinosaur(new Dinosaur('Herbivore', false));

        $this->expectException(NotABuffetException::class);
        $enclosure->addDinosaur(new Dinosaur('Carnivore', true));
    }

    /**
     * Test herbivores can not be added to enclosures containing carnivores.
     *
     * @expectedException \AppBundle\Exception\NotABuffetException
     *
     * @throws \AppBundle\Exception\DinosaursRunningRampantException
     * @throws \AppBundle\Exception\NotABuffetException
     */
    public function testItDoesNotAllowToAddNonCarnivorousDinosaursToACarnivorousEnclosure()
    {
        $enclosure = new Enclosure(true);
        $enclosure->addDinosaur(new Dinosaur('Carnivore', true));
        $enclosure->addDinosaur(new Dinosaur('Herbivore', false));
    }

    /**
     * Test dinosaurs can not be added to unsecured enclosures.
     *
     * @throws \AppBundle\Exception\DinosaursRunningRampantException
     * @throws \AppBundle\Exception\NotABuffetException
     */
    public function testItDoesNotAllowToAddDinosaursToUnsecureEnclosures()
    {
        $enclosure = new Enclosure();

        $this->expectException(DinosaursRunningRampantException::class);
        $this->expectExceptionMessage('Are you crazy!?');

        $enclosure->addDinosaur(new Dinosaur());
    }


}