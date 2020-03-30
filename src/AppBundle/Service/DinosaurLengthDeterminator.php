<?php
/**
 * @file
 * DinosaurLengthDeterminator.php
 */

namespace AppBundle\Service;

use AppBundle\Entity\Dinosaur;

/**
 * Class DinosaurLengthDeterminator
 *
 * @package AppBundle\Service
 */
class DinosaurLengthDeterminator
{
    /**
     * Determines the length of dinosaur from a specification/description
     *
     * @param string $specification
     *     The description of the dinosaur to parse
     *
     * @return int
     *     The determined length of dinosaur
     *
     * @throws \Exception
     */
    public function getLengthFromSpecification(string $specification): int
    {
        $minLength = 1;
        $maxLength = Dinosaur::LARGE - 1;

        $availableLengths = [
            'huge' => [
                'min' => Dinosaur::HUGE,
                'max' => 100
            ],
            'omg' => [
                'min' => Dinosaur::HUGE,
                'max' => 100
            ],
            '😱' => [
                'min' => Dinosaur::HUGE,
                'max' => 100
            ],
            'large' => [
                'min' => Dinosaur::LARGE,
                'max' => Dinosaur::HUGE - 1
            ],
        ];

        foreach (explode(' ', $specification) as $keyword) {
            $keyword = strtolower($keyword);

            if (array_key_exists($keyword, $availableLengths)) {
                $minLength = $availableLengths[$keyword]['min'];
                $maxLength = $availableLengths[$keyword]['max'];

                break;
            }
        }

        return random_int($minLength, $maxLength);
    }
}