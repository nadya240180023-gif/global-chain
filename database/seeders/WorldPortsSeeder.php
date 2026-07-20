<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Port;
use App\Models\Country;

class WorldPortsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $portsData = [
            // Asia
            ['name' => 'Port of Shanghai', 'code' => 'CN SHA', 'country' => 'China', 'latitude' => 31.2222, 'longitude' => 121.5033],
            ['name' => 'Port of Singapore', 'code' => 'SG SIN', 'country' => 'Singapore', 'latitude' => 1.2640, 'longitude' => 103.8400],
            ['name' => 'Port of Shenzhen', 'code' => 'CN SZX', 'country' => 'China', 'latitude' => 22.5020, 'longitude' => 113.8920],
            ['name' => 'Port of Ningbo-Zhoushan', 'code' => 'CN NGB', 'country' => 'China', 'latitude' => 29.9392, 'longitude' => 121.9056],
            ['name' => 'Port of Guangzhou', 'code' => 'CN CAN', 'country' => 'China', 'latitude' => 22.7567, 'longitude' => 113.6050],
            ['name' => 'Port of Busan', 'code' => 'KR PUS', 'country' => 'South Korea', 'latitude' => 35.1017, 'longitude' => 129.0400],
            ['name' => 'Port of Hong Kong', 'code' => 'HK HKG', 'country' => 'China', 'latitude' => 22.3333, 'longitude' => 114.1333],
            ['name' => 'Port of Qingdao', 'code' => 'CN TAO', 'country' => 'China', 'latitude' => 36.0833, 'longitude' => 120.3167],
            ['name' => 'Port of Tianjin', 'code' => 'CN TSN', 'country' => 'China', 'latitude' => 38.9833, 'longitude' => 117.7333],
            ['name' => 'Port of Jebel Ali (Dubai)', 'code' => 'AE JEA', 'country' => 'United Arab Emirates', 'latitude' => 24.9857, 'longitude' => 55.0273],
            ['name' => 'Port Klang', 'code' => 'MY PKG', 'country' => 'Malaysia', 'latitude' => 3.0000, 'longitude' => 101.4000],
            ['name' => 'Port of Tanjung Pelepas', 'code' => 'MY TPP', 'country' => 'Malaysia', 'latitude' => 1.3667, 'longitude' => 103.5500],
            ['name' => 'Port of Kaohsiung', 'code' => 'TW KHH', 'country' => 'Taiwan', 'latitude' => 22.5667, 'longitude' => 120.3000],
            ['name' => 'Port of Xiamen', 'code' => 'CN XMN', 'country' => 'China', 'latitude' => 24.4833, 'longitude' => 118.0667],
            ['name' => 'Port of Dalian', 'code' => 'CN DLC', 'country' => 'China', 'latitude' => 38.9333, 'longitude' => 121.6500],
            ['name' => 'Port of Yokohama', 'code' => 'JP YOK', 'country' => 'Japan', 'latitude' => 35.4500, 'longitude' => 139.6667],
            ['name' => 'Port of Tokyo', 'code' => 'JP TYO', 'country' => 'Japan', 'latitude' => 35.6167, 'longitude' => 139.7667],
            ['name' => 'Port of Colombo', 'code' => 'LK CMB', 'country' => 'Sri Lanka', 'latitude' => 6.9500, 'longitude' => 79.8500],
            ['name' => 'Jawaharlal Nehru Port (Nhava Sheva)', 'code' => 'IN NSA', 'country' => 'India', 'latitude' => 18.9500, 'longitude' => 72.9500],
            ['name' => 'Port of Tanjung Priok (Jakarta)', 'code' => 'ID TPP', 'country' => 'Indonesia', 'latitude' => -6.1000, 'longitude' => 106.8833],
            ['name' => 'Port of Ho Chi Minh City', 'code' => 'VN SGN', 'country' => 'Vietnam', 'latitude' => 10.7500, 'longitude' => 106.7000],

            // Europe
            ['name' => 'Port of Rotterdam', 'code' => 'NL RTM', 'country' => 'Netherlands', 'latitude' => 51.9500, 'longitude' => 4.0500],
            ['name' => 'Port of Antwerp', 'code' => 'BE ANR', 'country' => 'Belgium', 'latitude' => 51.3000, 'longitude' => 4.3167],
            ['name' => 'Port of Hamburg', 'code' => 'DE HAM', 'country' => 'Germany', 'latitude' => 53.5333, 'longitude' => 9.9667],
            ['name' => 'Port of Bremerhaven', 'code' => 'DE BRV', 'country' => 'Germany', 'latitude' => 53.5667, 'longitude' => 8.5667],
            ['name' => 'Port of Valencia', 'code' => 'ES VLC', 'country' => 'Spain', 'latitude' => 39.4333, 'longitude' => -0.3167],
            ['name' => 'Port of Algeciras', 'code' => 'ES ALG', 'country' => 'Spain', 'latitude' => 36.1333, 'longitude' => -5.4333],
            ['name' => 'Port of Felixstowe', 'code' => 'GB FXT', 'country' => 'United Kingdom', 'latitude' => 51.9500, 'longitude' => 1.3167],
            ['name' => 'Port of Piraeus', 'code' => 'GR PIR', 'country' => 'Greece', 'latitude' => 37.9500, 'longitude' => 23.6333],
            ['name' => 'Port of Gioia Tauro', 'code' => 'IT GIT', 'country' => 'Italy', 'latitude' => 38.4667, 'longitude' => 15.9000],
            ['name' => 'Port of Le Havre', 'code' => 'FR LEH', 'country' => 'France', 'latitude' => 49.4833, 'longitude' => 0.1167],

            // Americas
            ['name' => 'Port of Los Angeles', 'code' => 'US LAX', 'country' => 'United States', 'latitude' => 33.7288, 'longitude' => -118.2620],
            ['name' => 'Port of Long Beach', 'code' => 'US LGB', 'country' => 'United States', 'latitude' => 33.7542, 'longitude' => -118.2155],
            ['name' => 'Port of New York and New Jersey', 'code' => 'US NYC', 'country' => 'United States', 'latitude' => 40.6667, 'longitude' => -74.0500],
            ['name' => 'Port of Savannah', 'code' => 'US SAV', 'country' => 'United States', 'latitude' => 32.1167, 'longitude' => -81.1500],
            ['name' => 'Port of Houston', 'code' => 'US HOU', 'country' => 'United States', 'latitude' => 29.7167, 'longitude' => -95.2667],
            ['name' => 'Port of Seattle', 'code' => 'US SEA', 'country' => 'United States', 'latitude' => 47.6000, 'longitude' => -122.3500],
            ['name' => 'Port of Vancouver', 'code' => 'CA VAN', 'country' => 'Canada', 'latitude' => 49.2833, 'longitude' => -123.1167],
            ['name' => 'Port of Santos', 'code' => 'BR SSZ', 'country' => 'Brazil', 'latitude' => -23.9667, 'longitude' => -46.3000],
            ['name' => 'Port of Manzanillo', 'code' => 'MX ZLO', 'country' => 'Mexico', 'latitude' => 19.0667, 'longitude' => -104.3000],
            ['name' => 'Port of Callao', 'code' => 'PE CLL', 'country' => 'Peru', 'latitude' => -12.0500, 'longitude' => -77.1500],
            ['name' => 'Port of Cartagena', 'code' => 'CO CTG', 'country' => 'Colombia', 'latitude' => 10.4000, 'longitude' => -75.5333],
            ['name' => 'Port of Buenos Aires', 'code' => 'AR BUE', 'country' => 'Argentina', 'latitude' => -34.5833, 'longitude' => -58.3667],
            
            // Middle East & Africa
            ['name' => 'Port of Jeddah', 'code' => 'SA JED', 'country' => 'Saudi Arabia', 'latitude' => 21.4667, 'longitude' => 39.1667],
            ['name' => 'Port of Salalah', 'code' => 'OM SLL', 'country' => 'Oman', 'latitude' => 16.9333, 'longitude' => 54.0000],
            ['name' => 'Port of Durban', 'code' => 'ZA DUR', 'country' => 'South Africa', 'latitude' => -29.8667, 'longitude' => 31.0333],
            ['name' => 'Port Said', 'code' => 'EG PSD', 'country' => 'Egypt', 'latitude' => 31.2667, 'longitude' => 32.3167],
            ['name' => 'Port of Tanger Med', 'code' => 'MA PTA', 'country' => 'Morocco', 'latitude' => 35.8833, 'longitude' => -5.5000],
            ['name' => 'Port of Lagos (Apapa)', 'code' => 'NG LOS', 'country' => 'Nigeria', 'latitude' => 6.4500, 'longitude' => 3.3667],
            
            // Oceania
            ['name' => 'Port of Melbourne', 'code' => 'AU MEL', 'country' => 'Australia', 'latitude' => -37.8167, 'longitude' => 144.9167],
            ['name' => 'Port of Sydney (Botany)', 'code' => 'AU SYD', 'country' => 'Australia', 'latitude' => -33.9667, 'longitude' => 151.2167],
            ['name' => 'Port of Brisbane', 'code' => 'AU BNE', 'country' => 'Australia', 'latitude' => -27.3833, 'longitude' => 153.1667],
            ['name' => 'Port of Auckland', 'code' => 'NZ AKL', 'country' => 'New Zealand', 'latitude' => -36.8333, 'longitude' => 174.7833],
        ];

        // Retrieve existing countries to match IDs
        $countries = Country::all()->keyBy('name');

        foreach ($portsData as $port) {
            $country = $countries->get($port['country']);
            if ($country) {
                Port::updateOrCreate(
                    ['code' => $port['code']], // unique identifier
                    [
                        'name' => $port['name'],
                        'country_id' => $country->id,
                        'latitude' => $port['latitude'],
                        'longitude' => $port['longitude'],
                    ]
                );
            }
        }
    }
}
