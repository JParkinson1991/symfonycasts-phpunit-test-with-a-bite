<?php
/**
 * @file
 * Enclosure.php
 */

namespace AppBundle\Entity;

use AppBundle\Exception\DinosaursRunningRampantException;
use AppBundle\Exception\NotABuffetException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Enclosure
 *
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="enclosure")
 */
class Enclosure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Holds the dinosaurs within the collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Dinosaur", mappedBy="enclosure", cascade={"persist"})
     *
     * @var Collection|\AppBundle\Entity\Dinosaur[]
     */
    private $dinosaurs;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Security", mappedBy="enclosure", cascade={"persist"})
     *
     * @var Collection|\AppBundle\Entity\Security[]
     */
    private $securities;

    /**
     * Enclosure constructor.
     */
    public function __construct(bool $withBasicSecurity = false)
    {
        $this->dinosaurs = new ArrayCollection();
        $this->securities = new ArrayCollection();

        if ($withBasicSecurity) {
            $this->addSecurity(new Security('Fence', true, $this));
        }
    }

    /**
     * Returns the enclosure id or null if not yet saved to the database
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the collection of dinosaurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDinosaurs()
    {
        return $this->dinosaurs;
    }

    /**
     * Adds a dinosaur to the enclosure.
     *
     * Note: Only dinosaurs of the same dietary (carnivore/herbivore) can be
     * added to the same enclosure.
     *
     * @param \AppBundle\Entity\Dinosaur $dinosaur
     *     The dinosaur to add
     *
     * @throws \AppBundle\Exception\NotABuffetException
     * @throws \AppBundle\Exception\DinosaursRunningRampantException
     */
    public function addDinosaur(Dinosaur $dinosaur)
    {
        if ($this->canAddDinosaur($dinosaur) === false) {
            throw new NotABuffetException();
        }

        if ($this->isSecurityActive() === false) {
            throw new DinosaursRunningRampantException("Are you crazy!?");
        }

        $this->dinosaurs[] = $dinosaur;
    }

    /**
     * Returns the number of dinosaurs in the enclosure
     *
     * @return int
     */
    public function getDinosaurCount(): int
    {
        return $this->dinosaurs->count();
    }

    /**
     * Adds a security measure to the enclosure
     *
     * @param \AppBundle\Entity\Security $security
     */
    public function addSecurity(Security $security)
    {
        $this->securities[] = $security;
    }

    /**
     * Returns the security measures for the enclosure
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSecurities(): Collection
    {
        return $this->securities;
    }

    /**
     * Determines if security active for the enclosure
     *
     * @return bool
     */
    public function isSecurityActive(): bool
    {
        foreach ($this->securities as $security) {
            if ($security->getIsActive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns indication on whether the passed dinosaur is compatible with
     * this enclosure.
     *
     * This method ensures only dinosaurs of the same dietary type are added
     * to a single enclosure.
     *
     * @param \AppBundle\Entity\Dinosaur $dinosaur
     *     The dinosaur to check.
     *
     * @return bool
     */
    private function canAddDinosaur(Dinosaur $dinosaur): bool
    {
        if ($this->dinosaurs->isEmpty()) {
            return true;
        }

        return $this->dinosaurs->first()->isCarnivorous() === $dinosaur->isCarnivorous();
    }
}