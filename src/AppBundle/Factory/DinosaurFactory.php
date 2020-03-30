<?php
/**
 * @file
 * DinosaurFactory.php
 */

declare(strict_types=1);

namespace AppBundle\Factory;

use AppBundle\Entity\Dinosaur;
use AppBundle\Service\DinosaurLengthDeterminator;

/**
 * Class DinosaurFactory
 *
 * @package AppBundle\Factory
 */
class DinosaurFactory
{
    /**
     * @var \AppBundle\Service\DinosaurLengthDeterminator
     */
    private $lengthDeterminator;

    /**
     * DinosaurFactory constructor.
     *
     * @param \AppBundle\Service\DinosaurLengthDeterminator $lengthDeterminator
     */
    public function __construct(DinosaurLengthDeterminator $lengthDeterminator)
    {
        $this->lengthDeterminator = $lengthDeterminator;
    }

    /**
     * Grows and returns a Velociraptor dinosaur
     *
     * @param int $length
     *     The length of the dinosaur to grow in meters
     *
     * @return \AppBundle\Entity\Dinosaur
     */
    public function growVelociraptor(int $length): Dinosaur
    {
        return $this->createDinosaur(
            'Velociraptor',
            true,
            $length
        );
    }

    /**
     * Grows a dinosaur from a provided specification.
     *
     * @param string $specification
     *     The specification of the dinosaur to grow.
     *     Pass this method a description of request, this will be parsed and
     *     a relevant dinosaur and type grown and returned.
     *
     * @return \AppBundle\Entity\Dinosaur
     *
     * @throws \Exception
     */
    public function growFromSpecification(string $specification): Dinosaur
    {
        // Default dinosaur, unknown species, lab grown, give code name as
        // genus
        $codeName = 'InG-' . random_int(1, 99999);
        $length = $this->lengthDeterminator->getLengthFromSpecification($specification);
        $isCarnivorous = false;

        if (preg_match('/carnivorous|carnivore/i', $specification)) {
            $isCarnivorous = true;
        }

        return $this->createDinosaur($codeName, $isCarnivorous, $length);
    }

    /**
     * Creates a dinosaur from it's properties
     *
     * @param string $genus
     *     The genus (type) of dinosaur to grow
     * @param bool $isCarnivorous
     *     Is this dinosaur a carnivore?
     * @param int $length
     *     Requested length of the dinosaur
     *
     * @return \AppBundle\Entity\Dinosaur
     */
    private function createDinosaur(string $genus, bool $isCarnivorous, int $length): Dinosaur
    {
        $dinosaur = new Dinosaur($genus, $isCarnivorous);
        $dinosaur->setLength($length);

        return $dinosaur;
    }
}