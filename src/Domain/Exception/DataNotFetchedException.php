<?php
namespace BBC\ProgrammesPagesService\Domain\Exception;

use Exception;

/**
 * This exception indicates that data has been requested from a domain
 * object that hasn't actually been queried for.
 */
class DataNotFetchedException extends Exception
{
}
