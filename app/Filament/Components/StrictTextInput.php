<?php

namespace App\Filament\Components;

use Filament\Forms\Components\TextInput;

class StrictTextInput extends TextInput
{
    public static function make(?string $name = null): static
    {
        $instance = parent::make($name);

        // Default validation: block emojis and unusual symbols
        $instance->rule('regex:/^[\p{L}\p{N}\p{P}\p{Zs}]+$/u')
                 ->validationMessages([
                     'regex' => 'Caracteres especiais não permitidos.',
                 ])
                 ->maxLength(60);

        return $instance;
    }
}
