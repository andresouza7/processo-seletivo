@props(['field'])

@php
    $type = $field->type;
    $name = $field->name;
    $label = $field->label;
    $required = $field->required ?? false;
    $options = $field->options ?? [];
@endphp

<div class="mb-4">
    <label class="block font-semibold mb-1" for="{{ $name }}">
        {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
    </label>

    @switch($type)
        @case('text')
            <input type="text" id="{{ $name }}" {{ $attributes }}
                class="w-full border rounded-md p-2 focus:ring focus:ring-blue-300"
                @if($required) required @endif>
            @break

        @case('textarea')
            <textarea id="{{ $name }}" {{ $attributes }}
                class="w-full border rounded-md p-2 focus:ring focus:ring-blue-300"
                rows="4"
                @if($required) required @endif></textarea>
            @break

        @case('number')
            <input type="number" id="{{ $name }}" {{ $attributes }}
                class="w-full border rounded-md p-2 focus:ring focus:ring-blue-300"
                @if($required) required @endif>
            @break

        @case('date')
            <input type="date" id="{{ $name }}" {{ $attributes }}
                class="w-full border rounded-md p-2 focus:ring focus:ring-blue-300"
                @if($required) required @endif>
            @break

        @case('email')
            <input type="email" id="{{ $name }}" {{ $attributes }}
                class="w-full border rounded-md p-2 focus:ring focus:ring-blue-300"
                @if($required) required @endif>
            @break

        @case('select')
            <select id="{{ $name }}" {{ $attributes }}
                class="w-full border rounded-md p-2 focus:ring focus:ring-blue-300"
                @if($required) required @endif>
                <option value="">Selecione...</option>
                @foreach($options as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
            @break

        @case('checkbox')
            <div class="flex items-center gap-2">
                <input type="checkbox" id="{{ $name }}" value="1" {{ $attributes }}
                    class="border rounded focus:ring focus:ring-blue-300">
                <label for="{{ $name }}">{{ $label }}</label>
            </div>
            @break

        @case('file')
            <input type="file" id="{{ $name }}" {{ $attributes }}
                class="w-full border rounded-md p-2 focus:ring focus:ring-blue-300"
                @if($required) required @endif>
            @break

        @default
            <input type="text" id="{{ $name }}" {{ $attributes }}
                class="w-full border rounded-md p-2 focus:ring focus:ring-blue-300"
                @if($required) required @endif>
    @endswitch

    @error("formData.$name")
        <span class="text-sm text-red-500">{{ $message }}</span>
    @enderror
</div>
