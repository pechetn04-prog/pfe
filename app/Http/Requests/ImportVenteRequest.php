<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportVenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'excel_file.required' => 'Veuillez sélectionner un fichier Excel.',
            'excel_file.mimes'    => 'Le fichier doit être au format Excel (.xlsx, .xls) ou texte/CSV (.csv, .txt).',
            'excel_file.max'      => 'La taille du fichier ne doit pas dépasser 4 Mo.',
        ];
    }
}
