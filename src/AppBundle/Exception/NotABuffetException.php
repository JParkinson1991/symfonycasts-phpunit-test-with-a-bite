<?php
/**
 * @file
 * NotABuffetException.php
 */

namespace AppBundle\Exception;

/**
 * Class NotABuffetException
 *
 * Thrown on attempt to mix dietary type dinosaurs to an enclosure.
 *
 * @package AppBundle\Exception
 */
class NotABuffetException extends \Exception
{
    protected $message = 'Please do not mix the carnivorous and non-carnivorous dinosaurs!';
}