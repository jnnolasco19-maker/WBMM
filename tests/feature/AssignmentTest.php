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
final class AssignmentTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $migrate = false;

    protected function setUp(): void
    {
        parent::setUp();
        Services::injectMock('security', new MockSecurity(config(Security::class)));
    }

    public function testEditAssignmentAccessDeniedForCollector(): void
    {
        $result = $this->withSession([
            'is_logged_in' => true,
            'user_id'      => 3,
            'user_role'    => 'collector',
            'user_name'    => 'Juan Maningil',
        ])->get('assignments/edit/1');
        $result->assertStatus(302); // Redirect to dashboard due to role restriction
    }

    public function testEditAssignmentGet(): void
    {
        $result = $this->withSession([
            'is_logged_in' => true,
            'user_id'      => 1,
            'user_role'    => 'admin',
            'user_name'    => 'Market Administrator',
        ])->get('assignments/edit/1');
        $result->assertOK();
        $result->assertSee('Renew Permit / Edit Assignment');
        $result->assertSee('PRM-');
    }

    public function testEditAssignmentPostSuccess(): void
    {
        $uniquePermitNo = 'PRM-TEST-' . time();

        $security = service('security');

        $result = $this->withSession([
            'is_logged_in' => true,
            'user_id'      => 1,
            'user_role'    => 'admin',
            'user_name'    => 'Market Administrator',
        ])->post('assignments/edit/1', [
            $security->getTokenName() => $security->getHash(),
            'permit_no'     => $uniquePermitNo,
            'permit_issued' => '2026-01-01',
            'permit_expiry' => '2026-12-31',
            'assigned_date' => '2026-01-01',
            'notes'         => 'Renewed for testing',
        ]);

        $result->assertStatus(302); // Redirect back to vendor view
        $result->assertSessionHas('success', 'Assignment updated successfully.');

        // Assert record is updated in the database
        $db = \Config\Database::connect();
        $record = $db->table('vendor_stalls')->where('id', 1)->get()->getRowArray();
        $this->assertNotNull($record);
        $this->assertSame($uniquePermitNo, $record['permit_no']);
    }
}
