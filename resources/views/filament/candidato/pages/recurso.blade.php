<x-filament-panels::page>
    <div class="space-y-4">

        <div class="bg-white rounded-xl shadow-sm mb-6">
            {{-- Cabeçalho: maior altura --}}
            <div class="flex items-center gap-4 bg-white rounded-t-xl px-5 py-6 border-b border-gray-100">
                <div class="flex-shrink-0 bg-amber-200 p-3 rounded-xl">
                    <x-heroicon-o-information-circle class="w-8 h-8 text-amber-800" />
                </div>

                <div class="flex flex-col">
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ $stage->process->title }}
                    </h2>
                    <p class="text-sm text-gray-700 font-medium mt-1">
                        {{ $stage->title }}
                    </p>
                </div>
            </div>

            {{-- Corpo: padding reduzido acima da linha, ocupação menor --}}
            <div class="px-6 py-4 text-sm text-gray-800 leading-relaxed space-y-2">
                <p class="mb-1">
                    Envie seu <strong class="font-semibold text-gray-900">recurso</strong> revisando atentamente todas
                    as informações e anexos.
                </p>

                @php
                    $start = Carbon\Carbon::parse($stage->submission_start_date)->format('d/m/Y');
                    $end = Carbon\Carbon::parse($stage->submission_end_date)->format('d/m/Y');
                @endphp

                <p class="text-xs italic text-gray-600 mt-1">
                    Prazo de envio: de <strong class="text-gray-800">{{ $start }}</strong> até <strong
                        class="text-gray-800">{{ $end }}</strong>.
                </p>
            </div>
        </div>


        @if (count($this->options))
            {{ $this->form }}
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md text-yellow-700 text-sm shadow-sm">
                Você não possui inscrições para solicitar recurso nesta etapa.
            </div>
        @endif

        @php
            $resultLabels = [
                'D' => ['label' => 'Deferido', 'color' => 'success'],
                'I' => ['label' => 'Indeferido', 'color' => 'danger'],
                'P' => ['label' => 'Deferido Parcialmente', 'color' => 'warning'],
            ];
        @endphp

        {{-- Seção de recursos enviados --}}
        @if (count($appeals))
            <div class="bg-white border-l-4 border-gray-400 p-4 rounded-md text-gray-700 text-sm shadow-sm">
                Consulte abaixo os seus recursos em andamento
            </div>
        @endif

        @foreach ($appeals as $appeal)
            @php
                $result = $appeal->hasResult()
                    ? $resultLabels[$appeal->result]
                    : ['label' => 'Em análise', 'color' => 'info'];
            @endphp

            <div class="bg-white shadow-xs border border-gray-200 rounded-xl p-6">
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-300 pb-3 mb-4">
                    <span class="text-md font-bold text-primary-600">
                        {{-- Protocolo #{{ $appeal->id }} --}}
                        Inscrição #{{ $appeal->application->code }}
                    </span>
                    <span class="text-xs text-gray-500">
                        Enviado em {{ $appeal->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>

                {{-- Etapa + Processo + Resultado --}}
                <div class="flex items-center justify-between text-sm text-gray-700 mb-3">
                    <div class="space-y-1">
                        <p><span class="font-semibold">Processo Seletivo:</span> {{ $appeal->process->title }}</p>
                        <p><span class="font-semibold">Etapa:</span> {{ $appeal->appeal_stage->description }}</p>
                    </div>

                    <x-filament::badge :color="$result['color']">
                        {{ $result['label'] }}
                    </x-filament::badge>
                </div>

                {{-- Texto do Recurso --}}
                <div class="mt-4 text-sm">
                    <p class="font-semibold text-gray-800">Texto do Recurso:</p>
                    <div class="mt-1 text-gray-600 bg-gray-50 rounded-lg p-3">
                        {{ $appeal->text }}
                    </div>
                </div>

                {{-- Resposta da Banca --}}
                @if ($appeal->hasResult())
                    <div class="mt-4 text-sm">
                        <p class="font-semibold text-gray-800">Resposta da Banca:</p>
                        <div class="mt-1 text-gray-600 bg-gray-50 rounded-lg p-3">
                            {{ $appeal->response }}
                        </div>

                        @if ($appeal->hasMedia('anexo_avaliador'))
                            @php
                                $link = $appeal->getFirstMediaUrl('anexo_avaliador');
                            @endphp
                            @if ($link)
                                <p>
                                    <a href="{{ $link }}" target="_blank"
                                        class="text-primary-600 hover:underline">
                                        Visualizar Anexo
                                    </a>
                                </p>
                            @endif
                        @endif

                    </div>
                @endif

            </div>
        @endforeach
    </div>

</x-filament-panels::page>
