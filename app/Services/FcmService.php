<?php

namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            $client = new Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $accessToken = $client->fetchAccessTokenWithAssertion();

            // Get Project ID from the loaded JSON
            $authConfig = json_decode(file_get_contents($credentialsPath), true);
            $this->projectId = $authConfig['project_id'];

            return $accessToken['access_token'];
        } catch (\Exception $e) {
            Log::error('Error generating FCM Token: ' . $e->getMessage());
            return null;
        }
    }

    public static function sendNotification($token, $title, $body, $data = [])
    {
        $service = new self();
        $accessToken = $service->getAccessToken();

        if (!$accessToken) {
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$service->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => $data // Data must be string key-values
            ]
        ];

        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        if ($response->failed()) {
            Log::error('FCM Send Error: ' . $response->body());
            return false;
        }

        return true;
    }
}
