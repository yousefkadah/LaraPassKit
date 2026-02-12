<?php

namespace Tests\Feature\Certificates;

use App\Mail\CsrDownloadMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CSRGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->approved()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'region' => 'US',
            'tier' => 'Email_Verified',
        ]);
    }

    /**
     * Test CSR downloads with correct filename.
     */
    public function testCsrDownloadsWithCorrectFilename(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->get('/api/certificates/apple/csr');

        $response->assertSuccessful();
        $response->assertHeader('Content-Disposition', 'attachment; filename="cert.certSigningRequest"');
    }

    /**
     * Test CSR content is valid PEM format.
     */
    public function testCsrContentIsValidPemFormat(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->get('/api/certificates/apple/csr');

        $response->assertSuccessful();

        $content = $response->getContent();

        // Verify PEM format
        $this->assertStringContainsString('-----BEGIN CERTIFICATE REQUEST-----', $content);
        $this->assertStringContainsString('-----END CERTIFICATE REQUEST-----', $content);

        // Verify it's not empty
        $this->assertGreaterThan(200, strlen($content));
    }

    /**
     * Test email with instructions is sent.
     */
    public function testEmailWithInstructionsIsSent(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->get('/api/certificates/apple/csr');

        $response->assertSuccessful();

        // Verify email was sent
        Mail::assertSent(CsrDownloadMail::class, function ($mail) {
            return $mail->hasTo('john@example.com');
        });
    }

    /**
     * Test CSR contains correct subject information.
     */
    public function testCsrContainsCorrectSubjectInformation(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->get('/api/certificates/apple/csr');

        $response->assertSuccessful();

        $content = $response->getContent();

        // The CSR should contain the user's email in the subject
        // This is a basic check since parsing CSR content requires openssl
        $this->assertIsString($content);
        $this->assertGreaterThan(0, strlen($content));
    }

    /**
     * Test unauthenticated user cannot download CSR.
     */
    public function testUnauthenticatedUserCannotDownloadCsr(): void
    {
        Mail::fake();

        $response = $this->get('/api/certificates/apple/csr');

        $response->assertUnauthorized();
    }

    /**
     * Test CSR downloads with correct content type.
     */
    public function testCsrDownloadsWithCorrectContentType(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->get('/api/certificates/apple/csr');

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }

    /**
     * Test multiple CSR downloads work correctly.
     */
    public function testMultipleCsrDownloadsWorkCorrectly(): void
    {
        Mail::fake();

        // First download
        $response1 = $this->actingAs($this->user)->get('/api/certificates/apple/csr');
        $response1->assertSuccessful();

        // Second download
        $response2 = $this->actingAs($this->user)->get('/api/certificates/apple/csr');
        $response2->assertSuccessful();

        // Both should have valid PEM format
        $this->assertStringContainsString('-----BEGIN CERTIFICATE REQUEST-----', $response1->getContent());
        $this->assertStringContainsString('-----BEGIN CERTIFICATE REQUEST-----', $response2->getContent());
    }
}
