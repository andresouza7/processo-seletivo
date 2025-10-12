<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Item Etapas Recurso --}}
        <a href="{{ route('filament.gps.resources.processos.etapas_recurso', ['record' => $record]) }}"
           class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-md shadow-sm border-l-4 border-blue-600 hover:bg-blue-50 dark:hover:bg-gray-700 transition">
            <x-heroicon-o-rectangle-stack class="w-6 h-6 text-blue-600 mr-3" />
            <span class="font-semibold text-gray-800 dark:text-gray-100">Etapas</span>
        </a>

        {{-- Item Avaliadores --}}
        <a href="{{ route('filament.gps.resources.processos.evaluators', ['record' => $record]) }}"
           class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-md shadow-sm border-l-4 border-green-600 hover:bg-green-50 dark:hover:bg-gray-700 transition">
            <x-heroicon-o-user-group class="w-6 h-6 text-green-600 mr-3" />
            <span class="font-semibold text-gray-800 dark:text-gray-100">Avaliadores</span>
        </a>
    </div>
</x-filament-panels::page>
