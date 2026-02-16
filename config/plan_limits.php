<?php

return [
    // Ajusta estes valores como quiseres.
    // O briefing pede limite de utilizadores por plano.
    'plans' => [
        'trial' => [
            'max_users' => 2,
        ],
        'pro' => [
            'max_users' => 10,
        ],
        'enterprise' => [
            'max_users' => 999999,
        ],
    ],

    // Aviso do trial (dias antes)
    'trial_warn_days' => 7,

    // Deadline de entrega (atualizado por ti)
    'delivery_deadline' => '2026-02-18',
    'delivery_warn_days' => 5,
];
