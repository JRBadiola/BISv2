<?php

namespace App\Controllers;

class TestEnv extends BaseController
{
    public function index()
    {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Test environment is working.',
            'php_version' => PHP_VERSION,
            'openrouter_key' => !empty(getenv('OPENROUTER_API_KEY'))
                ? 'FOUND'
                : 'NOT FOUND',
            'environment' => ENVIRONMENT,
            'time' => date('Y-m-d H:i:s')
        ]);
    }
}