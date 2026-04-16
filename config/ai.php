<?php

return [
    'provider' => env('AI_PROVIDER', 'ollama'),
    'base_url' => env('AI_BASE_URL', 'http://127.0.0.1:11434'),
    'model'    => env('AI_MODEL', 'llama3.2'),
    'timeout'  => env('AI_TIMEOUT', 120),
];
