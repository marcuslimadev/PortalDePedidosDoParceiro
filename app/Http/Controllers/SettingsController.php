<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('settings.index');
    }
    
    public function update(Request $request)
    {
        // Implementação futura de configurações gerais do sistema
        return response()->json([
            'success' => true,
            'message' => 'Configurações atualizadas com sucesso!'
        ]);
    }
}
