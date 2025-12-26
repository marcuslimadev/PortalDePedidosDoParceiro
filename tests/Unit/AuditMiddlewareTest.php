<?php

use App\Models\User;
use App\Models\AuditLog;
use App\Http\Middleware\AuditMiddleware;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('AuditMiddleware logs successful requests', function () {
    $user = User::factory()->create();
    $middleware = new AuditMiddleware();

    $request = Request::create('/test/1', 'POST');
    $request->setUserResolver(fn() => $user);
    $request->setRouteResolver(function () {
        return new class {
            public function parameter($key) {
                return 1;
            }
        };
    });

    $middleware->handle(
        $request,
        fn() => response('OK', 200),
        'test.action',
        'TestResource'
    );

    expect(AuditLog::count())->toBe(1);
});

test('AuditMiddleware does not log failed requests', function () {
    $user = User::factory()->create();
    $middleware = new AuditMiddleware();

    $request = Request::create('/test', 'POST');
    $request->setUserResolver(fn() => $user);

    $middleware->handle(
        $request,
        fn() => response('Error', 500),
        'test.action',
        'TestResource'
    );

    expect(AuditLog::count())->toBe(0);
});

test('AuditMiddleware does not log unauthenticated requests', function () {
    $middleware = new AuditMiddleware();

    $request = Request::create('/test', 'POST');
    $request->setUserResolver(fn() => null);

    $middleware->handle(
        $request,
        fn() => response('OK', 200),
        'test.action',
        'TestResource'
    );

    expect(AuditLog::count())->toBe(0);
});

test('AuditMiddleware captures action and resource type', function () {
    $user = User::factory()->create();
    $middleware = new AuditMiddleware();

    $request = Request::create('/orders/1', 'PUT');
    $request->setUserResolver(fn() => $user);
    $request->setRouteResolver(function () {
        return new class {
            public function parameter($key) {
                return 1;
            }
        };
    });

    $middleware->handle(
        $request,
        fn() => response('OK', 200),
        'order.updated',
        'Order'
    );

    $log = AuditLog::first();

    expect($log)->not->toBeNull()
        ->and($log->action)->toBe('order.updated')
        ->and($log->resource_type)->toBe('Order');
});

test('AuditMiddleware logs request method and path', function () {
    $user = User::factory()->create();
    $middleware = new AuditMiddleware();

    $request = Request::create('/products/5', 'DELETE');
    $request->setUserResolver(fn() => $user);
    $request->setRouteResolver(function () {
        return new class {
            public function parameter($key) {
                return 5;
            }
        };
    });

    $middleware->handle(
        $request,
        fn() => response('OK', 200),
        'product.deleted',
        'Product'
    );

    $log = AuditLog::first();

    expect($log)->not->toBeNull()
        ->and($log->description)->toContain('DELETE')
        ->and($log->description)->toContain('products/5');
});

test('AuditMiddleware passes response through', function () {
    $user = User::factory()->create();
    $middleware = new AuditMiddleware();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $user);

    $response = $middleware->handle(
        $request,
        fn() => response('Test Response', 200),
        'test.action',
        'Test'
    );

    expect($response->getContent())->toBe('Test Response')
        ->and($response->getStatusCode())->toBe(200);
});
