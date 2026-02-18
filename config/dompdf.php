<?php

return array(
    'show_warnings' => false,
    'orientation' => 'portrait',
    'defines' => array(
        'dompdf_dir' => realpath(base_path('vendor/dompdf/dompdf')),
        'dompdf_font_dir' => realpath(base_path('vendor/dompdf/dompdf/lib/fonts')),
        'dompdf_font_cache_dir' => realpath(storage_path('fonts')),
        'dompdf_tmp_dir' => realpath(sys_get_temp_dir()),
        'dompdf_chroot' => realpath(base_path()),
    ),
    'options' => array(
        'font_cache' => storage_path('fonts'),
        'font_dir' => storage_path('fonts'),
        'temp_dir' => sys_get_temp_dir(),
        'chroot' => base_path(),
        'isRemoteEnabled' => true,
        'isPhpEnabled' => true,
        'isFontSubsettingEnabled' => false,
        'allowed_protocols' => array(
            'file://' => array('allow' => true),
            'http://' => array('allow' => true),
            'https://' => array('allow' => true),
        ),
    ),
    'font_family' => 'Arabic Typesetting',
    'default_font' => 'Arabic Typesetting',
    'dpi' => 96,
);
