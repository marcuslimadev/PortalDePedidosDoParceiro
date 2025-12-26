<?php

use App\Models\User;
use App\Http\Middleware\CheckRole;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('CheckRole allows access for correct role', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $middleware = new CheckRole();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $admin);

    $response = $middleware->handle($request, fn() => response('OK'), 'admin');

    expect($response->getContent())->toBe('OK');
});

test('CheckRole blocks access for incorrect role', function () {
    $loja = User::factory()->create(['role' => 'loja']);
    $middleware = new CheckRole();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $loja);

    expect(fn() => $middleware->handle($request, fn() => response('OK'), 'admin'))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

test('CheckRole allows multiple roles', function () {
    $operador = User::factory()->create(['role' => 'operador']);
    $middleware = new CheckRole();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $operador);

    $response = $middleware->handle($request, fn() => response('OK'), 'admin', 'operador');

    expect($response->getContent())->toBe('OK');
});

test('CheckRole redirects unauthenticated users to login', function () {
    $middleware = new CheckRole();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => null);

    $response = $middleware->handle($request, fn() => response('OK'), 'admin');

    expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
});

test('CheckRole works with admin role', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $middleware = new CheckRole();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $admin);

    $response = $middleware->handle($request, fn() => response('OK'), 'admin');

    expect($response->getContent())->toBe('OK');
});

test('CheckRole works with operador role', function () {
    $operador = User::factory()->create(['role' => 'operador']);
    $middleware = new CheckRole();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $operador);

    $response = $middleware->handle($request, fn() => response('OK'), 'operador');

    expect($response->getContent())->toBe('OK');
});

test('CheckRole works with loja role', function () {
    $loja = User::factory()->create(['role' => 'loja']);
    $middleware = new CheckRole();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $loja);

    $response = $middleware->handle($request, fn() => response('OK'), 'loja');

    expect($response->getContent())->toBe('OK');
});

test('CheckRole blocks admin from loja-only routes', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $middleware = new CheckRole();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $admin);

    expect(fn() => $middleware->handle($request, fn() => response('OK'), 'loja'))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

test('CheckRole blocks loja from admin-only routes', function () {
    $loja = User::factory()->create(['role' => 'loja']);
    $middleware = new CheckRole();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $loja);

    expect(fn() => $middleware->handle($request, fn() => response('OK'), 'admin'))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});
