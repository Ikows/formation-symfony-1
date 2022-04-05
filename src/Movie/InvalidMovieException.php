<?php

namespace App\Movie;

use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

class InvalidMovieException extends UnrecoverableMessageHandlingException
{
}