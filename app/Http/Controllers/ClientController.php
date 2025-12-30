<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        // Por enquanto, retorna view vazia - será implementado com modelo Client futuramente
        return view('clients.index', [
            'clients' => []
        ]);
    }
}
