<?php declare(strict_types=1);

namespace Psr\Log;


/**
 * Exception interface for invalid logger arguments.
 *
 * Any time an invalid argument is passed into a method it must throw an exception class which
 * implements Psr\Log\InvalidArgumentException.
 */
class InvalidArgumentException extends \InvalidArgumentException
{
}
