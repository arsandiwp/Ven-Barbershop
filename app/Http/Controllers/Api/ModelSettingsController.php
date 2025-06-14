<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModelSetting;
use Illuminate\Http\Request;

class ModelSettingsController extends Controller
{
    /**
     * Mendapatkan semua pengaturan model
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'settings' => ModelSetting::all()
        ]);
    }

    /**
     * Menyimpan pengaturan model baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model_id' => 'required|string|max:255|unique:model_settings',
        ]);

        $setting = ModelSetting::create($validated);

        return response()->json([
            'success' => true,
            'setting' => $setting
        ], 201);
    }

    /**
     * Mendapatkan pengaturan model tertentu
     */
    public function show($id)
    {
        $setting = ModelSetting::findOrFail($id);

        return response()->json([
            'success' => true,
            'setting' => $setting
        ]);
    }

    /**
     * Memperbarui pengaturan model
     */
    public function update(Request $request, $id)
    {
        $setting = ModelSetting::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'model_id' => 'string|max:255|unique:model_settings,model_id,' . $id,
            'expires_at' => 'nullable|integer|min:1'
        ]);

        $setting->update($validated);

        return response()->json([
            'success' => true,
            'setting' => $setting
        ]);
    }

    /**
     * Menghapus pengaturan model
     */
    public function destroy($id)
    {
        $setting = ModelSetting::findOrFail($id);
        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan model berhasil dihapus'
        ]);
    }
}