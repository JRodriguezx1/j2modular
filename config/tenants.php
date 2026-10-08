<?php

return [

    'cliente' => [
        'database' => 'contapos',
        'modules' => ['POS', 'Cash', 'Restaurant'],
    ],

    'cliente1' => [
        'database' => 'j2a1',
        'modules' => ['POS', 'Cash'],
    ],

    'cliente2' => [
        'database' => 'j2a2',
        'modules' => ['POS', 'Cash', /* 'Restaurant', // todavia no existe */],
    ],

];
