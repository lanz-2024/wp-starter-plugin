<?php
/**
 * PSR-11 NotFoundExceptionInterface implementation.
 *
 * @package WPStarterPlugin
 */

declare(strict_types=1);

namespace WPStarterPlugin;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * Thrown when the DI container cannot resolve a requested identifier.
 */
class NotFoundException extends RuntimeException implements NotFoundExceptionInterface {
}
