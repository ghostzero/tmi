<?php

namespace GhostZero\Tmi;

use React\Http\HttpServer;
use React\Http\Browser;
use React\Http\Message\Response;
use React\Socket\Server as SocketServer;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Psr\Http\Message\ServerRequestInterface;

class OAuthHelper
{
    private const AUTH_URL = 'https://id.twitch.tv/oauth2/authorize';
    private const TOKEN_URL = 'https://id.twitch.tv/oauth2/token';
    private const VALIDATE_URL = 'https://id.twitch.tv/oauth2/validate';

    private string $clientId;
    private string $clientSecret;
    private int $port;
    private string $tokenFile;

    public function __construct(
        string $clientId,
        string $clientSecret,
        int $port = 3000,
        string $tokenFile = '.twitch_token.json'
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->port = $port;
        $this->tokenFile = $tokenFile;
    }

    /**
     * Returns a valid access token, running the OAuth flow when needed.
     *
     * @param string[] $scopes Required OAuth scopes.
     */
    public function getAccessToken(array $scopes = ['user:read:chat']): string
    {
        $token = $this->loadToken();

        if ($token !== null && $this->scopesChanged($token, $scopes)) {
            $token = null;
        }

        if ($token !== null && !$this->isTokenExpired($token)) {
            return $token['access_token'];
        }

        if ($token !== null && isset($token['refresh_token'])) {
            try {
                $refreshed = $this->exchangeToken([
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $token['refresh_token'],
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);
                $this->saveToken($refreshed);
                return $refreshed['access_token'];
            } catch (\Exception $e) {
                // Fall through to full flow
            }
        }

        $token = $this->runOAuthFlow($scopes);
        $this->saveToken($token);
        return $token['access_token'];
    }

    /**
     * Returns the Twitch user ID for the authenticated account, or null if not yet authorized.
     */
    public function getUserId(): ?string
    {
        return $this->loadToken()['user_id'] ?? null;
    }

    /**
     * Returns the Twitch login name for the authenticated account, or null if not yet authorized.
     */
    public function getLogin(): ?string
    {
        return $this->loadToken()['login'] ?? null;
    }

    private function runOAuthFlow(array $scopes): array
    {
        $loop = Factory::create();
        $state = bin2hex(random_bytes(16));
        $redirectUri = "http://localhost:{$this->port}/callback";

        $authUrl = self::AUTH_URL . '?' . http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'state' => $state,
            'force_verify' => 'true',
        ]);

        $browser = new Browser($loop);
        $socket = new SocketServer("127.0.0.1:{$this->port}", $loop);

        $tokenData = null;
        $flowError = null;

        $httpServer = new HttpServer(
            function (ServerRequestInterface $request) use (
                $state, $redirectUri, $browser, $socket, $loop, &$tokenData, &$flowError
            ) {
                parse_str($request->getUri()->getQuery(), $params);

                if (($params['state'] ?? '') !== $state) {
                    return new Response(400, ['Content-Type' => 'text/plain'], 'Invalid state parameter.');
                }

                if (isset($params['error'])) {
                    $flowError = $params['error_description'] ?? $params['error'];
                    $socket->close();
                    $loop->stop();
                    return new Response(400, ['Content-Type' => 'text/plain'], "Authorization denied: {$flowError}");
                }

                $browser->post(self::TOKEN_URL, [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ], http_build_query([
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code' => $params['code'],
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $redirectUri,
                ]))->then(
                    function ($response) use ($browser, $socket, $loop, &$tokenData, &$flowError) {
                        $data = json_decode((string) $response->getBody(), true);

                        if (!isset($data['access_token'])) {
                            $flowError = 'Token exchange failed: ' . (string) $response->getBody();
                            $socket->close();
                            $loop->stop();
                            return;
                        }

                        // Fetch user info so caller knows broadcaster_user_id / user_id
                        $browser->get(self::VALIDATE_URL, [
                            'Authorization' => 'OAuth ' . $data['access_token'],
                        ])->then(
                            function ($vResponse) use ($data, $socket, $loop, &$tokenData) {
                                $userInfo = json_decode((string) $vResponse->getBody(), true);
                                $tokenData = array_merge($data, [
                                    'expires_at' => time() + ($data['expires_in'] ?? 3600),
                                    'user_id' => $userInfo['user_id'] ?? null,
                                    'login' => $userInfo['login'] ?? null,
                                ]);
                                $socket->close();
                                $loop->stop();
                            },
                            function () use ($data, $socket, $loop, &$tokenData) {
                                $tokenData = array_merge($data, [
                                    'expires_at' => time() + ($data['expires_in'] ?? 3600),
                                ]);
                                $socket->close();
                                $loop->stop();
                            }
                        );
                    },
                    function (\Exception $e) use ($socket, $loop, &$flowError) {
                        $flowError = $e->getMessage();
                        $socket->close();
                        $loop->stop();
                    }
                );

                return new Response(
                    200,
                    ['Content-Type' => 'text/html'],
                    '<!DOCTYPE html><html><body>' .
                    '<h1>Authorization successful!</h1>' .
                    '<p>You can close this window and return to the terminal.</p>' .
                    '</body></html>'
                );
            }
        );

        $httpServer->listen($socket);

        echo PHP_EOL . "Opening Twitch authorization page in your browser..." . PHP_EOL;
        echo "If it does not open automatically, visit:" . PHP_EOL . PHP_EOL;
        echo "  {$authUrl}" . PHP_EOL . PHP_EOL;
        echo "Waiting for authorization on http://localhost:{$this->port}/callback ..." . PHP_EOL;

        $this->openBrowser($authUrl);

        $loop->run();

        if ($flowError !== null) {
            throw new \RuntimeException("OAuth flow failed: {$flowError}");
        }

        if (!isset($tokenData['access_token'])) {
            throw new \RuntimeException('OAuth flow failed: no access token received.');
        }

        echo "Authorized as: " . ($tokenData['login'] ?? 'unknown') .
            " (user_id: " . ($tokenData['user_id'] ?? 'unknown') . ")" . PHP_EOL . PHP_EOL;

        return $tokenData;
    }

    private function exchangeToken(array $params): array
    {
        $loop = Factory::create();
        $browser = new Browser($loop);

        $result = null;
        $error = null;

        $browser->post(self::TOKEN_URL, [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ], http_build_query($params))->then(
            function ($response) use (&$result, $loop) {
                $data = json_decode((string) $response->getBody(), true);
                if (isset($data['access_token'])) {
                    $data['expires_at'] = time() + ($data['expires_in'] ?? 3600);
                    $result = $data;
                }
                $loop->stop();
            },
            function (\Exception $e) use (&$error, $loop) {
                $error = $e->getMessage();
                $loop->stop();
            }
        );

        $loop->run();

        if ($error !== null || $result === null) {
            throw new \RuntimeException("Token exchange failed: " . ($error ?? 'empty response'));
        }

        return $result;
    }

    private function openBrowser(string $url): void
    {
        $escaped = escapeshellarg($url);
        match (PHP_OS_FAMILY) {
            'Windows' => exec("start {$escaped}"),
            'Darwin'  => exec("open {$escaped}"),
            default   => exec("xdg-open {$escaped} 2>/dev/null &"),
        };
    }

    private function loadToken(): ?array
    {
        if (!file_exists($this->tokenFile)) {
            return null;
        }

        $data = json_decode(file_get_contents($this->tokenFile), true);
        return is_array($data) ? $data : null;
    }

    private function saveToken(array $tokenData): void
    {
        file_put_contents($this->tokenFile, json_encode($tokenData, JSON_PRETTY_PRINT));
    }

    private function isTokenExpired(array $tokenData): bool
    {
        if (!isset($tokenData['expires_at'])) {
            return false;
        }
        return time() >= ($tokenData['expires_at'] - 60);
    }

    private function scopesChanged(array $token, array $requestedScopes): bool
    {
        $stored = $token['scope'] ?? [];
        if (is_string($stored)) {
            $stored = explode(' ', $stored);
        }
        sort($stored);
        sort($requestedScopes);
        return $stored !== $requestedScopes;
    }
}
