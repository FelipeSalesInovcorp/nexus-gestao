<?php

return [
    // plano free (pós-trial)
    'free' => [
        'proposals' => true,
        'orders' => true,
        'supplier_orders' => true,
        'supplier_invoices' => false,
        'calendar' => false,
        'logs' => false,
        'access_management' => false,
        'advanced_reports' => false,
    ],

    // trial: podes decidir se é igual ao pro durante 14 dias
    'trial' => [
        'proposals' => true,
        'orders' => true,
        'supplier_orders' => true,
        'supplier_invoices' => true,
        'calendar' => true,
        'logs' => true,
        'access_management' => true,
        'advanced_reports' => false,
    ],

    'pro' => [
        'proposals' => true,
        'orders' => true,
        'supplier_orders' => true,
        'supplier_invoices' => true,
        'calendar' => true,
        'logs' => true,
        'access_management' => true,
        'advanced_reports' => true,
    ],

    // enterprise: tudo
    'enterprise' => [
        '*' => true,
    ],
];
