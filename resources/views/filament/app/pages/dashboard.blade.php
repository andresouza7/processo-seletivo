<x-filament-panels::page>
    {{-- Banner Principal --}}
    <div class="relative h-64 rounded-lg overflow-hidden shadow-lg dashboard-banner">
        <img src="{{ asset('img/banner-home.jpg') }}" alt="Banner Concursos"
            class="w-full h-full object-cover object-center transition-transform duration-500 hover:scale-105" />

        <div
            class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/60 flex flex-col justify-center px-8 py-6">
            <h1 class="text-3xl dashboard-banner-title font-bold text-white leading-tight drop-shadow-md">
                Universidade do Estado do Amapá
            </h1>
            <p class="mt-2 text-lg dashboard-banner-subtitle font-semibold text-gray-300 drop-shadow-md">
                Portal de Processo Seletivo
            </p>
        </div>

        <div class="absolute bottom-2 right-3 text-[8px] text-gray-300 select-none pointer-events-none">
            Foto: Floriano Lima
        </div>
    </div>


    <div>
        <div class="mb-2">
            <h2 class="text-large font-bold text-gray-800 dark:text-white leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Acesso Rápido
            </h2>

            {{-- <div class="mt-1 h-1 w-100 bg-green-600 rounded-full"></div> --}}
        </div>

        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl overflow-hidden relative">
            <!-- Top green border with narrower edges -->
            <div class="absolute top-0 left-0 right-0 flex justify-center">
                <div class="bg-green-600 h-1 w-[100%] rounded-t-md"></div>
            </div>
            @php
                $menus = [
                    [
                        'url' => route('filament.candidato.pages.dashboard'),
                        'img' => '/img/menu/area-candidato.jpg',
                        'label' => 'Área do Candidato',
                    ],
                    [
                        'url' => \App\Filament\App\Resources\ProcessoSeletivos\ProcessoSeletivoResource::getUrl('index', [
                            'status' => 'inscricoes_abertas',
                        ]),
                        'img' => '/img/menu/inscricoes-abertas.jpg',
                        'label' => 'Inscrições Abertas',
                    ],
                    [
                        'url' => \App\Filament\App\Resources\ProcessoSeletivos\ProcessoSeletivoResource::getUrl('index', [
                            'status' => 'em_andamento',
                        ]),
                        'img' => '/img/menu/editais-andamento.jpg',
                        'label' => 'Editais em Andamento',
                    ],
                    [
                        'url' => \App\Filament\App\Resources\ProcessoSeletivos\ProcessoSeletivoResource::getUrl('index', [
                            'status' => 'finalizados',
                        ]),
                        'img' => '/img/menu/editais-finalizados.jpg',
                        'label' => 'Editais Encerrados',
                    ],
                    ['url' => '#faq', 'img' => '/img/menu/faq.jpg', 'label' => 'FAQ'],
                    [
                        'url' => 'https://www.ueap.edu.br',
                        'img' => '/img/menu/site-ueap.png',
                        'label' => 'Site UEAP',
                        'target' => '_blank',
                    ],
                ];
            @endphp

            {{-- Itens do menu --}}
            <div class="p-4">
                <div class="flex flex-wrap gap-4 justify-start overflow-hidden dashboard-menu">
                    {{-- Itera sobre os menus e cria os links --}}
                    @foreach ($menus as $menu)
                        <a href="{{ $menu['url'] }}"
                            class="w-[72px] h-[72px] md:w-[90px] md:h-[90px] relative group flex-shrink-0 overflow-hidden img-overlay"
                            @if (isset($menu['target'])) target="{{ $menu['target'] }}" @endif>

                            {{-- Imagem redonda --}}
                            <div
                                class="w-[72px] h-[72px] md:w-[90px] md:h-[90px] rounded-full overflow-hidden border border-gray-300 dark:border-gray-700 shadow-sm">
                                <img src="{{ $menu['img'] }}" alt="{{ $menu['label'] }}"
                                    class="object-cover w-[100%] h-[100%] transition-transform duration-300 group-hover:scale-110" />
                            </div>

                            {{-- Texto sobre a imagem --}}
                            <div
                                class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center img-overlay">
                                <span class="text-white text-[12px] font-semibold text-center px-2 leading-tight">
                                    {{ $menu['label'] }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>


    <div x-data="{ tab: 'tab2' }">
        <div class="mb-2">
            <h2 class="text-large font-bold text-gray-800 dark:text-white leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                Fique por dentro
            </h2>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl overflow-hidden relative p-4">
            <!-- Top green border with narrower edges -->
            <div class="absolute top-0 left-0 right-0 flex justify-center">
                <div class="bg-yellow-400 h-1 w-[100%] rounded-t-md"></div>
            </div>
            {{-- Custom tab wrapper to remove border --}}
            <div class="border-b border-transparent mb-4">
                <ul class="flex flex-wrap gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    <li>
                        <button @click="tab = 'tab2'"
                            :class="tab === 'tab2' ?
                                'text-primary-600 dark:text-primary-400 border-b-2 border-primary-600 dark:border-primary-400' :
                                'border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-600'"
                            class="py-2 transition-colors duration-200">
                            {{-- <x-heroicon-m-bell class="w-4 h-4 inline-block mr-1" /> --}}
                            Novas Publicações
                        </button>
                    </li>
                    <li>
                        <button @click="tab = 'tab1'"
                            :class="tab === 'tab1' ?
                                'text-primary-600 dark:text-primary-400 border-b-2 border-primary-600 dark:border-primary-400' :
                                'border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-600'"
                            class="px-2 py-2 transition-colors duration-200">
                            {{-- <x-heroicon-m-document class="w-4 h-4 inline-block mr-1" /> --}}
                            Editais Recentes
                        </button>
                    </li>
                </ul>
            </div>

            <div>
                <div x-show="tab === 'tab1'">
                    @php

                        $processos = \App\Models\ProcessoSeletivo::emAndamento()
                            ->latest('data_criacao')
                            ->limit(10)
                            ->get();
                    @endphp

                    <div class="divide-y divide-gray-200">
                        @foreach ($processos as $processo)
                            <div class="py-4">
                                <div class="flex items-start gap-4 text-sm text-gray-700">
                                    <div class="w-20 text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($processo->data_criacao)->format('d/m/Y') }}
                                    </div>

                                    <div class="flex-1">
                                        <div class="text-xs font-medium text-gray-800">
                                            Edital nº {{ $processo->numero }}
                                        </div>

                                        <a href="{{ route('filament.app.resources.processo-seletivos.view', ['record' => $processo->idprocesso_seletivo]) }}"
                                            class="text-primary-600 text-sm font-medium hover:underline mt-1.5"
                                            title="{{ $processo->titulo }}">
                                            {{ \Illuminate\Support\Str::limit($processo->titulo, 100) }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    {{-- @livewire('public-dashboard-table-processos') --}}
                </div>

                <div x-show="tab === 'tab2'">
                    @php
                        $anexos = \App\Models\ProcessoSeletivoAnexo::latest('data_publicacao')->limit(10)->get();
                    @endphp

                    <div class="divide-y divide-gray-200">
                        @foreach ($anexos as $anexo)
                            <div class="py-4">
                                <div class="flex items-start gap-4">
                                    {{-- Coluna da Data --}}
                                    <div class="w-20 shrink-0">
                                        <span class="text-xs text-gray-500 block">
                                            {{ \Carbon\Carbon::parse($anexo->data_publicacao)->format('d/m/Y') }}
                                        </span>
                                    </div>

                                    {{-- Coluna do Conteúdo --}}
                                    <div class="flex-1 text-sm text-gray-700">
                                        <div class="text-xs font-medium text-gray-800">
                                            {{ $anexo->processo_seletivo->titulo }} – Edital nº
                                            {{ $anexo->processo_seletivo->numero }}
                                        </div>
                                        <a href="{{ $anexo->url_arquivo }}" target="_blank"
                                            class="text-primary-600 text-sm font-medium hover:underline mt-1.5">
                                            {{ \Illuminate\Support\Str::limit($anexo->descricao, 100) }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- @livewire('public-dashboard-table-anexos') --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Perguntas Frequentes --}}
    @php
        $resetPasswordUrl = route('filament.candidato.auth.password-reset.request');
        $resetEmailUrl = route('filament.app.pages.redefinir-email');
        $faqItems = [
            [
                'pergunta' => 'Como se inscrever em um processo seletivo?',
                'resposta' =>
                    'Para se inscrever, acesse a página do processo seletivo desejado, clique no botão "Inscrever-se" e siga as instruções. Você precisará preencher seus dados pessoais e, se necessário, efetuar o pagamento da taxa de inscrição.',
            ],
            [
                'pergunta' => 'Como acompanhar minha inscrição?',
                'resposta' =>
                    'Para acompanhar sua inscrição, acesse a área do candidato com seu login e senha. Lá você poderá verificar o status da sua inscrição, baixar o comprovante e consultar informações importantes.',
            ],
            [
                'pergunta' => 'Como redefinir minha senha?',
                'resposta' =>
                    'Esqueceu sua senha? <a class="text-blue-500 underline font-medium" href="' .
                    $resetPasswordUrl .
                    '">Clique aqui para redefinir</a>',
            ],

            [
                'pergunta' => 'Como redefinir meu email?',
                'resposta' =>
                    'Esqueceu email? <a class="text-blue-500 underline font-medium" href="' .
                    $resetEmailUrl .
                    '">Clique aqui para redefinir</a>. Ou entre em contato com a DIPS.',
            ],
        ];
    @endphp

    <div>
        <div class="mb-2" id="faq">
            {{-- Título da seção --}}
            <h2 class="text-large font-bold text-gray-800 dark:text-white leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                </svg>
                Perguntas Frequentes
            </h2>
        </div>
        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl overflow-hidden relative p-4">
            <!-- Top green border with narrower edges -->
            <div class="absolute top-0 left-0 right-0 flex justify-center">
                <div class="bg-yellow-400 h-1 w-[100%] rounded-t-md"></div>
            </div>

            <div class="space-y-3">
                @foreach ($faqItems as $faq)
                    <div x-data="{ open: false }" class="border border-gray-200 rounded-lg bg-white">
                        <button @click="open = !open"
                            class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 rounded-lg transition">
                            <span class="text-sm font-medium text-gray-800">
                                {{ $faq['pergunta'] }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="w-5 h-5 text-gray-400 transition-transform duration-300"
                                :class="{ 'rotate-180': open }">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="px-4 py-3 border-t">
                            <p class="text-sm text-gray-600">
                                {!! $faq['resposta'] !!}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    <style>
        @media only screen and (min-width: 600px) and (max-width: 1200px) {
            .dashboard-banner {
                height: 280px;
            }

            .dashboard-menu {
                /* justify-content: center; */
            }
        }

        @media only screen and (max-width: 600px) {
            .dashboard-banner {
                height: 200px;
            }

            .dashboard-banner-title {
                font-size: 1.1rem;
                line-height: 1.2;
            }

            .dashboard-banner-subtitle {
                font-size: 1rem;
            }

            .dashboard-menu span {
                font-size: 0.7rem;
            }
        }
    </style>

</x-filament-panels::page>
