<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testando Login ===\n\n";

// Buscar usuário
$user = App\Models\User::where('email', 'admin@portalpedidos.com')->first();

if ($user) {
    echo "✓ Usuário encontrado\n";
    echo "  Nome: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: {$user->role}\n";
    echo "  Ativo: " . ($user->ativo ? 'Sim' : 'Não') . "\n\n";
    
    // Testar senha
    $passwordCheck = Hash::check('admin123', $user->password);
    echo "✓ Teste de senha 'admin123': " . ($passwordCheck ? "VÁLIDA ✓" : "INVÁLIDA ✗") . "\n";
} else {
    echo "✗ Usuário NÃO encontrado\n";
}

echo "\n=== Total de usuários: " . App\Models\User::count() . " ===\n";
