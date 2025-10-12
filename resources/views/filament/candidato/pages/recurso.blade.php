<x-filament-panels::page>
    {{ $this->form }}

    @if ($appeal)
        <x-filament::section>
            <x-slot name="description">
                Acompanhe o andamento do seu recurso
            </x-slot>

            <div class="space-y-4 text-sm">
                {{-- Justificativa --}}
                <div>
                    <span class="font-semibold text-gray-800">Justificativa:</span>
                    <p class="mt-1 text-gray-700">{{ $appeal->text }}</p>
                </div>

                {{-- Resposta da banca --}}
                @if ($appeal->response)
                    <div class="space-y-1">
                        <span class="font-semibold text-gray-800">Resposta da banca:</span>
                        <p class="mt-1 text-gray-700">{{ $appeal->response }}</p>

                        @if ($appeal->hasMedia('anexo_avaliador'))
                            @php
                                $link = $appeal->getFirstMediaUrl('anexo_avaliador');
                            @endphp
                            @if ($link)
                                <p>
                                    <a href="{{ $link }}" target="_blank" class="text-primary-600 hover:underline">
                                        Visualizar Anexo
                                    </a>
                                </p>
                            @endif
                        @endif
                    </div>
                @endif

                {{-- Status / Resultado --}}
                @php
                    $statusColors = [
                        'D' => 'success',
                        'DP' => 'warning',
                        'I' => 'danger',
                    ];

                    $statusLabels = [
                        'D' => 'Deferido',
                        'DP' => 'Deferido parcialmente',
                        'I' => 'Indeferido',
                    ];

                    $badgeColor = $appeal->result ? $statusColors[$appeal->result] ?? 'gray' : 'info';
                    $badgeLabel = $appeal->result ? $statusLabels[$appeal->result] ?? $appeal->result : 'Em análise';
                @endphp

                <div class="flex items-center gap-2">
                    <x-filament::badge size="sm" :color="$badgeColor">
                        {{ $badgeLabel }}
                    </x-filament::badge>
                    <span class="text-xs text-gray-500">
                        Enviado em {{ $appeal->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
