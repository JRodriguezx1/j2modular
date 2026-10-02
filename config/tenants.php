<?php

return [

    'cliente' => [
        'database' => 'contapos',
        'modules' => ['POS', 'Restaurant'],
    ],

    'cliente1' => [
        'database' => 'j2a1',
        'modules' => ['Pos',],
    ],

    'cliente2' => [
        'database' => 'j2a2',
        'modules' => ['Pos', /* 'Restaurant', // todavia no existe */],
    ],

];