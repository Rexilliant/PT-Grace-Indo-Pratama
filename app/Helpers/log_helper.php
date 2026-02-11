<?php

use App\Models\LogError;

if (! function_exists('save_log_error')) {

    function save_log_error(\Throwable $e): void
    {
        try {
            LogError::create([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
            ]);
        } catch (\Throwable $ignore) {
            // supaya tidak error berantai
        }
    }
}
