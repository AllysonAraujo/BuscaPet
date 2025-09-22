<?php
/**
 * Petfinder API Configuration
 * Configuration for integrating with Petfinder API
 */

class PetfinderAPI {
    private $api_key;
    private $secret;
    private $base_url = 'https://api.petfinder.com/v2';
    private $token;
    private $token_expiry;
    
    public function __construct() {
        // Note: These should be set via environment variables in production
        $this->api_key = getenv('PETFINDER_API_KEY') ?: 'your_api_key_here';
        $this->secret = getenv('PETFINDER_SECRET') ?: 'your_secret_here';
    }
    
    /**
     * Get access token for API requests
     */
    private function getAccessToken() {
        // Check if token is still valid
        if ($this->token && time() < $this->token_expiry) {
            return $this->token;
        }
        
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->base_url . '/oauth2/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'client_credentials',
                'client_id' => $this->api_key,
                'client_secret' => $this->secret
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            $this->token = $data['access_token'];
            $this->token_expiry = time() + $data['expires_in'] - 60; // 1 minute buffer
            return $this->token;
        }
        
        throw new Exception('Failed to obtain access token');
    }
    
    /**
     * Search for animals
     */
    public function searchAnimals($params = []) {
        $token = $this->getAccessToken();
        
        $curl = curl_init();
        
        $queryString = http_build_query($params);
        $url = $this->base_url . '/animals' . ($queryString ? '?' . $queryString : '');
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ]
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        return null;
    }
    
    /**
     * Get animal by ID
     */
    public function getAnimal($id) {
        $token = $this->getAccessToken();
        
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->base_url . '/animals/' . $id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ]
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        return null;
    }
    
    /**
     * Get organizations
     */
    public function getOrganizations($params = []) {
        $token = $this->getAccessToken();
        
        $curl = curl_init();
        
        $queryString = http_build_query($params);
        $url = $this->base_url . '/organizations' . ($queryString ? '?' . $queryString : '');
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ]
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        return null;
    }
}
?>