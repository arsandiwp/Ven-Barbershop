<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        $phone = isset($row['phone']) ? $this->processPhoneNumber($row['phone']) : null;



        return new User([
            'name' => $row['full_name'],
            'email' => $row['email'],
            'phone' => $phone,
            'address' => $row['address'] ?? null,
            'photo' => $this->extractPhotoUrl($row['photo'] ?? null),
            'status' => $row['status'] ?? 'Active',
            'role_id' => 2,


            'password' => Hash::make('123456'),            
        ]);
    }

    /**
     * Extract the actual URL from hyperlink text
     *
     * @param string|null $photoText
     * @return string|null
     */
    private function extractPhotoUrl($photoText)
    {
        if (!$photoText || $photoText == '-') {
            return null;
        }



        if (preg_match('/=HYPERLINK\("([^"]+)".+\)/', $photoText, $matches)) {
            return $matches[1];
        }

        return $photoText;
    }

    /**
     * Format nomor telepon ke format standar penyimpanan
     *
     * @param string $phone
     * @return string
     */
    private function processPhoneNumber($phone)
    {
        if (!$phone || $phone == '-') {
            return null;
        }


        $phone = preg_replace('/[^0-9]/', '', $phone);


        if (preg_match('/^(\+)?62/', $phone, $matches)) {
            $phone = '62' . substr($phone, strlen($matches[0]));
        }

        return $phone;
    }

    /**
     * Validasi data impor
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->getEmailId()),
            ],
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'photo' => 'nullable|string',
            'status' => 'nullable|string|in:Active,Pending',
        ];
    }

    /**
     * Custom messages untuk validasi
     *
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'status.in' => 'Status hanya boleh Active atau Pending',
        ];
    }

    /**
     * Mendapatkan ID user berdasarkan email (untuk validasi unique)
     *
     * @param string $email
     * @return int|null
     */
    private function getEmailId($email = null)
    {
        if (!$email) {
            return null;
        }

        $user = User::where('email', $email)->first();
        return $user ? $user->id : null;
    }
}
