<?php

namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class FcmService
{
    private $projectId;

    public function __construct()
    {
        // Constructor left empty as we initialize client in getAccessToken
    }

    /**
     * Get Access Token from Service Account JSON
     */
    private function getAccessToken()
    {
        $credentialsPath = storage_path('app/firebase_credentials.json');

        if (!file_exists($credentialsPath)) {
            Log::error('Firebase Credentials not found at: ' . $credentialsPath);
            return null;
        }

        try {
            $authConfig = json_decode(file_get_contents($credentialsPath), true);
            if (!$authConfig || !isset($authConfig['project_id'])) {
                Log::error('Invalid Firebase Credentials JSON or missing project_id');
                return null;
            }

            $this->projectId = $authConfig['project_id'];

            $client = new Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $accessToken = $client->fetchAccessTokenWithAssertion();

            if (!isset($accessToken['access_token'])) {
                Log::error('Failed to fetch access token from Google API');
                return null;
            }

            return $accessToken['access_token'];
        } catch (\Exception $e) {
            Log::error('Error generating FCM Token: ' . $e->getMessage());
            return null;
        }
    }

    public static function sendNotification($token, $title, $body, $data = [], $extraConfig = [])
    {
        $service = new self();
        $accessToken = $service->getAccessToken();

        if (!$accessToken) {
            Log::error("FCM Send Error: Could not get access token");
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$service->projectId}/messages:send";

        // FCM V1 requires all data values to be strings
        $formattedData = [];
        foreach ($data as $key => $value) {
            $formattedData[(string)$key] = (string)$value;
        }

        $payload = [
            'message' => array_merge([
                'token' => $token,
                'notification' => [
                    'title' => (string)$title,
                    'body'  => (string)$body,
                ],
                'data' => $formattedData
            ], $extraConfig)
        ];

        try {
            /** @var Response $response */
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            // Defensive check against "Undefined method failed" or null response
            if (!$response || $response->status() >= 400) {
                $errorMsg = $response ? $response->body() : 'No response from FCM server';
                Log::error("FCM Send Error to Token: " . substr($token, 0, 10) . "... Error: " . $errorMsg);
                return false;
            }

            Log::info("FCM Notification sent successfully to: " . substr($token, 0, 10) . "...");
            return true;
        } catch (\Exception $e) {
            Log::error("FCM Send Exception: " . $e->getMessage());
            return false;
        }
    }
}
