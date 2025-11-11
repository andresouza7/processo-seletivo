<x-filament-panels::page>
    <style>
        @media print {
            body * {
                visibility: hidden;
                /* esconde tudo */
            }

            #application-printable,
            #application-printable * {
                visibility: visible;
                /* mostra apenas o container */
            }

            #application-printable {
                position: absolute;
                left: 0;
                top: 50px;
                width: 100%;
            }
        }
    </style>

    <div id="application-printable"
        class="max-w-5xl mx-auto bg-white dark:bg-gray-900 shadow rounded-xl p-8 print:p-0 print:shadow-none print:bg-white">
        {{-- Header --}}
        {{-- <div class="mb-8 text-center border-b border-gray-300 dark:border-gray-700 pb-4">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-1">Comprovante de Inscrição</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Use o botão imprimir acima para gerar uma versão em PDF ou papel.</p>
        </div> --}}

        {{-- Header em uma linha --}}
        <div
            class="mb-8 flex items-center justify-between border-b border-gray-300 dark:border-gray-700 pb-3 print:pb-4">

            {{-- Logo + Nome da Instituição --}}
            <img src="{{ asset('img/logo.png') }}" alt="UEAP Logo" class="h-16 w-auto">

            {{-- Título e descrição --}}
            <div class="text-right">
                <h1 class="text-xl md:text-2xl font-extrabold text-gray-900 dark:text-gray-100 mb-1">
                    Comprovante de Inscrição
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Use o botão imprimir acima para gerar uma versão em PDF ou papel.
                </p>
            </div>
        </div>

        <div class="space-y-8 print:space-y-4">
            {{-- Dados pessoais --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Dados Pessoais</h2>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div><strong>Nome:</strong> {{ $record->candidate->social_name ?? $record->candidate->name }}</div>
                    <div><strong>CPF:</strong> {{ $record->candidate->cpf }}</div>
                    <div><strong>RG:</strong> {{ $record->candidate->rg }}</div>
                    <div><strong>Data de Nascimento:</strong>
                        {{ \Carbon\Carbon::parse($record->candidate->birth_date)->format('d/m/Y') }}</div>
                </div>
            </div>

            {{-- Dados da inscrição --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Dados da Inscrição</h2>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div><strong>Inscrição:</strong> {{ $record->code }}</div>
                    <div><strong>Processo Seletivo:</strong> {{ $record->process->title }}</div>
                    <div><strong>Vaga:</strong> {{ $record->position->description }}</div>
                    <div><strong>Tipo de Vaga:</strong> {{ $record->quota?->description }}</div>
                </div>
            </div>

            {{-- Atendimento Especial --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Atendimento Especial</h2>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <strong>Atendimento Especial:</strong>
                        @if ($record->requires_assistance)
                            <span
                                class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">
                                Solicitado
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800">
                                Não solicitado
                            </span>
                        @endif
                    </div>

                    @if ($record->requires_assistance)
                        <div><strong>Qual Atendimento:</strong> {{ $record->assistance_details }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-12 text-center text-xs text-gray-500 print:hidden">
            Universidade do Estado do Amapá — Sistema de Processo Seletivo
        </div>
    </div>
</x-filament-panels::page>
