<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Country;
use App\Models\Port;
use App\Models\Supplier;
use App\Models\Shipment;
use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@gsc.com'],
            [
                'name' => 'Admin GSC Risk',
                'password' => Hash::make('password'),
            ]
        );

        $testUser = User::updateOrCreate(
            ['email' => 'nadya.240180023@mhs.unimal.ac.id'],
            [
                'name' => 'nadya zahra',
                'password' => Hash::make('nadya123'),
            ]
        );

        // 2. Seed Sentiment Lexicon Words
        $posWords = ['growth', 'increase', 'profit', 'stable', 'improve', 'success', 'positive', 'gain', 'recovery', 'strong', 'expansion', 'upgrade', 'optimistic', 'advantage', 'secure', 'thrive'];
        foreach ($posWords as $w) {
            PositiveWord::updateOrCreate(['word' => $w]);
        }

        $negWords = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'conflict', 'decrease', 'loss', 'negative', 'weak', 'drop', 'recession', 'strike', 'congestion', 'storm', 'risk', 'failure', 'decline', 'sanctions', 'tension', 'tariff'];
        foreach ($negWords as $w) {
            NegativeWord::updateOrCreate(['word' => $w]);
        }

        // 3. Seed Countries
        $countries = [
            [
                'name' => 'Germany',
                'code' => 'DE',
                'capital' => 'Berlin',
                'region' => 'Europe',
                'subregion' => 'Western Europe',
                'currency' => 'Euro',
                'currency_code' => 'EUR',
                'flag' => 'https://flagcdn.com/w320/de.png',
                'latitude' => 51.165691,
                'longitude' => 10.451526,
                'population' => 83200000,
            ],
            [
                'name' => 'China',
                'code' => 'CN',
                'capital' => 'Beijing',
                'region' => 'Asia',
                'subregion' => 'Eastern Asia',
                'currency' => 'Renminbi',
                'currency_code' => 'CNY',
                'flag' => 'https://flagcdn.com/w320/cn.png',
                'latitude' => 35.86166,
                'longitude' => 104.195397,
                'population' => 1412000000,
            ],
            [
                'name' => 'Indonesia',
                'code' => 'ID',
                'capital' => 'Jakarta',
                'region' => 'Asia',
                'subregion' => 'South-Eastern Asia',
                'currency' => 'Indonesian rupiah',
                'currency_code' => 'IDR',
                'flag' => 'https://flagcdn.com/w320/id.png',
                'latitude' => -0.789275,
                'longitude' => 113.921327,
                'population' => 275000000,
            ],
            [
                'name' => 'Australia',
                'code' => 'AU',
                'capital' => 'Canberra',
                'region' => 'Oceania',
                'subregion' => 'Australia and New Zealand',
                'currency' => 'Australian dollar',
                'currency_code' => 'AUD',
                'flag' => 'https://flagcdn.com/w320/au.png',
                'latitude' => -25.274398,
                'longitude' => 133.775136,
                'population' => 26000000,
            ],
            [
                'name' => 'United States',
                'code' => 'US',
                'capital' => 'Washington, D.C.',
                'region' => 'Americas',
                'subregion' => 'Northern America',
                'currency' => 'United States dollar',
                'currency_code' => 'USD',
                'flag' => 'https://flagcdn.com/w320/us.png',
                'latitude' => 37.09024,
                'longitude' => -95.712891,
                'population' => 333000000,
            ],
            [
                'name' => 'Singapore',
                'code' => 'SG',
                'capital' => 'Singapore',
                'region' => 'Asia',
                'subregion' => 'South-Eastern Asia',
                'currency' => 'Singapore dollar',
                'currency_code' => 'SGD',
                'flag' => 'https://flagcdn.com/w320/sg.png',
                'latitude' => 1.352083,
                'longitude' => 103.819836,
                'population' => 5600000,
            ],
            [
                'name' => 'Japan',
                'code' => 'JP',
                'capital' => 'Tokyo',
                'region' => 'Asia',
                'subregion' => 'Eastern Asia',
                'currency' => 'Japanese yen',
                'currency_code' => 'JPY',
                'flag' => 'https://flagcdn.com/w320/jp.png',
                'latitude' => 36.204824,
                'longitude' => 138.252924,
                'population' => 125000000,
            ],
            [
                'name' => 'United Kingdom',
                'code' => 'GB',
                'capital' => 'London',
                'region' => 'Europe',
                'subregion' => 'Northern Europe',
                'currency' => 'British pound',
                'currency_code' => 'GBP',
                'flag' => 'https://flagcdn.com/w320/gb.png',
                'latitude' => 55.378051,
                'longitude' => -3.435973,
                'population' => 67000000,
            ]
        ];

        $countryMap = [];
        foreach ($countries as $c) {
            $created = Country::updateOrCreate(['code' => $c['code']], $c);
            $countryMap[$c['code']] = $created;
        }

        // 4. Seed Ports
        $ports = [
            ['name' => 'Port of Hamburg', 'code' => 'DEHAM', 'country_code' => 'DE', 'latitude' => 53.5394, 'longitude' => 9.9782],
            ['name' => 'Port of Shanghai', 'code' => 'CNSHA', 'country_code' => 'CN', 'latitude' => 30.6264, 'longitude' => 122.0645],
            ['name' => 'Tanjung Priok', 'code' => 'IDTPP', 'country_code' => 'ID', 'latitude' => -6.0988, 'longitude' => 106.8910],
            ['name' => 'Port of Sydney', 'code' => 'AUSYD', 'country_code' => 'AU', 'latitude' => -33.8608, 'longitude' => 151.2136],
            ['name' => 'Port of Los Angeles', 'code' => 'USLAX', 'country_code' => 'US', 'latitude' => 33.7288, 'longitude' => -118.2620],
            ['name' => 'Port of Singapore', 'code' => 'SGSGP', 'country_code' => 'SG', 'latitude' => 1.2740, 'longitude' => 103.8010],
            ['name' => 'Port of Tokyo', 'code' => 'JPTYO', 'country_code' => 'JP', 'latitude' => 35.6260, 'longitude' => 139.7820],
            ['name' => 'Port of London', 'code' => 'GBLON', 'country_code' => 'GB', 'latitude' => 51.5034, 'longitude' => 0.0538],
        ];

        foreach ($ports as $p) {
            $country = $countryMap[$p['country_code']] ?? null;
            if ($country) {
                Port::updateOrCreate(
                    ['code' => $p['code']],
                    [
                        'name' => $p['name'],
                        'country_id' => $country->id,
                        'latitude' => $p['latitude'],
                        'longitude' => $p['longitude'],
                    ]
                );
            }
        }

        // 5. Seed Suppliers
        $suppliers = [
            ['nama_supplier' => 'Rheinland Logistik GmbH', 'kode_supplier' => 'SUP-DE01', 'email' => 'contact@rheinland.de', 'telepon' => '+49 221 123456', 'alamat' => 'Köln, Germany', 'status' => 'Aktif'],
            ['nama_supplier' => 'Shenzhen Electronics Corp', 'kode_supplier' => 'SUP-CN01', 'email' => 'sales@shenzhenelec.cn', 'telepon' => '+86 755 888888', 'alamat' => 'Guangdong, China', 'status' => 'Aktif'],
            ['nama_supplier' => 'PT Nusantara Cargo', 'kode_supplier' => 'SUP-ID01', 'email' => 'info@nusantara.co.id', 'telepon' => '+62 21 555019', 'alamat' => 'Jakarta, Indonesia', 'status' => 'Aktif'],
            ['nama_supplier' => 'Pacific Trade Co.', 'kode_supplier' => 'SUP-US01', 'email' => 'orders@pactrade.com', 'telepon' => '+1 213 555012', 'alamat' => 'Los Angeles, USA', 'status' => 'Aktif'],
            ['nama_supplier' => 'Sydney Shipping Ltd', 'kode_supplier' => 'SUP-AU01', 'email' => 'ops@sydneyship.com.au', 'telepon' => '+61 2 999988', 'alamat' => 'Sydney, Australia', 'status' => 'Aktif'],
        ];

        $supplierMap = [];
        foreach ($suppliers as $s) {
            $created = Supplier::updateOrCreate(['kode_supplier' => $s['kode_supplier']], $s);
            $supplierMap[$s['kode_supplier']] = $created;
        }

        // 6. Seed Shipments
        $shipments = [
            [
                'supplier_id' => 'SUP-DE01',
                'product_name' => 'Precision Steel Pipes',
                'quantity' => 250,
                'shipping_date' => now()->subDays(5)->format('Y-m-d'),
                'estimated_arrival' => now()->addDays(10)->format('Y-m-d'),
                'status' => 'Shipping',
            ],
            [
                'supplier_id' => 'SUP-CN01',
                'product_name' => 'Microchips Bulk',
                'quantity' => 5000,
                'shipping_date' => now()->subDays(3)->format('Y-m-d'),
                'estimated_arrival' => now()->addDays(12)->format('Y-m-d'),
                'status' => 'Shipping',
            ],
            [
                'supplier_id' => 'SUP-ID01',
                'product_name' => 'Raw Rubber Material',
                'quantity' => 800,
                'shipping_date' => now()->subDays(10)->format('Y-m-d'),
                'estimated_arrival' => now()->subDays(1)->format('Y-m-d'),
                'status' => 'Arrived',
            ],
            [
                'supplier_id' => 'SUP-US01',
                'product_name' => 'Medical Equipment',
                'quantity' => 120,
                'shipping_date' => now()->addDays(2)->format('Y-m-d'),
                'estimated_arrival' => now()->addDays(20)->format('Y-m-d'),
                'status' => 'Pending',
            ],
            [
                'supplier_id' => 'SUP-AU01',
                'product_name' => 'Lithium Ore',
                'quantity' => 1500,
                'shipping_date' => now()->subDays(7)->format('Y-m-d'),
                'estimated_arrival' => now()->addDays(7)->format('Y-m-d'),
                'status' => 'Delayed',
            ]
        ];

        foreach ($shipments as $sh) {
            $supplier = $supplierMap[$sh['supplier_id']] ?? null;
            if ($supplier) {
                Shipment::create([
                    'supplier_id' => $supplier->id,
                    'product_name' => $sh['product_name'],
                    'quantity' => $sh['quantity'],
                    'shipping_date' => $sh['shipping_date'],
                    'estimated_arrival' => $sh['estimated_arrival'],
                    'status' => $sh['status'],
                ]);
            }
        }
    }
}
