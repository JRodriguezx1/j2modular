<?php

return [

    'cliente' => [
        'database' => 'contapos',
        'modules' => ['POS', 'Restaurant'],
    ],

    'cliente1' => [
        'database' => 'j2a1',
        'modules' => ['POS',],
    ],

    'cliente2' => [
        'database' => 'j2a2',
        'modules' => ['POS', /* 'Restaurant', // todavia no existe */],
    ],

];