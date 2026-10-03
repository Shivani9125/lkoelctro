<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Electrician;

class ElectricianSeeder extends Seeder
{
    /**
     * Run database seeds for Lucknow areas with 20 records for each major area.
     */
    public function run(): void
    {
        // 6 Major areas in Lucknow with base coordinates
        $areas = [
            'Hazratganj' => [
                'base_lat' => 26.8467,
                'base_lng' => 80.9462,
                'landmarks' => [
                    'Near Mayfair Cinema, MG Marg',
                    'Near Halwasiya Market',
                    'Janpath Market, Hazratganj',
                    'Near GPO, Hazratganj',
                    'Shahnajaf Road, Hazratganj',
                    'Near Capitol Cinema, Vidhan Sabha Marg',
                    'Habibullah Estate, Hazratganj',
                    'Near Multilevel Parking, Hazratganj',
                    'Sapru Marg, Near Sahara India Tower',
                    'Rani Laxmi Bai Marg',
                    'Near Cathedral School, MG Marg',
                    'Naval Kishore Road',
                    'Near Lalbagh Crossing',
                    'Near Carlton Hotel, Shahnajaf Road',
                    'Near Prince Market, Hazratganj',
                    'Near Sikandar Bagh Crossing',
                    'Near Clarks Avadh, MG Marg',
                    'Near Hazratganj Chauraha',
                    'Near Governor House Road',
                    'Behind Sahu Cinema, Hazratganj'
                ]
            ],
            'Gomti Nagar' => [
                'base_lat' => 26.8530,
                'base_lng' => 80.9980,
                'landmarks' => [
                    'Patrakarpuram Chauraha, Gomti Nagar',
                    'Vibhuti Khand, Near Wave Mall',
                    'Vipin Khand, Near Taj Hotel',
                    'Manoj Pandey Chauraha, Gomti Nagar',
                    'Vinay Khand 3, Gomti Nagar',
                    'Vikas Khand 1, Near Police Station',
                    'Vishal Khand 2, Near CMS School',
                    'Vivek Khand 4, Gomti Nagar',
                    'Near Mithaiwala Chauraha, Gomti Nagar',
                    'Near Husariya Chauraha, Gomti Nagar',
                    'Viram Khand 5, Gomti Nagar',
                    'Near One Awadh Center Mall, Vibhuti Khand',
                    'Near Cinepolis, Gomti Nagar',
                    'Gomti Nagar Railway Station Road',
                    'Near Dayal Paradise, Vipul Khand',
                    'Vastu Khand, Near Amity University',
                    'Near City Mall, Gomti Nagar',
                    'Near Hahnemann Chauraha, Gomti Nagar',
                    'Sector 4, Gomti Nagar Extension',
                    'Near Riverfront, Gomti Nagar'
                ]
            ],
            'Aliganj' => [
                'base_lat' => 26.8920,
                'base_lng' => 80.9380,
                'landmarks' => [
                    'Near Kapoorthala Chauraha, Aliganj',
                    'Sector B, Near Aliganj Post Office',
                    'Sector Q, Near Novelty Cinema',
                    'Sector C, Near Engineering College Chauraha',
                    'Near Purania Chauraha, Aliganj',
                    'Sector H, Near Central School',
                    'Near Hanuman Mandir, Sector A',
                    'Sector M, Near Aliganj Thana',
                    'Near Dandiya Bazar, Aliganj',
                    'Sector J, Near City Montessori School',
                    'Near Pragati Kendra, Sector G',
                    'Sector D, Near SBI Bank, Aliganj',
                    'Near Sahara Hospital Road, Aliganj',
                    'Near Sector L Market, Aliganj',
                    'Sector K, Near Ram Ram Bank Chauraha',
                    'Near Ring Road Crossing, Aliganj',
                    'Near Chhota Imambara Road, Aliganj',
                    'Near Mini Stadium, Sector G',
                    'Sector N, Near Water Tank, Aliganj',
                    'Near Kapoorthala Shopping Complex'
                ]
            ],
            'Indira Nagar' => [
                'base_lat' => 26.8780,
                'base_lng' => 80.9850,
                'landmarks' => [
                    'Near Bhootnath Market, Indira Nagar',
                    'Sector 14, Near Munshi Pulia Chauraha',
                    'Sector 11, Near Kaleva Sweets',
                    'Sector 16, Near Ring Road',
                    'Near Shalimar Square, Indira Nagar',
                    'Sector 8, Near Church Road',
                    'Sector 18, Near Shiv Mandir',
                    'Sector 21, Near Manas Enclave',
                    'Sector 9, Near Takrohi Road',
                    'Near Picnic Spot Road, Indira Nagar',
                    'Sector 19, Near Amrapali Market',
                    'Sector 22, Near polytechnic Chauraha',
                    'Sector 12, Near Awas Vikas Colony',
                    'Sector 10, Near Meena Market',
                    'Near Bhootnath Metro Station',
                    'Sector 15, Near City International School',
                    'Sector 20, Near Sector 20 Market',
                    'Near Lekhraj Market, Indira Nagar',
                    'Sector 17, Near Pani Ki Tanki',
                    'Near Munshi Pulia Metro Station'
                ]
            ],
            'Alambagh' => [
                'base_lat' => 26.8150,
                'base_lng' => 80.9100,
                'landmarks' => [
                    'Near Alambagh Bus Stand Chauraha',
                    'Near Chander Nagar Market, Alambagh',
                    'Singar Nagar, Near Metro Station',
                    'Near Phoenix United Mall, Kanpur Road',
                    'Sujanpura, Near Railway Colony',
                    'Near Mawaiya Chauraha, Alambagh',
                    'Near Nattha Chauraha, Alambagh',
                    'Geeta Palli, Near VIP Road',
                    'Near Alambagh Police Station',
                    'Near Krishna Nagar Crossing',
                    'Near Anand Nagar Market, Alambagh',
                    'Near Guru Nanak Market, Alambagh',
                    'Near Chhota Barha, Alambagh',
                    'Near Avadh Hospital Chauraha',
                    'Near Sindhi School, Alambagh',
                    'Near Manak Nagar Station Road',
                    'Near LDA Colony, Alambagh',
                    'Near Sanjay Gandhi Puram, Alambagh',
                    'Near Ram Nagar, Alambagh',
                    'Near Sneh Nagar, Alambagh'
                ]
            ],
            'Chowk' => [
                'base_lat' => 26.8680,
                'base_lng' => 80.9020,
                'landmarks' => [
                    'Near Gol Darwaza, Chowk',
                    'Near Akbari Gate, Chowk',
                    'Near Victoria Street, Chowk',
                    'Near Medical College Chauraha (KGMU)',
                    'Near Rumi Darwaza, Husainabad',
                    'Near Bada Imambara Road, Chowk',
                    'Near Tunday Kababi Gali, Chowk',
                    'Kona Chauraha, Chowk',
                    'Near Nakhas Chauraha, Chowk',
                    'Near Nimbu Park, Chowk',
                    'Near Raja Bazaar, Chowk',
                    'Near Tehseen Masjid, Chowk',
                    'Near Campbell Road Crossing, Chowk',
                    'Near Phool Wali Gali, Chowk',
                    'Near Sarai Mali Khan, Chowk',
                    'Near Chowk Thana Market',
                    'Near Haiderganj Chauraha, Chowk',
                    'Near Shah Meena Road, Chowk',
                    'Near Subhash Marg, Chowk',
                    'Near Machhi Bhavan, Chowk'
                ]
            ],
        ];

        $firstNames = [
            'Rajesh', 'Suresh', 'Amit', 'Manoj', 'Dinesh',
            'Vikram', 'Ramesh', 'Sunil', 'Pankaj', 'Deepak',
            'Anil', 'Sanjay', 'Santosh', 'Vinod', 'Mahesh',
            'Pramod', 'Ashok', 'Ajay', 'Rakesh', 'Pradeep'
        ];

        $lastNames = [
            'Verma', 'Sharma', 'Shukla', 'Tiwari', 'Gupta',
            'Mishra', 'Pandey', 'Yadav', 'Singh', 'Srivastava',
            'Tripathi', 'Kumar', 'Dubey', 'Chaurasia', 'Dixit',
            'Maurya', 'Saxena', 'Rathore', 'Pathak', 'Chauhan'
        ];

        $businessTypes = [
            'Electrical Works',
            'Electrician & Wireman',
            'Power Solutions',
            'Electricals & Maintenance',
            'Quick Electrical Services',
            'Home Electrician Service',
            'Expert Electrical Care'
        ];

        // Ensure clear prior data if re-seeding
        Electrician::truncate();

        $phoneCounter = 9812340001;

        foreach ($areas as $areaName => $areaData) {
            $baseLat = $areaData['base_lat'];
            $baseLng = $areaData['base_lng'];
            $landmarks = $areaData['landmarks'];

            for ($i = 0; $i < 20; $i++) {
                $fName = $firstNames[$i % count($firstNames)];
                $lName = $lastNames[$i % count($lastNames)];
                $bType = $businessTypes[$i % count($businessTypes)];

                // Vary names between personal pro and shop name
                if ($i % 3 === 0) {
                    $fullName = "{$fName} {$lName} ({$bType})";
                } elseif ($i % 3 === 1) {
                    $fullName = "{$lName} {$bType}";
                } else {
                    $fullName = "{$fName} {$lName}";
                }

                // Create realistic, non-overlapping coordinates in the area (~100m to 1.5km spread)
                $latOffset = (sin($i * 1.3) * 0.007) + (($i % 4 - 1.5) * 0.003);
                $lngOffset = (cos($i * 1.5) * 0.008) + (($i % 5 - 2) * 0.002);

                $phone = '+91 ' . ($phoneCounter++);
                $email = strtolower($fName . '.' . $lName . $i . '@electrofix.in');
                $address = $landmarks[$i] . ', Lucknow, Uttar Pradesh';

                Electrician::create([
                    'name' => $fullName,
                    'phone' => $phone,
                    'email' => $email,
                    'address' => $address,
                    'area' => $areaName,
                    'latitude' => round($baseLat + $latOffset, 7),
                    'longitude' => round($baseLng + $lngOffset, 7),
                    'status' => 'active',
                ]);
            }
        }
    }
}
