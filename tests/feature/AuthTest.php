<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\Mock\MockSecurity;
use Config\Security;
use Config\Services;

/**
 * @internal
 */
final class AuthTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $migrate = false;

    protected function setUp(): void
    {
        parent::setUp();
        Services::injectMock('security', new MockSecurity(config(Security::class)));
    }

    private function loginPost(string $email, string $password)
    {
        $security = service('security');

        return $this->post('login', [
            $security->getTokenName() => $security->getHash(),
            'email'                   => $email,
            'password'                => $password,
        ]);
    }

    public function testLoginPageLoads(): void
    {
        $result = $this->get('login');
        $result->assertStatus(200);
        $result->assertSee('Sign In');
    }

    public function testInvalidLoginShowsGenericError(): void
    {
        $result = $this->loginPost('nobody@wbmm.com', 'wrongpassword');

        $result->assertRedirect();
        $result->assertSessionHas('error');
    }

    public function testValidAdminLoginRedirectsToDashboard(): void
    {
        $result = $this->loginPost('admin@wbmm.com', 'Admin@1234');

        $result->assertRedirect();
        $this->assertStringContainsString('dashboard', $result->response()->getHeaderLine('Location'));
    }

    public function testCollectorLoginRedirectsToPaymentCreate(): void
    {
        $result = $this->loginPost('collector1@wbmm.com', 'Admin@1234');

        $result->assertRedirect();
        $this->assertStringContainsString('payments/create', $result->response()->getHeaderLine('Location'));
    }
}
