<?php

namespace AppBundle\Service;

use AppBundle\Entity\Enclosure;
use AppBundle\Entity\Security;
use AppBundle\Exception\EnclosureBuilderAddDinosaurException;
use AppBundle\Factory\DinosaurFactory;
use Doctrine\ORM\EntityManagerInterface;

class EnclosureBuilderService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var DinosaurFactory
     */
    private $dinosaurFactory;

    /**
     * EnclosureBuilderService constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \AppBundle\Factory\DinosaurFactory $dinosaurFactory
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        DinosaurFactory $dinosaurFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->dinosaurFactory = $dinosaurFactory;
    }

    /**
     * Builds an enclosure, populates it and persists it to the database
     *
     * @param int $numberOfSecuritySystems
     * @param int $numberOfDinosaurs
     *
     * @return \AppBundle\Entity\Enclosure
     * @throws \AppBundle\Exception\DinosaursRunningRampantException
     * @throws \AppBundle\Exception\NotABuffetException
     * @throws \Exception
     */
    public function buildEnclosureByQuantity(int $numberOfSecuritySystems = 1, int $numberOfDinosaurs = 3): Enclosure
    {
        $enclosure = new Enclosure();

        $securityNames = ['Fence', 'Electric fence', 'Guard tower'];
        for ($i = 0; $i < $numberOfSecuritySystems; $i++) {
            $securityName = $securityNames[array_rand($securityNames)];
            $security = new Security($securityName, true, $enclosure);

            $enclosure->addSecurity($security);
        }

        $lengths = ['small', 'large', 'huge'];
        $diets = ['herbivore', 'carnivorous'];
        // We should not mix herbivore and carnivorous together,
        // so use the same diet for every dinosaur.
        $diet = $diets[array_rand($diets)];
        for ($i = 0; $i < $numberOfDinosaurs; $i++) {
            $length = $lengths[array_rand($lengths)];
            $specification = "{$length} {$diet} dinosaur";
            $dinosaur = $this->dinosaurFactory->growFromSpecification($specification);

            $enclosure->addDinosaur($dinosaur);
        }

        $this->entityManager->persist($enclosure);
        $this->entityManager->flush();

        return $enclosure;
    }

    /**
     * Builds an enclosure from verbosely described security and dinosaur
     * specifications
     *
     * @param array $securitySpecifications
     * @param array $dinosaurSpecifications
     *
     * @return \AppBundle\Entity\Enclosure
     *
     * @throws \AppBundle\Exception\DinosaursRunningRampantException
     * @throws \AppBundle\Exception\NotABuffetException
     * @throws \AppBundle\Exception\EnclosureBuilderAddDinosaurException
     * @throws \Exception
     */
    public function buildEnclosureVerbose(array $securitySpecifications, array $dinosaurSpecifications)
    {
        $enclosure = new Enclosure();

        foreach ($securitySpecifications as $securityName) {
            $enclosure->addSecurity(new Security($securityName, true, $enclosure));
        }

        foreach ($dinosaurSpecifications as $dinosaurSpecification) {
            $enclosure->addDinosaur(
                $this->dinosaurFactory->growFromSpecification($dinosaurSpecification)
            );
        }

        $this->entityManager->persist($enclosure);
        $this->entityManager->flush();

        return $enclosure;
    }
}
