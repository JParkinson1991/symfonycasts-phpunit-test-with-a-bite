<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Dinosaur;
use AppBundle\Entity\Enclosure;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadBasicParkData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Accessible object references
     */
    const CARNIVORE_ENCLOSURE = "carnivorous-enclosure";
    const HERBIVORE_ENCLOSURE = "herbivorous-enclosure";

    /**
     * Sets the data fixtures load order
     *
     * Fixtures are run in order lowest to highest
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * Loads the data fixtures
     *
     * Creates three enclosures:
     * - Carnivore enclosure, adds 3 dinosaurs
     * - Herbivore enclosure, adds 1 dinosaur
     * - Empty enclosure
     *
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $carnivorousEnclosure = new Enclosure();
        $manager->persist($carnivorousEnclosure);
        $this->addReference(self::CARNIVORE_ENCLOSURE, $carnivorousEnclosure);
        $this->addDinosaur($manager, $carnivorousEnclosure, 'Velociraptor', true, 3);
        $this->addDinosaur($manager, $carnivorousEnclosure, 'Velociraptor', true, 1);
        $this->addDinosaur($manager, $carnivorousEnclosure, 'Velociraptor', true, 5);

        $herbivorousEnclosure = new Enclosure();
        $manager->persist($herbivorousEnclosure);
        $this->addReference(self::HERBIVORE_ENCLOSURE, $herbivorousEnclosure);
        $this->addDinosaur($manager, $herbivorousEnclosure, 'Triceratops', false, 7);

        $manager->persist(new Enclosure(true));

        $manager->flush();
    }

    /**
     * Creates and adds a dinosaur to an enclosure before saving to the
     * database
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param \AppBundle\Entity\Enclosure $enclosure
     * @param string $genus
     * @param bool $isCarnivorous
     * @param int $length
     */
    private function addDinosaur(
        ObjectManager $manager,
        Enclosure $enclosure,
        string $genus,
        bool $isCarnivorous,
        int $length
    ) {
        $dinosaur = new Dinosaur($genus, $isCarnivorous);
        $dinosaur->setEnclosure($enclosure);
        $dinosaur->setLength($length);

        $manager->persist($dinosaur);
    }
}
