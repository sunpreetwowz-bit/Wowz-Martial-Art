<?php

/**
 * Shared-hosting entry point (InfinityFree, cPanel, etc.).
 * Forwards every request into Laravel's real public/index.php.
 */
require __DIR__.'/public/index.php';
