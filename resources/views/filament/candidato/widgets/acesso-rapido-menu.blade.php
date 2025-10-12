<x-filament-widgets::widget>
    <div class="space-y-6">
        @php
            $user = auth('candidato')->user();

            $menus = [
                [
                    'url' => route('filament.candidato.resources.inscricoes.index'),
                    'icon' => 'heroicon-o-document-text',
                    'label' => 'Minhas Inscrições',
                ],
                [
                    'url' => route('filament.candidato.resources.inscricoes.create'),
                    'icon' => 'heroicon-o-plus',
                    'label' => 'Nova Inscrição',
                ],
                [
                    'url' => route('filament.candidato.pages.recurso'),
                    'icon' => 'heroicon-o-chat-bubble-left-right',
                    'label' => 'Recursos',
                ],
                [
                    'url' => route('filament.candidato.pages.meus-dados'),
                    'icon' => 'heroicon-o-user',
                    'label' => 'Meus Dados',
                ],
                [
                    'url' => route('filament.candidato.auth.profile'),
                    'icon' => 'heroicon-o-lock-closed',
                    'label' => 'Alterar Senha',
                ],
            ];
        @endphp

        {{-- Welcome --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-4 relative overflow-hidden">
            <div class="text-sm text-gray-600 dark:text-gray-300 mb-1">
                Bem-vindo(a),
            </div>
            <div class="text-base font-semibold text-gray-800 dark:text-white">
                {{ $user->name }}
            </div>
        </div>

        <div>
            {{-- Section Title --}}
            <div class="mb-1">
                <h2 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-home class="w-5 h-5 text-green-600" />
                    Minha Área
                </h2>
            </div>

            {{-- Navigation Menu --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0">
                    {{-- v4 style would be the same here --}}
                    <div class="bg-green-600 h-1 w-full rounded-t-md"></div>
                </div>

                <div class="px-4 py-8 sm:py-4">
                    <div class="grid grid-cols-4 sm:flex sm:flex-wrap sm:justify-start gap-4 justify-items-center">
                        @foreach ($menus as $menu)
                            <a href="{{ $menu['url'] }}"
                               class="flex flex-col items-center w-full sm:w-16 text-center transition hover:scale-105">
                                <div
                                    class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center shadow-sm border border-gray-200 dark:border-gray-700">
                                    <x-dynamic-component :component="$menu['icon']"
                                        class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-200" />
                                </div>
                                <span
                                    class="mt-1 text-[10px] sm:text-[11px] font-semibold text-gray-700 dark:text-gray-300 leading-tight">
                                    {{ $menu['label'] }}
                                </span>
                            </a>
                        @endforeach

                        {{-- Logout --}}
                        <form method="POST" action="{{ route('filament.candidato.auth.logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex flex-col items-center w-full sm:w-16 text-center transition hover:scale-105">
                                <div
                                    class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center shadow-sm border border-gray-200 dark:border-gray-700">
                                    <x-heroicon-o-arrow-left-on-rectangle
                                        class="w-5 h-5 sm:w-6 sm:h-6 text-red-700 dark:text-red-200" />
                                </div>
                                <span
                                    class="mt-1 text-[10px] sm:text-[11px] font-semibold text-gray-700 dark:text-gray-300 leading-tight">
                                    Sair
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
