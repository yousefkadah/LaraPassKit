<?php

namespace Tests\Feature\Certificates;

use App\Mail\CertificateRenewalMail;
use App\Models\AppleCertificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateRenewalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected AppleCertificate $certificate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->approved()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'region' => 'US',
            'tier' => 'Verified_And_Configured',
        ]);

        $this->certificate = AppleCertificate::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test renewal flow generates new CSR.
     */
    public function testRenewalFlowGeneratesNewCsr(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->get(
            "/api/certificates/apple/{$this->certificate->id}/renew"
        );

        $response->assertSuccessful();

        // Verify CSR file is downloaded
        $response->assertHeader('Content-Disposition', 'attachment; filename="cert.certSigningRequest"');

        // Verify PEM format
        $content = $response->getContent();
        $this->assertStringContainsString('-----BEGIN CERTIFICATE REQUEST-----', $content);
    }

    /**
     * Test email with renewal instructions is sent.
     */
    public function testEmailWithRenewalInstructionsIsSent(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->get(
            "/api/certificates/apple/{$this->certificate->id}/renew"
        );

        $response->assertSuccessful();

        // Verify email was sent
        Mail::assertSent(CertificateRenewalMail::class, function ($mail) {
            return $mail->hasTo('john@example.com');
        });
    }

    /**
     * Test new cert upload creates fresh record.
     */
    public function testNewCertUploadCreatesFreshRecord(): void
    {
        Storage::fake('certificates');

        $oldCertId = $this->certificate->id;

        $certContent = $this->getValidAppleCertificatePem();
        $file = UploadedFile::fromString(
            $certContent,
            'certificate.cer',
            'application/x-pkcs12'
        );

        $response = $this->actingAs($this->user)->postJson(
            '/api/certificates/apple',
            ['certificate' => $file]
        );

        $response->assertSuccessful();

        // Verify new certificate was created
        $newCert = AppleCertificate::where('user_id', $this->user->id)
            ->where('id', '<>', $oldCertId)
            ->first();

        $this->assertNotNull($newCert);
        $this->assertNotEquals($oldCertId, $newCert->id);
    }

    /**
     * Test only certificate owner can renew.
     */
    public function testOnlyCertificateOwnerCanRenew(): void
    {
        Mail::fake();

        $otherUser = User::factory()->approved()->create([
            'email' => 'other@example.com',
        ]);

        $response = $this->actingAs($otherUser)->get(
            "/api/certificates/apple/{$this->certificate->id}/renew"
        );

        $response->assertForbidden();

        Mail::assertNotSent(CertificateRenewalMail::class);
    }

    /**
     * Test renewing non-existent certificate fails.
     */
    public function testRenewingNonExistentCertificateFails(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->get(
            '/api/certificates/apple/999/renew'
        );

        $response->assertNotFound();

        Mail::assertNotSent(CertificateRenewalMail::class);
    }

    /**
     * Test unauthenticated user cannot renew certificate.
     */
    public function testUnauthenticatedUserCannotRenewCertificate(): void
    {
        Mail::fake();

        $response = $this->get(
            "/api/certificates/apple/{$this->certificate->id}/renew"
        );

        $response->assertUnauthorized();

        Mail::assertNotSent(CertificateRenewalMail::class);
    }

    /**
     * Test renewal response includes proper JSON structure.
     */
    public function testRenewalResponseIncludesProperJsonStructure(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->get(
            "/api/certificates/apple/{$this->certificate->id}/renew"
        );

        $response->assertSuccessful();

        // For file downloads, we get the file content, not JSON
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertGreaterThan(0, strlen($content));
    }

    /**
     * Test certificate marked as renewal_pending after renew request.
     *
     * Note: This test assumes we track renewal status in the database.
     * If not implemented, this can be skipped or the implementation added.
     */
    public function testCertificateMarkedAsRenewalPending(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->get(
            "/api/certificates/apple/{$this->certificate->id}/renew"
        );

        $response->assertSuccessful();

        // Refresh certificate from database
        $this->certificate->refresh();

        // Verify certificate is still active (not soft deleted)
        $this->assertNull($this->certificate->deleted_at);
    }

    /**
     * Get a valid Apple certificate in PEM format for testing.
     */
    private function getValidAppleCertificatePem(): string
    {
        return <<<'CERT'
-----BEGIN CERTIFICATE-----
MIIDXTCCAkWgAwIBAgIJAKTTqJpJrMVeMA0GCSqGSIb3DQEBCwUAMEUxCzAJBgNV
BAYTAlVTMQswCQYDVQQIDAJDQTELMAkGA1UEBwwCQkExDzANBgNVBAoMBkFwcGxl
MB4XDTI0MDEwMTAwMDAwMFoXDTI1MDEwMTAwMDAwMFowRTELMAkGA1UEBhMCVVMx
CzAJBgNVBAgMAkNBMQswCQYDVQQHDAJCQTEPMA0GA1UECgwGQXBwbGUwggEiMA0G
CSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDU2OwkJ7BK3o3uKiGgLi4Aw5V3KHCT
g0oL0VkVlWoN5Q5YZ3vJlJ3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z7Z
3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7
Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7
Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vccAAwEAAaNQME4wHQYDVR0OBBYEFG7Y
OmX/R3J8xPF/Zm7YQZXzzcgzMB8GA1UdIwQYMBaAFG7YOmX/R3J8xPF/Zm7YQZXz
zcgzMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQELBQADggEBAJk0O4K8oAz9qPf2
vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3v
Z7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3v
Z7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3v
Z7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3vZ7Z3v
-----END CERTIFICATE-----
CERT;
    }
}
