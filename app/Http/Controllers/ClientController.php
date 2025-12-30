<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::latest()->paginate(15);
        
        return view('clients.index', compact('clients'));
    }
    
    public function create(): View
    {
        return view('clients.create');
    }
    
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'phone' => 'nullable|string|max:20',
            'cpf_cnpj' => 'nullable|string|max:18|unique:clients,cpf_cnpj',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'status' => 'required|in:ativo,inativo',
        ]);
        
        Client::create($validated);
        
        return redirect()->route('clients.index')
            ->with('success', 'Cliente criado com sucesso!');
    }
    
    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }
    
    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'phone' => 'nullable|string|max:20',
            'cpf_cnpj' => 'nullable|string|max:18|unique:clients,cpf_cnpj,' . $client->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'status' => 'required|in:ativo,inativo',
        ]);
        
        $client->update($validated);
        
        return redirect()->route('clients.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }
    
    public function destroy(Client $client): JsonResponse
    {
        try {
            $client->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir cliente: ' . $e->getMessage()
            ], 500);
        }
    }
}
