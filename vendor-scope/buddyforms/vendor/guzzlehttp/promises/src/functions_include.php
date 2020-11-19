<?php

namespace tk;

// Don't redefine the functions if included multiple times.
if (!\function_exists('tk\\GuzzleHttp\\Promise\\promise_for')) {
    require __DIR__ . '/functions.php';
}
