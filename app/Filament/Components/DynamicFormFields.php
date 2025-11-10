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

    public function processId(int|string|Closure|null $id): static
    {
        $this->processId = $id;
        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Gerar os campos dinamicamente
        $this->childComponents(fn() => $this->resolveDynamicFields());
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

        return $fields->map(fn($field) => $this->buildField($field))->toArray();
    }

    protected function buildField(FormField $field)
    {
        $fieldPath = "form_data.{$field->name}"; // <-- ESSENCIAL

        return match ($field->type) {
            'text' => TextInput::make($fieldPath)
                ->required($field->required)
                ->label($field->label),

            'textarea' => Textarea::make($fieldPath)
                ->required($field->required)
                ->label($field->label),

            'number' => TextInput::make($fieldPath)
                ->required($field->required)
                ->numeric()
                ->label($field->label),

            'email' => TextInput::make($fieldPath)
                ->required($field->required)
                ->email()
                ->label($field->label),

            'date' => DatePicker::make($fieldPath)
                ->required($field->required)
                ->label($field->label),
                
            'select' => Select::make($fieldPath)
                ->required($field->required)
                ->label($field->label)
                ->options(function () use ($field) {
                    return collect($field->options ?? [])
                        ->mapWithKeys(fn($opt) => [
                            $opt['value'] => $opt['label'],
                        ])
                        ->toArray();
                }),

            'checkbox' => Checkbox::make($fieldPath)
                ->required($field->required)
                ->label($field->label),

            'file' => AttachmentUpload::make($fieldPath)
                ->required($field->required)
                ->helperText($field->helper_text)
                ->label($field->label)
                ->mediaName($field->label)
                ->disk('local'),

            default => TextInput::make($fieldPath)
                ->required($field->required)
                ->label($field->label),
        };
    }
}
