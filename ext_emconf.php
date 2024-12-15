<?php
$EM_CONF[$_EXTKEY] = [
    'title'        => 'Changelog Info Module',
    'description'  => 'Info module to show parsed CHANGELOG.md content',
    'category'     => 'plugin',
    'author'       => 'Stefan Berger, MOGIC GmbH',
    'author_email' => 'berger@mogic.com',
    'state'        => 'beta',
    'clearCacheOnLoad' => 0,
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
