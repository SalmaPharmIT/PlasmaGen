<?php

if (!function_exists('vite_assets')) {
    /**
     * A helper to handle Vite assets when not using npm
     *
     * @param array $entrypoints
     * @return string
     */
    function vite_assets($entrypoints)
    {
        $html = '';
        
        // Add CSS files
        foreach ($entrypoints as $entry) {
            if (pathinfo($entry, PATHINFO_EXTENSION) === 'css') {
                $html .= '<link rel="stylesheet" href="' . asset('assets/css/style.css') . '">' . PHP_EOL;
            }
        }
        
        // Add JS files
        foreach ($entrypoints as $entry) {
            if (pathinfo($entry, PATHINFO_EXTENSION) === 'js') {
                $html .= '<script src="' . asset('assets/js/main.js') . '" defer></script>' . PHP_EOL;
            }
        }
        
        return $html;
    }
} 