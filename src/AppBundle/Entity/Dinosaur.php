<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dinosaurs")
 */
class Dinosaur
{
    const LARGE = 10;
    const HUGE = 30;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $length = 0;

    /**
     * @ORM\Column(type="string")
     */
    private $genus;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCarnivorous;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Enclosure", inversedBy="dinosaurs")
     *
     * @var Enclosure
     */
    private $enclosure;

    /**
     * Dinosaur constructor.
     *
     * @param string $genus
     * @param bool   $isCarnivorous
     */
    public function __construct(string $genus = 'Unknown', bool $isCarnivorous = false)
    {
        $this->genus = $genus;
        $this->isCarnivorous = $isCarnivorous;
    }

    /**
     * Returns the length of the dinosaur in meters.
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Sets the length of the dinosaur
     *
     * @param int $length
     *     The length of the dinosaur in meters
     */
    public function setLength(int $length)
    {
        $this->length = $length;
    }

    /**
     * Returns the genus (type) of dinosaur
     *
     * @return string
     */
    public function getGenus(): string
    {
        return $this->genus;
    }

    /**
     * Returns boolean indication on whether dinosaur is carnivorous
     *
     * @return bool
     */
    public function isCarnivorous(): bool
    {
        return $this->isCarnivorous;
    }

    /**
     * Returns the specification of the dinosaur
     *
     * @return string
     */
    public function getSpecification(): string
    {
        return sprintf(
            'The %s %scarnivorous dinosaur is %d meters long',
            $this->genus,
            $this->isCarnivorous ? '' : 'non-',
            $this->length
        );
    }

    /**
     * Sets the enclosure this dinosaur belongs too
     *
     * @param \AppBundle\Entity\Enclosure $enclosure
     */
    public function setEnclosure(Enclosure $enclosure)
    {
        $this->enclosure = $enclosure;
    }
}
