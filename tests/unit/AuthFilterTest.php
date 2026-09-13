<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Filters\AuthFilter;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;

/**
 * @internal
 */
final class AuthFilterTest extends CIUnitTestCase
{

    private AuthFilter $authFilter;
    private IncomingRequest $request;
    private Response $response;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->authFilter = new AuthFilter();
        $this->request = new IncomingRequest(new Config\App(), new CodeIgniter\HTTP\URI());
        $this->response = new Response(new Config\App());
        
        // Clear any existing session data
        session()->destroy();
    }

    public function testRedirectsToLoginWhenNotLoggedIn(): void
    {
        // Given: User is not logged in and trying to access another page
        $uri = new CodeIgniter\HTTP\URI('http://example.com/some/restricted/page');
        $this->request = new IncomingRequest(new Config\App(), $uri);
        
        // When: Filter is applied
        $result = $this->authFilter->before($this->request);
        
        // Then: Should redirect to login page
        $this->assertNotNull($result);
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
        $this->assertSame('/auth/login', $result->getHeaderLine('Location'));
    }

    public function testAllowsAccessToLoginPageWhenNotLoggedIn(): void
    {
        // Given: User is not logged in but accessing login page
        $uri = new CodeIgniter\HTTP\URI('http://example.com/auth/login');
        $this->request = new IncomingRequest(new Config\App(), $uri);
        
        // When: Filter is applied
        $result = $this->authFilter->before($this->request);
        
        // Then: Should not redirect
        $this->assertNull($result);
    }

    public function testAllowsAccessWhenLoggedIn(): void
    {
        // Given: User is logged in
        session()->set(['logged_in' => true]);
        $uri = new CodeIgniter\HTTP\URI('http://example.com/some/restricted/page');
        $this->request = new IncomingRequest(new Config\App(), $uri);
        
        // When: Filter is applied
        $result = $this->authFilter->before($this->request);
        
        // Then: Should not redirect
        $this->assertNull($result);
    }

    public function testRedirectsWhenRoleDoesNotMatch(): void
    {
        // Given: User is logged in with wrong role
        session()->set([
            'logged_in' => true,
            'role' => 'user'
        ]);
        $uri = new CodeIgniter\HTTP\URI('http://example.com/admin/dashboard');
        $this->request = new IncomingRequest(new Config\App(), $uri);
        
        // When: Filter is applied with admin role requirement
        $result = $this->authFilter->before($this->request, ['admin']);
        
        // Then: Should redirect to user dashboard
        $this->assertNotNull($result);
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
        $this->assertSame('/user', $result->getHeaderLine('Location'));
    }

    public function testAllowsAccessWhenRoleMatches(): void
    {
        // Given: User is logged in with correct role
        session()->set([
            'logged_in' => true,
            'role' => 'admin'
        ]);
        $uri = new CodeIgniter\HTTP\URI('http://example.com/admin/dashboard');
        $this->request = new IncomingRequest(new Config\App(), $uri);
        
        // When: Filter is applied with matching role requirement
        $result = $this->authFilter->before($this->request, ['admin']);
        
        // Then: Should not redirect
        $this->assertNull($result);
    }

    public function testDoesNotRedirectWhenAlreadyOnTargetPath(): void
    {
        // Given: User is logged in with user role and accessing their own dashboard
        session()->set([
            'logged_in' => true,
            'role' => 'user'
        ]);
        $uri = new CodeIgniter\HTTP\URI('http://example.com/user');
        $this->request = new IncomingRequest(new Config\App(), $uri);
        
        // When: Filter is applied with user role requirement
        $result = $this->authFilter->before($this->request, ['user']);
        
        // Then: Should not redirect
        $this->assertNull($result);
    }

    public function testAfterMethodExists(): void
    {
        // Test that after method exists and can be called
        $result = $this->authFilter->after($this->request, $this->response);
        $this->assertNull($result);
    }
}