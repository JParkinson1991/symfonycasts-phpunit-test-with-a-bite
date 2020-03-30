<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Enclosure;
use AppBundle\Entity\Security;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSecurityData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Sets the data fixtures load order
     *
     * Fixtures are run in order lowest to highest
     *
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * Persists this fixture to the database
     *
     * Adds security fixtures to the enclosures created in the
     * \AppBundle\DataFixtures\ORM\LoadBasicParkData fixture.
     *
     * Adds a fence to the herbivore enclosure
     * Adds an electric fence and guard tower to the carnivore enclosure
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /* @var Enclosure $herbivorousEnclosure */
        $herbivorousEnclosure = $this->getReference(LoadBasicParkData::HERBIVORE_ENCLOSURE);
        $this->addSecurity($herbivorousEnclosure, 'Fence', true);

        /* @var Enclosure $carnivorousEnclosure */
        $carnivorousEnclosure = $this->getReference(LoadBasicParkData::CARNIVORE_ENCLOSURE);
        $this->addSecurity($carnivorousEnclosure, 'Electric fence', false);
        $this->addSecurity($carnivorousEnclosure, 'Guard tower', false);

        $manager->flush();
    }

    /**
     * Adds the security to the enclosure before persisting to the database
     *
     * @param \AppBundle\Entity\Enclosure $enclosure
     * @param string $name
     * @param bool $isActive
     */
    private function addSecurity(
        Enclosure $enclosure,
        string $name,
        bool $isActive
    ) {
        $enclosure->addSecurity(new Security($name, $isActive, $enclosure));
    }
}
