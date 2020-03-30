<?php
/**
 * @file
 * DinosaurLengthDeterminatorTest.php
 */

namespace Tests\Unit\AppBundle\Service;

use AppBundle\Entity\Dinosaur;
use AppBundle\Service\DinosaurLengthDeterminator;
use PHPUnit\Framework\TestCase;

/**
 * Class DinosaurLengthDeterminatorTest
 *
 * @package Tests\AppBundle\Service
 */
class DinosaurLengthDeterminatorTest extends TestCase
{
    /**
     * @param string $specification
     * @param int $minExpectedSize
     * @param int $maxExpectedSize
     *
     * @dataProvider getSpecLengthTests()
     *
     * @throws \Exception
     */
    public function testItReturnsCorrectLengthRange(string $specification, int $minExpectedSize, int $maxExpectedSize)
    {
        $determinator = new DinosaurLengthDeterminator();
        $actualSize = $determinator->getLengthFromSpecification($specification);

        $this->assertGreaterThanOrEqual($minExpectedSize, $actualSize);
        $this->assertLessThanOrEqual($maxExpectedSize, $actualSize);
    }

    public function getSpecLengthTests()
    {
        return [
            // Specification, expected min length, expected max length
            'large carnivore' => ['large carnivorous dinosaur', Dinosaur::LARGE, Dinosaur::HUGE - 1],
            'default response' => ['give me all the cookies!!', 0, Dinosaur::LARGE - 1],
            'large herbivore' => ['large herbivore', Dinosaur::LARGE, Dinosaur::HUGE - 1],
            ['huge dinosaur', Dinosaur::HUGE, 100],
            ['huge dino', Dinosaur::HUGE, 100],
            ['huge', Dinosaur::HUGE, 100],
            ['OMG', Dinosaur::HUGE, 100],
            ['😱', Dinosaur::HUGE, 100]
        ];
    }
}