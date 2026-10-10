<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContactTurnstileTest extends TestCase
{
    use RefreshDatabase;

    private array $form = [
        'name' => 'Teszt Elek',
        'email' => 'teszt@example.com',
        'message' => 'Próbaüzenet',
        'consent' => '1',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
        config(['services.turnstile.site_key' => 'site', 'services.turnstile.secret_key' => 'secret']);
    }

    public function test_missing_token_is_rejected(): void
    {
        Http::fake();

        $this->post('/kapcsolat', $this->form)->assertSessionHasErrorsIn('contact', 'cf-turnstile-response');
        $this->assertSame(0, ContactMessage::count());
        Http::assertNothingSent();
    }

    public function test_failed_verification_is_rejected(): void
    {
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false])]);

        $this->post('/kapcsolat', $this->form + ['cf-turnstile-response' => 'bad'])
            ->assertSessionHasErrorsIn('contact', 'cf-turnstile-response');
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_valid_token_saves_message(): void
    {
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);

        $this->post('/kapcsolat', $this->form + ['cf-turnstile-response' => 'good'])->assertSessionHasNoErrors();
        $this->assertSame(1, ContactMessage::count());
        Http::assertSent(fn ($r) => $r['secret'] === 'secret' && $r['response'] === 'good');
    }

    public function test_disabled_when_keys_missing(): void
    {
        config(['services.turnstile.site_key' => null, 'services.turnstile.secret_key' => null]);
        Http::fake();

        $this->post('/kapcsolat', $this->form)->assertSessionHasNoErrors();
        $this->assertSame(1, ContactMessage::count());
        Http::assertNothingSent();
    }
}
