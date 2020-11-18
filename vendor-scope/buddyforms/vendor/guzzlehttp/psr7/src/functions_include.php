<?php

namespace tk;

// Don't redefine the functions if included multiple times.
if (!\function_exists('tk\\GuzzleHttp\\Psr7\\str')) {
    require __DIR__ . '/functions.php';
}
