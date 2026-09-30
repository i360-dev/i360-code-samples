<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

/**
 * Sends mail directly via Microsoft Graph API using the OAuth2 client
 * credentials flow — no third-party mail package involved.
 *
 * This mirrors the exact request sequence that was manually verified
 * to work via PowerShell (token acquisition + POST to /sendMail).
 */
class MicrosoftGraphMailService
{
    protected string $tenant;
    protected string $client;
    protected string $secret;

    public function __construct()
    {
        $this->tenant = config('services.msgraph.tenant');
        $this->client = config('services.msgraph.client');
        $this->secret = config('services.msgraph.secret');
    }

    /**
     * Send an email via Microsoft Graph.
     *
     * @param  string  $to       Recipient email address
     * @param  string  $subject  Email subject
     * @param  string  $body     Email body content
     * @param  string|null  $from  Sending mailbox (defaults to MAIL_FROM_ADDRESS)
     * @param  bool  $isHtml    Whether $body is HTML (true) or plain text (false)
     * @param  string|null  $replyTo  Address replies should route to (defaults to none / $from)
     * @param  string|null  $replyToName  Display name for $replyTo
     */
    public function send(
        string $to,
        string $subject,
        string $body,
        ?string $from = null,
        bool $isHtml = true,
        ?string $replyTo = null,
        ?string $replyToName = null,
    ): void {
        $from = $from ?? config('mail.from.address');

        if (! $from) {
            throw new RuntimeException('No sending address configured (MAIL_FROM_ADDRESS is empty).');
        }

        $token = $this->getAccessToken();

        $message = [
            'subject' => $subject,
            'body' => [
                'contentType' => $isHtml ? 'HTML' : 'Text',
                'content' => $body,
            ],
            'toRecipients' => [
                ['emailAddress' => ['address' => $to]],
            ],
        ];

        if ($replyTo) {
            $message['replyTo'] = [
                ['emailAddress' => array_filter([
                    'address' => $replyTo,
                    'name' => $replyToName,
                ])],
            ];
        }

        $response = Http::withToken($token)
            ->post("https://graph.microsoft.com/v1.0/users/{$from}/sendMail", [
                'message' => $message,
                'saveToSentItems' => true,
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Microsoft Graph sendMail failed: '.$response->status().' '.$response->body()
            );
        }
    }

    /**
     * Get a cached access token, requesting a fresh one only when needed.
     * Tokens are valid ~60 minutes; cached for 50 to leave safety margin.
     */
    protected function getAccessToken(): string
    {
        return Cache::remember('msgraph_access_token', now()->addMinutes(50), function () {
            $response = Http::asForm()->post(
                "https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/token",
                [
                    'client_id' => $this->client,
                    'client_secret' => $this->secret,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
                ]
            );

            if ($response->failed()) {
                throw new RuntimeException(
                    'Failed to obtain Microsoft Graph token: '.$response->status().' '.$response->body()
                );
            }

            return $response->json('access_token');
        });
    }
}
