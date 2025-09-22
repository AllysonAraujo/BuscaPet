<?php
class PetfinderAPI {
    private $api_key;
    private $api_secret;
    private $base_url = 'https://api.petfinder.com/v2';
    private $access_token;
    private $token_expires;

    public function __construct($api_key = null, $api_secret = null) {
        // Use environment variables or provide defaults for demo
        $this->api_key = $api_key ?: ($_ENV['PETFINDER_API_KEY'] ?? 'demo_key');
        $this->api_secret = $api_secret ?: ($_ENV['PETFINDER_API_SECRET'] ?? 'demo_secret');
    }

    private function getAccessToken() {
        if ($this->access_token && $this->token_expires > time()) {
            return $this->access_token;
        }

        $url = $this->base_url . '/oauth2/token';
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->api_key,
            'client_secret' => $this->api_secret
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        try {
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            
            if ($result === false) {
                throw new Exception('Failed to get access token');
            }

            $response = json_decode($result, true);
            
            if (isset($response['access_token'])) {
                $this->access_token = $response['access_token'];
                $this->token_expires = time() + $response['expires_in'] - 60; // Refresh 1 minute early
                return $this->access_token;
            } else {
                throw new Exception('Invalid API response');
            }
        } catch (Exception $e) {
            error_log('Petfinder API Error: ' . $e->getMessage());
            return false;
        }
    }

    public function searchAnimals($params = []) {
        $token = $this->getAccessToken();
        if (!$token) {
            return $this->getMockData();
        }

        $url = $this->base_url . '/animals?' . http_build_query($params);
        
        $options = [
            'http' => [
                'header' => "Authorization: Bearer $token\r\n",
                'method' => 'GET'
            ]
        ];

        try {
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            
            if ($result === false) {
                throw new Exception('Failed to fetch animals');
            }

            $response = json_decode($result, true);
            return $this->formatAnimalsResponse($response);
            
        } catch (Exception $e) {
            error_log('Petfinder API Error: ' . $e->getMessage());
            return $this->getMockData();
        }
    }

    public function getAnimalTypes() {
        $token = $this->getAccessToken();
        if (!$token) {
            return $this->getMockTypes();
        }

        $url = $this->base_url . '/types';
        
        $options = [
            'http' => [
                'header' => "Authorization: Bearer $token\r\n",
                'method' => 'GET'
            ]
        ];

        try {
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            
            if ($result === false) {
                throw new Exception('Failed to fetch animal types');
            }

            return json_decode($result, true);
            
        } catch (Exception $e) {
            error_log('Petfinder API Error: ' . $e->getMessage());
            return $this->getMockTypes();
        }
    }

    private function formatAnimalsResponse($response) {
        if (!isset($response['animals'])) {
            return [];
        }

        $formatted = [];
        foreach ($response['animals'] as $animal) {
            $formatted[] = [
                'id' => 'pf_' . $animal['id'],
                'name' => $animal['name'],
                'species' => $animal['species'],
                'breed' => isset($animal['breeds']['primary']) ? $animal['breeds']['primary'] : 'Mixed',
                'age' => $this->convertAge($animal['age']),
                'description' => $animal['description'] ?? 'No description available',
                'image_url' => isset($animal['primary_photo_cropped']['medium']) ? 
                             $animal['primary_photo_cropped']['medium'] : 'assets/images/default-pet.jpg',
                'owner_username' => 'Petfinder',
                'is_favorited' => false,
                'external' => true,
                'contact' => [
                    'email' => $animal['contact']['email'] ?? '',
                    'phone' => $animal['contact']['phone'] ?? '',
                    'address' => $this->formatAddress($animal['contact']['address'] ?? [])
                ]
            ];
        }

        return $formatted;
    }

    private function convertAge($age) {
        switch (strtolower($age)) {
            case 'baby':
                return 1;
            case 'young':
                return 2;
            case 'adult':
                return 5;
            case 'senior':
                return 10;
            default:
                return null;
        }
    }

    private function formatAddress($address) {
        $parts = [];
        if (!empty($address['city'])) $parts[] = $address['city'];
        if (!empty($address['state'])) $parts[] = $address['state'];
        return implode(', ', $parts);
    }

    private function getMockData() {
        // Return mock data when API is not available
        return [
            [
                'id' => 'mock_1',
                'name' => 'Buddy (Demo)',
                'species' => 'Dog',
                'breed' => 'Golden Retriever',
                'age' => 3,
                'description' => 'Friendly dog from Petfinder API (Demo data)',
                'image_url' => 'assets/images/default-pet.jpg',
                'owner_username' => 'Petfinder (Demo)',
                'is_favorited' => false,
                'external' => true,
                'contact' => [
                    'email' => 'demo@petfinder.com',
                    'phone' => '(555) 123-4567',
                    'address' => 'Demo City, Demo State'
                ]
            ]
        ];
    }

    private function getMockTypes() {
        return [
            'types' => [
                ['name' => 'Dog'],
                ['name' => 'Cat'],
                ['name' => 'Rabbit'],
                ['name' => 'Bird'],
                ['name' => 'Horse']
            ]
        ];
    }
}
?>