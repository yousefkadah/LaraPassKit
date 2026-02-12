<?php

namespace Tests\Unit\Services;

use App\Models\BusinessDomain;
use App\Models\User;
use App\Services\EmailDomainService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EmailDomainServiceTest extends TestCase
{
    protected EmailDomainService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EmailDomainService::class);
    }

    /**
     * Test domain extraction from email.
     */
    public function testExtractDomainFromEmail(): void
    {
        $domain = $this->service->extractDomain('john@stripe.com');
        $this->assertEquals('stripe.com', $domain);

        $domain = $this->service->extractDomain('jane.doe+test@google.com');
        $this->assertEquals('google.com', $domain);
    }

    /**
     * Test business domain check with cache.
     */
    public function testIsBusinessDomainWithCache(): void
    {
        BusinessDomain::create(['domain' => 'stripe.com']);

        // First call should query database
        $result = $this->service->isBusinessDomain('john@stripe.com');
        $this->assertTrue($result);

        // Verify cache is set
        $cached = Cache::get(EmailDomainService::CACHE_KEY);
        $this->assertIsArray($cached);
        $this->assertContains('stripe.com', $cached);

        // Second call should use cache
        $result2 = $this->service->isBusinessDomain('jane@stripe.com');
        $this->assertTrue($result2);
    }

    /**
     * Test consumer domain returns false.
     */
    public function testConsumerDomainNotWhitelisted(): void
    {
        BusinessDomain::create(['domain' => 'stripe.com']);

        $result = $this->service->isBusinessDomain('john@gmail.com');
        $this->assertFalse($result);

        $result = $this->service->isBusinessDomain('jane@yahoo.com');
        $this->assertFalse($result);
    }

    /**
     * Test case-insensitive domain matching.
     */
    public function testDomainMatchingIsCaseInsensitive(): void
    {
        BusinessDomain::create(['domain' => 'Stripe.com']);

        $result = $this->service->isBusinessDomain('john@STRIPE.COM');
        $this->assertTrue($result);

        $result = $this->service->isBusinessDomain('jane@stripe.COM');
        $this->assertTrue($result);
    }

    /**
     * Test cache invalidation.
     */
    public function testCacheInvalidation(): void
    {
        BusinessDomain::create(['domain' => 'stripe.com']);

        // Populate cache
        $this->service->isBusinessDomain('john@stripe.com');
        $this->assertNotNull(Cache::get(EmailDomainService::CACHE_KEY));

        // Invalidate cache
        $this->service->invalidateCache();
        $this->assertNull(Cache::get(EmailDomainService::CACHE_KEY));
    }

    /**
     * Test approval status for business domain.
     */
    public function testApprovalStatusForBusinessDomain(): void
    {
        BusinessDomain::create(['domain' => 'stripe.com']);

        $status = $this->service->getApprovalStatus('john@stripe.com');
        $this->assertEquals('approved', $status);
    }

    /**
     * Test approval status for consumer domain.
     */
    public function testApprovalStatusForConsumerDomain(): void
    {
        BusinessDomain::create(['domain' => 'stripe.com']);

        $status = $this->service->getApprovalStatus('john@gmail.com');
        $this->assertEquals('pending', $status);
    }

    /**
     * Test queue for approval.
     */
    public function testQueueForApproval(): void
    {
        $user = User::factory()->approved()->create();

        $this->service->queueForApproval($user);

        $user->refresh();
        $this->assertEquals('pending', $user->approval_status);
        $this->assertNull($user->approved_at);
    }

    /**
     * Test approve account.
     */
    public function testApproveAccount(): void
    {
        $user = User::factory()->pending()->create();
        $admin = User::factory()->admin()->create();

        $this->service->approveAccount($user, $admin);

        $user->refresh();
        $this->assertEquals('approved', $user->approval_status);
        $this->assertNotNull($user->approved_at);
        $this->assertEquals($admin->id, $user->approved_by);
    }

    /**
     * Test reject account.
     */
    public function testRejectAccount(): void
    {
        $user = User::factory()->pending()->create();
        $admin = User::factory()->admin()->create();

        $this->service->rejectAccount($user, $admin);

        $user->refresh();
        $this->assertEquals('rejected', $user->approval_status);
        $this->assertNotNull($user->approved_at);
        $this->assertEquals($admin->id, $user->approved_by);
    }
}
