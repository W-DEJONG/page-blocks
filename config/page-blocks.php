<?php

return [

    /*
     * Directories to scan for PageBlocks
     */
    'directories_to_scan' => [
        [
            'directory' => env('PAGEBLOCKS_FOLDER', 'app//PageBlocks'),
            'namespace' => env('PAGEBLOCKS_NAMESPACE', 'App\PageBlocks'),
        ],
    ],
];
