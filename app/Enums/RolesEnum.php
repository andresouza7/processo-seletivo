<?php

namespace App\Enums;

enum RolesEnum: string
{
    case ADMIN = 'admin';
    case AVALIADOR = 'avaliador';
    case DIPS = 'dips';
    case ASCOM = 'ascom';
    case PROGRAD = 'prograd';
}
