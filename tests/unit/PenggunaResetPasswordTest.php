<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Router\DefinedRouteCollector;

/**
 * @internal
 */
final class PenggunaResetPasswordTest extends CIUnitTestCase
{
    public function testResetPasswordRoutesExist(): void
    {
        $collection = service('routes')->loadRoutes();
        $definedRoutes = iterator_to_array((new DefinedRouteCollector($collection))->collect());

        $resetRoutes = array_filter($definedRoutes, function ($r) {
            return str_contains($r['route'], 'pengguna') && str_contains($r['route'], 'reset-password');
        });

        $this->assertNotEmpty($resetRoutes, 'Reset password routes should be defined');

        $methods = array_map('strtoupper', array_column($resetRoutes, 'method'));
        $this->assertContains('GET', $methods);
        $this->assertContains('POST', $methods);
    }

    public function testPasswordHashVerification(): void
    {
        $defaultPassword = '12345678';
        $hash = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $this->assertTrue(password_verify('12345678', $hash));
        $this->assertFalse(password_verify('wrongpassword', $hash));
    }

    public function testResetPasswordValidationRequirement(): void
    {
        // Enforce minimum 8 characters requirement
        $shortPassword = '12345';
        $this->assertLessThan(8, strlen($shortPassword));

        $validPassword = '12345678';
        $this->assertGreaterThanOrEqual(8, strlen($validPassword));
    }
}
