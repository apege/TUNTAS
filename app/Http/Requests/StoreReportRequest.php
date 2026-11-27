<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kategori_id' => ['required', 'uuid', 'exists:kategoris,id'],
            'judul' => ['required', 'string', 'min:5', 'max:150'],
            'lokasi_detail' => ['required', 'string', 'min:5', 'max:255'],
            'blok' => ['nullable', 'string', 'max:50'],
            'dampak' => ['required', 'string', 'in:rendah,sedang,tinggi'],
            'urgensi' => ['required', 'string', 'in:rendah,sedang,tinggi,mendesak'],
            'uraian' => ['required', 'string', 'min:20', 'max:1500'],
            'foto' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'kategori_id' => 'Kategori masalah',
            'judul' => 'Judul laporan',
            'lokasi_detail' => 'Lokasi masalah',
            'blok' => 'Area lingkungan / blok',
            'dampak' => 'Dampak bagi warga',
            'urgensi' => 'Urgensi penanganan',
            'uraian' => 'Uraian masalah',
            'foto' => 'Foto pendukung',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Silakan pilih salah satu kategori masalah di atas.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'judul.required' => 'Judul laporan wajib diisi.',
            'judul.min' => 'Judul laporan minimal :min karakter.',
            'lokasi_detail.required' => 'Lokasi masalah atau patokan wajib diisi.',
            'lokasi_detail.min' => 'Lokasi masalah minimal :min karakter.',
            'uraian.required' => 'Uraian masalah wajib diisi.',
            'uraian.min' => 'Uraian masalah minimal :min karakter agar kronologi masalah cukup jelas.',
            'uraian.max' => 'Uraian masalah maksimal :max karakter.',
            'foto.image' => 'File foto pendukung harus berupa gambar (JPG, PNG, atau WebP).',
            'foto.max' => 'Ukuran foto pendukung tidak boleh melebihi 5 MB.',
        ];
    }
}
