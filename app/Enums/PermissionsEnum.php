<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    // PROCESSO SELETIVO
    case CONSULTAR_PROCESSO = 'consultar processo';
    case GERENCIAR_PROCESSO = 'gerenciar processo';

    // ANEXOS
    case CONSULTAR_ANEXO = 'consultar anexo';
    case GERENCIAR_ANEXO = 'gerenciar anexo';

    // INSCRIÇÕES
    case CONSULTAR_INSCRICAO = 'consultar inscrição';

    // VAGAS
    case GERENCIAR_VAGA = 'gerenciar vaga';
    case CONSULTAR_VAGA = 'consultar vaga';

    // ETAPA RECURSO
    case GERENCIAR_ETAPA_RECURSO = 'gerenciar etapa de recurso';

    // RECURSO
    case CONSULTAR_RECURSO = 'consultar recurso';
    case AVALIAR_RECURSO = 'avaliar recurso';
    case ATRIBUIR_AVALIADOR = 'atribuir avaliador';

    // CANDIDATOS
    case CONSULTAR_CANDIDATO = 'consultar candidato';
}
