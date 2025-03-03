<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SAPServices
{
    protected Client $client;
    protected ?string $sessionId = null;
    protected string $baseUrl;
    protected ?string $companyDb = null;
    protected const CACHE_PREFIX = 'sap_session_';
    protected const CACHE_DURATION = 3600; // 1 hour
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->baseUrl = rtrim(config('sap.sapb1.url'), '/') . '/';
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'verify' => false,
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => true,
        ]);
    }

    public function connect(): void
    {
        $this->setCompanyDb();
        $this->initSession();
    }

    protected function setCompanyDb(): void
    {
        // Coba ambil dari session menggunakan request
        $this->companyDb = $this->request->session()->get('company_db');

        // Jika tidak ada di session, coba ambil dari user yang terautentikasi
        if (!$this->companyDb && Auth::check()) {
            $user = Auth::user();
            $this->companyDb = $user->employe->branch->database ?? null;

            // Jika berhasil mendapatkan dari user, simpan ke session
            if ($this->companyDb) {
                $this->request->session()->put('company_db', $this->companyDb);
            }
        }

        if (!$this->companyDb) {
            throw new \Exception('Database branch tidak ditemukan. Silahkan hubungi administrator.');
        }
    }

    protected function getCacheKey(): string
    {
        $userId = Auth::id() ?? 'guest';
        $companyKey = $this->companyDb ?? 'default';
        return self::CACHE_PREFIX . $userId . '_' . $companyKey;
    }

    protected function initSession(): void
    {
        // Coba ambil session dari cache
        $this->sessionId = Cache::get($this->getCacheKey());

        if (!$this->sessionId) {
            $this->authenticate();
        }
    }

    protected function authenticate(): void
    {
        if (!$this->companyDb) {
            throw new \Exception('Database branch harus diset sebelum melakukan autentikasi.');
        }

        try {
            $response = $this->client->post('Login', [
                'json' => [
                    'UserName' => config('sap.sapb1.username'),
                    'Password' => config('sap.sapb1.password'),
                    'CompanyDB' => $this->companyDb,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            $this->sessionId = $body['SessionId'] ?? null;

            if ($this->sessionId) {
                Cache::put($this->getCacheKey(), $this->sessionId, self::CACHE_DURATION);
            } else {
                throw new \Exception('Session ID tidak ditemukan dalam response');
            }
        } catch (RequestException $e) {
            Log::error('SAP Authentication gagal', [
                'error' => $e->getMessage(),
                'company_db' => $this->companyDb,
                'user_id' => Auth::id(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);
            throw new \Exception('Autentikasi ke SAP B1 gagal: ' . $e->getMessage());
        }
    }

    protected function getDefaultHeaders(): array
    {
        return [
            'Cookie' => "B1SESSION={$this->sessionId}",
            'Content-Type' => 'application/json',
            'Prefer' => 'odata.maxpagesize=50'
        ];
    }

    protected function handleRequest(callable $request)
    {
        try {
            // Ambil respons pertama
            $response = $request();
            $data = json_decode($response->getBody()->getContents(), true);

            // Cek apakah responsnya ada error
            if (isset($data['value'])) {
                return $data['value'];
            }
            return $data;
        } catch (RequestException $e) {
            // Jika session expired, coba authenticate ulang
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                Cache::forget($this->getCacheKey());
                $this->authenticate();
                return json_decode($request()->getBody()->getContents(), true);
            }

            Log::error('SAP API Request gagal', [
                'error' => $e->getMessage(),
                'company_db' => $this->companyDb,
                'user_id' => Auth::id(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);

            throw new \Exception('SAP API request gagal: ' . $e->getMessage());
        }
    }

    public function get(string $endpoint, array $parameters = [])
    {
        return $this->handleRequest(function () use ($endpoint, $parameters) {
            return $this->client->get($endpoint, [
                'headers' => $this->getDefaultHeaders(),
                'query' => $parameters
            ]);
        });
    }

    public function getById(string $endpoint, string $id, array $parameters = [])
    {
        return $this->handleRequest(function () use ($endpoint, $id, $parameters) {
            return $this->client->get($endpoint . "('" . $id . "')", [
                'headers' => array_merge(
                    $this->getDefaultHeaders(),
                    ['Prefer' => 'odata.maxpagesize=1']
                ),
                'query' => $parameters
            ]);
        });
    }

    public function post(string $endpoint, array $data)
    {
        return $this->handleRequest(function () use ($endpoint, $data) {
            return $this->client->post($endpoint, [
                'headers' => array_merge(
                    $this->getDefaultHeaders(),
                    ['Prefer' => 'return=representation']
                ),
                'json' => $data
            ]);
        });
    }

    public function put(string $endpoint, array $data)
    {
        return $this->handleRequest(function () use ($endpoint, $data) {
            return $this->client->put($endpoint, [
                'headers' => array_merge(
                    $this->getDefaultHeaders(),
                    ['Prefer' => 'return=representation']
                ),
                'json' => $data
            ]);
        });
    }

    public function patch(string $endpoint, string $id, array $data)
    {
        return $this->handleRequest(function () use ($endpoint, $id, $data) {
            return $this->client->patch($endpoint . "('" . $id . "')", [
                'headers' => array_merge(
                    $this->getDefaultHeaders(),
                    ['Prefer' => 'return=representation']
                ),
                'json' => $data
            ]);
        });
    }

    public function delete(string $endpoint, string $id)
    {
        return $this->handleRequest(function () use ($endpoint, $id) {
            return $this->client->delete($endpoint . "('" . $id . "')", [
                'headers' => $this->getDefaultHeaders()
            ]);
        });
    }

    public function logout(): bool
    {
        try {
            if ($this->sessionId) {
                $response = $this->client->post('Logout', [
                    'headers' => [
                        'Cookie' => "B1SESSION={$this->sessionId}"
                    ]
                ]);

                Cache::forget($this->getCacheKey());
                $this->sessionId = null;

                return true;
            }
            return false;
        } catch (RequestException $e) {
            Log::error('SAP Logout gagal', [
                'error' => $e->getMessage(),
                'company_db' => $this->companyDb,
                'user_id' => Auth::id(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);

            Cache::forget($this->getCacheKey());
            $this->sessionId = null;

            throw new \Exception('Logout dari SAP B1 gagal: ' . $e->getMessage());
        }
    }

    public function refreshCompanyDb(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $branchDB = $user->employe->branch->database ?? null;

            if ($branchDB && $branchDB !== $this->companyDb) {
                $this->logout();
                $this->companyDb = $branchDB;
                $this->request->session()->put('company_db', $branchDB);
                $this->authenticate();
            }
        }
    }

    public function __destruct()
    {
        try {
            if ($this->sessionId) {
                $this->logout();
            }
        } catch (\Exception $e) {
            Log::error('Error saat cleanup SAP service', [
                'error' => $e->getMessage(),
                'company_db' => $this->companyDb
            ]);
        }
    }
}
