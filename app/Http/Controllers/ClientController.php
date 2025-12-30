<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        // Clientes são os usuários com role='loja'
        // Este menu pode ser removido ou redirecionado para gestão de usuários
        return view('clients.index');
    }
}
