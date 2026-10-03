<?php

namespace App\Http\Controllers;

use App\Models\Electrician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ElectricianController extends Controller
{
    /**
     * Detect customer's actual desktop/broadband IP location.
     * 
     * Endpoint: GET /api/my-location
     */
    public function myLocation(Request $request)
    {
        try {
            $clientIp = $request->ip();
            $targetUrl = ($clientIp === '127.0.0.1' || $clientIp === '::1')
                ? 'http://ip-api.com/json'
                : 'http://ip-api.com/json/' . $clientIp;

            $response = Http::timeout(4)->get($targetUrl);

            if ($response->successful() && $response->json('status') === 'success') {
                return response()->json([
                    'success' => true,
                    'city' => $response->json('city') ?? 'Lucknow',
                    'region' => $response->json('regionName') ?? 'Uttar Pradesh',
                    'zip' => $response->json('zip') ?? '226002',
                    'latitude' => (float) $response->json('lat'),
                    'longitude' => (float) $response->json('lon'),
                    'isp' => $response->json('isp') ?? 'Broadband',
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Desktop IP geolocation failed: ' . $e->getMessage());
        }

        // Fallback default: Lucknow coordinates
        return response()->json([
            'success' => true,
            'city' => 'Lucknow',
            'region' => 'Uttar Pradesh',
            'zip' => '226002',
            'latitude' => 26.8373,
            'longitude' => 80.9165,
            'isp' => 'Default Desktop Location',
        ]);
    }

    /**
     * Intelligent Geocoding Layer for Lucknow coordinates.
     * Resolves raw GPS lat/lng into recognized, human-friendly Lucknow localities,
     * mapping obscure OSM micro-hamlets/wards into their actual major living areas.
     * 
     * Endpoint: GET /api/geocode?latitude=26.8373&longitude=80.9165
     */
    public function geocode(Request $request)
    {
        $lat = (float) ($request->query('latitude') ?? $request->query('lat', 26.8530));
        $lng = (float) ($request->query('longitude') ?? $request->query('lng', 80.9980));

        // 1. Comprehensive Lucknow Localities catalog with center coordinates, radius, and known aliases
        $localities = [
            'charbagh' => [
                'name' => 'Charbagh',
                'lat' => 26.8320,
                'lng' => 80.9220,
                'radius_km' => 2.5,
                'aliases' => ['arya nagar', 'trivedi nagar', 'moti nagar', 'naka hindola', 'mawaiya', 'pandeyganj', 'hussainganj', 'dav college', 'pan dariba', 'durga puri', 'charbagh']
            ],
            'gomti_nagar' => [
                'name' => 'Gomti Nagar',
                'lat' => 26.8530,
                'lng' => 80.9980,
                'radius_km' => 4.5,
                'aliases' => ['vipul khand', 'vijay khand', 'vikas khand', 'vivek khand', 'vinay khand', 'viraj khand', 'vibhuti khand', 'patrakar puram', 'husadiya', 'manoj pandey', 'gomti nagar']
            ],
            'indira_nagar' => [
                'name' => 'Indira Nagar',
                'lat' => 26.8780,
                'lng' => 80.9850,
                'radius_km' => 3.5,
                'aliases' => ['munshipulia', 'bhootnath', 'aravalli', 'meena market', 'takrohi', 'indira nagar', 'sector 1', 'sector 2', 'sector 14', 'sector 16', 'sector 18', 'sector 20']
            ],
            'hazratganj' => [
                'name' => 'Hazratganj',
                'lat' => 26.8467,
                'lng' => 80.9462,
                'radius_km' => 2.5,
                'aliases' => ['ashok marg', 'naval kishore', 'mg road', 'gpo', 'vidhan sabha', 'shahnajaf', 'halwasiya', 'lalbagh', 'nawazganj', 'hazratganj']
            ],
            'aliganj' => [
                'name' => 'Aliganj',
                'lat' => 26.8920,
                'lng' => 80.9380,
                'radius_km' => 3.0,
                'aliases' => ['kapoorthala', 'purania', 'dandiya', 'aliganj', 'sector a', 'sector b', 'sector c', 'sector j', 'sector m', 'sector q']
            ],
            'alambagh' => [
                'name' => 'Alambagh',
                'lat' => 26.8150,
                'lng' => 80.9100,
                'radius_km' => 2.8,
                'aliases' => ['sardar patel', 'singar nagar', 'chander nagar', 'geeta palli', 'sucheta kripalani', 'phoenix mall', 'bhilawan', 'vip road', 'alambagh']
            ],
            'rajajipuram' => [
                'name' => 'Rajajipuram',
                'lat' => 26.8520,
                'lng' => 80.8850,
                'radius_km' => 3.0,
                'aliases' => ['talkatora', 'meena bakshi', 'c block', 'e block', 'para', 'tikait rai talab', 'rajajipuram']
            ],
            'chowk' => [
                'name' => 'Chowk',
                'lat' => 26.8680,
                'lng' => 80.9020,
                'radius_km' => 2.5,
                'aliases' => ['rumi darwaza', 'golaganj', 'victoria street', 'akbari gate', 'kashmiri mohalla', 'kallu chauraha', 'chowk']
            ],
            'mahanagar' => [
                'name' => 'Mahanagar',
                'lat' => 26.8720,
                'lng' => 80.9520,
                'radius_km' => 2.2,
                'aliases' => ['gole market', 'mandir marg', 'nishatganj', 'badshahnagar', 'mahanagar']
            ],
            'ashiyana' => [
                'name' => 'Ashiyana',
                'lat' => 26.7910,
                'lng' => 80.9120,
                'radius_km' => 3.2,
                'aliases' => ['lda colony', 'bangla bazar', 'power house', 'transport nagar', 'ruchi khand', 'ratan khand', 'ashiyana']
            ],
            'vikas_nagar' => [
                'name' => 'Vikas Nagar',
                'lat' => 26.8980,
                'lng' => 80.9580,
                'radius_km' => 2.2,
                'aliases' => ['tedhi pulia', 'khurram nagar', 'vikas nagar']
            ],
            'jankipuram' => [
                'name' => 'Jankipuram',
                'lat' => 26.9240,
                'lng' => 80.9490,
                'radius_km' => 3.5,
                'aliases' => ['jankipuram extension', 'engineering college', 'bhawani bazar', 'ring road', 'jankipuram']
            ],
            'aminabad' => [
                'name' => 'Aminabad',
                'lat' => 26.8440,
                'lng' => 80.9310,
                'radius_km' => 1.8,
                'aliases' => ['nazirabad', 'mohan market', 'kaiserbagh', 'ganesh ganj', 'aminabad']
            ],
            'telibagh' => [
                'name' => 'Telibagh',
                'lat' => 26.7780,
                'lng' => 80.9480,
                'radius_km' => 3.5,
                'aliases' => ['vrindavan yojna', 'sgpgims', 'raebareli road', 'sainik nagar', 'avadh vihar', 'telibagh']
            ]
        ];

        // 2. Find closest locality by geometric coordinates
        $closestKey = 'gomti_nagar';
        $closestName = 'Gomti Nagar';
        $minDistance = 999999.0;

        foreach ($localities as $key => $loc) {
            $dist = $this->calculateDistance($lat, $lng, $loc['lat'], $loc['lng']);
            if ($dist < $minDistance) {
                $minDistance = $dist;
                $closestKey = $key;
                $closestName = $loc['name'];
            }
        }

        // 3. Query external reverse geocoder with high precision
        $detectedSubArea = null;
        try {
            $reverseUrl = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1";
            $res = Http::withHeaders(['User-Agent' => 'ElectroFixApp/1.0', 'Accept-Language' => 'en'])
                ->timeout(3)
                ->get($reverseUrl);

            if ($res->successful() && $json = $res->json()) {
                $addr = $json['address'] ?? [];
                $rawSub = $addr['neighbourhood'] 
                    ?? $addr['suburb'] 
                    ?? $addr['residential'] 
                    ?? $addr['quarter'] 
                    ?? $addr['road'] 
                    ?? null;

                if ($rawSub) {
                    $rawSubLower = strtolower($rawSub);
                    // Check if this raw name is an alias of one of our known localities!
                    foreach ($localities as $key => $loc) {
                        foreach ($loc['aliases'] as $alias) {
                            if (str_contains($rawSubLower, $alias)) {
                                $closestKey = $key;
                                $closestName = $loc['name'];
                                break 2;
                            }
                        }
                    }
                    $detectedSubArea = $rawSub;
                }
            }
        } catch (\Exception $e) {
            // Fallback gracefully to coordinate closest
        }

        // 4. Return clean, human-recognized Lucknow area
        return response()->json([
            'success' => true,
            'area' => $closestName,
            'colony' => $detectedSubArea,
            'formatted' => $closestName . ', Lucknow',
            'latitude' => $lat,
            'longitude' => $lng,
            'distance_to_center_km' => round($minDistance, 2),
            'area_key' => $closestKey,
        ]);
    }

    /**
     * Find active electricians from MySQL database near the customer's coordinates.
     * 
     * Endpoint: GET /api/electricians/nearby?latitude=28.6315&longitude=77.2167
     */
    public function nearby(Request $request)
    {
        // 1. Customer's coordinates from query parameters
        $customerLat = (float) ($request->query('latitude') ?? $request->query('lat', 28.6315));
        $customerLng = (float) ($request->query('longitude') ?? $request->query('lng', 77.2167));

        // 2. Fetch all active electricians from MySQL
        $electricians = Electrician::where('status', 'active')->get();

        // 3. Calculate distance for each active electrician
        $results = [];

        foreach ($electricians as $electrician) {
            $distanceKm = $this->calculateDistance(
                $customerLat,
                $customerLng,
                (float) $electrician->latitude,
                (float) $electrician->longitude
            );

            $item = $electrician->toArray();
            $item['distance_km'] = $distanceKm;
            $item['distance'] = $distanceKm . ' km away';

            $results[] = $item;
        }

        // 4. Sort by distance (nearest first)
        usort($results, function ($a, $b) {
            return $a['distance_km'] <=> $b['distance_km'];
        });

        // 5. Return JSON response
        return response()->json([
            'success' => true,
            'count' => count($results),
            'customer_location' => [
                'latitude' => $customerLat,
                'longitude' => $customerLng,
            ],
            'data' => array_values($results),
        ]);
    }

    /**
     * List nearby electricians using Google Places API (New) or fallback sample data.
     * 
     * Endpoint: GET /api/electricians?lat=28.6315&lng=77.2167&service=fan
     */
    public function index(Request $request)
    {
        // Step 1: User coordinates aur service query params read karo
        $userLat = (float) $request->query('lat', 28.6315);
        $userLng = (float) $request->query('lng', 77.2167);
        $service = strtolower(trim((string) $request->query('service', '')));

        // Step 2: .env se Google Places API Key read karo
        $apiKey = env('GOOGLE_PLACES_API_KEY');

        $electricians = [];
        $source = 'sample_data';
        $apiMessage = null;

        // Step 3: Agar Google API Key maujood hai, to Google Places API (New) call karo
        if (!empty($apiKey) && $apiKey !== 'YOUR_GOOGLE_API_KEY_HERE') {
            $googleResults = $this->fetchFromGooglePlaces($userLat, $userLng, $service, $apiKey);

            if (!empty($googleResults)) {
                $electricians = $googleResults;
                $source = 'google_places_api_new';
                $apiMessage = 'Live businesses fetched from Google Places API (New)';
            } else {
                // Agar Google API se koi result nahi mila ya error aaya, fallback to sample data
                $electricians = $this->getSampleElectricians();
                $apiMessage = 'Google API returned no results or error. Showing local sample data.';
            }
        } else {
            // Agar API key configure nahi hai to sample data use karo
            $electricians = $this->getSampleElectricians();
            $apiMessage = 'Google Places API key not set in .env. Showing local verified electricians.';
        }

        // Step 4: Har electrician ke liye user location se dynamic distance calculate karo
        $results = [];

        foreach ($electricians as $electrician) {
            $distanceKm = $this->calculateDistance($userLat, $userLng, $electrician['lat'], $electrician['lng']);

            $electrician['distance_km'] = $distanceKm;
            $electrician['distance'] = $distanceKm . ' km away';

            // Service filter (agar user ne specific service maangi ho)
            if (!empty($service) && $service !== 'all') {
                $hasMatchingSpecialty = false;
                foreach ($electrician['specialties'] as $spec) {
                    if (stripos($spec, $service) !== false) {
                        $hasMatchingSpecialty = true;
                        break;
                    }
                }

                if (!$hasMatchingSpecialty) {
                    continue; // Skip agar service match nahi hoti
                }
            }

            $results[] = $electrician;
        }

        // Step 5: Distance ke hisaab se sort karo (Sabse nazdeek wala pehle aayega)
        usort($results, function ($a, $b) {
            return $a['distance_km'] <=> $b['distance_km'];
        });

        // Step 6: Clean JSON response return karo
        return response()->json([
            'success' => true,
            'source' => $source,
            'message' => $apiMessage,
            'count' => count($results),
            'user_location' => [
                'lat' => $userLat,
                'lng' => $userLng,
            ],
            'filter_service' => $service ?: 'all',
            'data' => array_values($results),
        ]);
    }

    /**
     * Google Places API (New) - Nearby Search
     * POST https://places.googleapis.com/v1/places:searchNearby
     */
    private function fetchFromGooglePlaces(float $lat, float $lng, string $service, string $apiKey): array
    {
        try {
            $url = 'https://places.googleapis.com/v1/places:searchNearby';

            $headers = [
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $apiKey,
                // FieldMask batata hai ki Google se kaun-kaun se fields chahiye
                'X-Goog-FieldMask' => 'places.id,places.displayName,places.formattedAddress,places.location,places.rating,places.userRatingCount,places.nationalPhoneNumber',
            ];

            // Request Body: 5000 meters (5 km) radius me electrician dhoondo
            $payload = [
                'includedTypes' => ['electrician'],
                'maxResultCount' => 10,
                'locationRestriction' => [
                    'circle' => [
                        'center' => [
                            'latitude' => $lat,
                            'longitude' => $lng,
                        ],
                        'radius' => 5000.0, // 5 km radius
                    ],
                ],
            ];

            $response = Http::withHeaders($headers)->timeout(5)->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $places = $data['places'] ?? [];

                $formatted = [];
                $index = 1;

                foreach ($places as $place) {
                    $placeLat = (float) ($place['location']['latitude'] ?? $lat);
                    $placeLng = (float) ($place['location']['longitude'] ?? $lng);
                    $name = $place['displayName']['text'] ?? 'Local Electrician';

                    $formatted[] = [
                        'id' => $place['id'] ?? ('g_' . $index),
                        'name' => $name,
                        'initials' => $this->makeInitials($name),
                        'rating' => (float) ($place['rating'] ?? 4.5),
                        'reviews' => (int) ($place['userRatingCount'] ?? 25),
                        'status' => 'Verified on Google',
                        'status_class' => 'available',
                        'address' => $place['formattedAddress'] ?? '',
                        'lat' => $placeLat,
                        'lng' => $placeLng,
                        'specialties' => [
                            $service ? ucfirst($service) . ' Repair' : 'General Electrical',
                            'Wiring & Installation',
                            'Emergency Fixes',
                        ],
                        'base_price' => '₹199',
                        'phone' => $place['nationalPhoneNumber'] ?? '+91 98000 12345',
                        'experience' => 'Licensed Pro',
                    ];

                    $index++;
                }

                return $formatted;
            }

            Log::warning('Google Places API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Google Places API Exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fallback Local Sample Electricians
     */
    private function getSampleElectricians(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Rajesh Sharma',
                'initials' => 'RS',
                'rating' => 4.9,
                'reviews' => 128,
                'status' => 'Available Now',
                'status_class' => 'available',
                'lat' => 28.6360,
                'lng' => 77.2210,
                'specialties' => ['Fan Repair', 'Short Circuit', 'Modular Switches'],
                'base_price' => '₹149',
                'phone' => '+91 98765 43210',
                'experience' => '8 years',
            ],
            [
                'id' => 2,
                'name' => 'Amit Kumar',
                'initials' => 'AK',
                'rating' => 4.8,
                'reviews' => 94,
                'status' => 'Available in 15m',
                'status_class' => 'available',
                'lat' => 28.6270,
                'lng' => 77.2110,
                'specialties' => ['Switch & Socket', 'Chandelier Lighting', 'MCB Box'],
                'base_price' => '₹99',
                'phone' => '+91 98111 22334',
                'experience' => '5 years',
            ],
            [
                'id' => 3,
                'name' => 'Vikram Patel',
                'initials' => 'VP',
                'rating' => 4.9,
                'reviews' => 210,
                'status' => 'Available Now',
                'status_class' => 'available',
                'lat' => 28.6385,
                'lng' => 77.2080,
                'specialties' => ['Full House Wiring', 'Appliance Hookup', 'Emergency'],
                'base_price' => '₹499',
                'phone' => '+91 98222 33445',
                'experience' => '12 years',
            ],
            [
                'id' => 4,
                'name' => 'Suresh Verma',
                'initials' => 'SV',
                'rating' => 4.7,
                'reviews' => 76,
                'status' => 'Available Today',
                'status_class' => 'available',
                'lat' => 28.6220,
                'lng' => 77.2260,
                'specialties' => ['AC Wiring', 'Geyser Repair', 'Inverter Lines'],
                'base_price' => '₹299',
                'phone' => '+91 98333 44556',
                'experience' => '6 years',
            ],
            [
                'id' => 5,
                'name' => 'Deepak Rawat',
                'initials' => 'DR',
                'rating' => 4.8,
                'reviews' => 89,
                'status' => 'Available Now',
                'status_class' => 'available',
                'lat' => 28.6410,
                'lng' => 77.2280,
                'specialties' => ['Light Installation', 'Fan Repair', 'House Wiring'],
                'base_price' => '₹199',
                'phone' => '+91 98444 55667',
                'experience' => '7 years',
            ],
        ];
    }

    /**
     * Helper to make initials from name (e.g. "Rajesh Sharma" -> "RS")
     */
    private function makeInitials(string $name): string
    {
        $words = explode(' ', trim($name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper($w[0] ?? '');
        }
        return $initials ?: 'EL';
    }

    /**
     * Beginner-friendly distance calculator using Haversine formula (km)
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadiusKm * $c, 1);
    }
}
