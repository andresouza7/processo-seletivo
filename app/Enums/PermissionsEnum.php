<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    // PROCESSO SELETIVO = criar e editar processo, administrar vagas, anexos e etapas de recurso
    case GERENCIAR_PROCESSO = 'gerenciar processo';

    // RECURSO
    case AVALIAR_RECURSO = 'avaliar recurso';
    case ATRIBUIR_AVALIADOR = 'atribuir avaliador';

    // CANDIDATOS
    case CONSULTAR_CANDIDATO = 'consultar candidato';

    // INSCRIÇÕES
    case CONSULTAR_INSCRICAO = 'consultar inscrição';
}
