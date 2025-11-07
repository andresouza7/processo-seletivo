<?php

namespace App\Filament\Components;

use App\Models\FormField;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class DynamicFormFields extends Field
{
    protected string $view = 'filament.dynamic-form-fields';

    public mixed $processId = null;

    public static function make(?string $name = null): static
    {
        return parent::make($name);
    }

    public function processId(int|string|Closure|null $id): static
    {
        $this->processId = $id;
        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);

        // Aqui está o segredo: gerar os campos dinamicamente
        $this->childComponents(fn () => $this->resolveDynamicFields());
    }

    protected function resolveDynamicFields(): array
    {
        $processId = $this->evaluate($this->processId);

        if (! $processId) {
            return [];
        }

        $fields = FormField::where('process_id', $processId)
            ->orderBy('order')
            ->get();

        return $fields->map(fn ($field) => $this->buildField($field))->toArray();
    }

    protected function buildField(FormField $field)
{
    $fieldPath = "form_data.{$field->name}"; // <-- ESSENCIAL

    return match ($field->type) {
        'text' => TextInput::make($fieldPath)
            ->label($field->label),

        'textarea' => Textarea::make($fieldPath)
            ->label($field->label),

        'number' => TextInput::make($fieldPath)
            ->numeric()
            ->label($field->label),

        'email' => TextInput::make($fieldPath)
            ->email()
            ->label($field->label),

        'date' => DatePicker::make($fieldPath)
            ->label($field->label),

        'select' => Select::make($fieldPath)
            ->label($field->label)
            ->options($field->options ?? []),

        'checkbox' => Checkbox::make($fieldPath)
            ->label($field->label),

        'file' => FileUpload::make($fieldPath)
            ->label($field->label)
            ->directory("applications/{$field->process_id}")
            ->preserveFilenames(),

        default => TextInput::make($fieldPath)
            ->label($field->label),
    };
}

}
