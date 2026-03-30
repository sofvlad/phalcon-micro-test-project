<?php

return [
    'issuer' => 'http://localhost:8080/',
    'secret' => $_ENV['JWT_SECRET'],
    'expires' => 86400,
    'algorithm' => 'sha256',
];
