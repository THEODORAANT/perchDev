<?php
    return [
        'images' => [
            'type'      => 'file',
            'web_path'  => '/images',
            'file_path' => '/var/www/mysite/public_html/images',
        ],
        'whitepapers' => [
            'type'      => 'file',
            'web_path'  => '/whitepapers',
            'file_path' => '/var/www/mysite/public_html/whitepapers',
        ],
        'backup' => [
            'type'      => 'dropbox',
            'web_path'  => '',
            'file_path' => 'backups',
            'role'      => 'backup',
        ],
    ];
?>
