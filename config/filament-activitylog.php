<?php

return [
    'resources' => [
        'label'                  => 'Logs de Atividade',
        'plural_label'           => 'Logs de Atividade',
        'navigation_item'        => true,
        'navigation_group'       => 'Administrador',
        'navigation_icon'        => 'heroicon-o-shield-check',
        'navigation_sort'        => null,
        'default_sort_column'    => 'id',
        'default_sort_direction' => 'desc',
        'navigation_count_badge' => false,
        'resource'               => \Rmsramos\Activitylog\Resources\ActivitylogResource::class,
    ],
    'datetime_format' => 'd/m/Y H:i:s',
];
