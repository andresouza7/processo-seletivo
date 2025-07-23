<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}
        {{-- <br />
        <x-filament::button type="submit">
            Salvar
        </x-filament::button> --}}
    </form>
</x-filament-panels::page>
