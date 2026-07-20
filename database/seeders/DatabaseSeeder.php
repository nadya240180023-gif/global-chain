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
            // ── ORIGINAL 8 ──
            ['name'=>'Germany','code'=>'DE','capital'=>'Berlin','region'=>'Europe','subregion'=>'Western Europe','currency'=>'Euro','currency_code'=>'EUR','flag'=>'https://flagcdn.com/w320/de.png','latitude'=>51.165691,'longitude'=>10.451526,'population'=>83200000],
            ['name'=>'China','code'=>'CN','capital'=>'Beijing','region'=>'Asia','subregion'=>'Eastern Asia','currency'=>'Renminbi','currency_code'=>'CNY','flag'=>'https://flagcdn.com/w320/cn.png','latitude'=>35.86166,'longitude'=>104.195397,'population'=>1412000000],
            ['name'=>'Indonesia','code'=>'ID','capital'=>'Jakarta','region'=>'Asia','subregion'=>'South-Eastern Asia','currency'=>'Indonesian rupiah','currency_code'=>'IDR','flag'=>'https://flagcdn.com/w320/id.png','latitude'=>-0.789275,'longitude'=>113.921327,'population'=>275000000],
            ['name'=>'Australia','code'=>'AU','capital'=>'Canberra','region'=>'Oceania','subregion'=>'Australia and New Zealand','currency'=>'Australian dollar','currency_code'=>'AUD','flag'=>'https://flagcdn.com/w320/au.png','latitude'=>-25.274398,'longitude'=>133.775136,'population'=>26000000],
            ['name'=>'United States','code'=>'US','capital'=>'Washington, D.C.','region'=>'Americas','subregion'=>'Northern America','currency'=>'United States dollar','currency_code'=>'USD','flag'=>'https://flagcdn.com/w320/us.png','latitude'=>37.09024,'longitude'=>-95.712891,'population'=>333000000],
            ['name'=>'Singapore','code'=>'SG','capital'=>'Singapore','region'=>'Asia','subregion'=>'South-Eastern Asia','currency'=>'Singapore dollar','currency_code'=>'SGD','flag'=>'https://flagcdn.com/w320/sg.png','latitude'=>1.352083,'longitude'=>103.819836,'population'=>5600000],
            ['name'=>'Japan','code'=>'JP','capital'=>'Tokyo','region'=>'Asia','subregion'=>'Eastern Asia','currency'=>'Japanese yen','currency_code'=>'JPY','flag'=>'https://flagcdn.com/w320/jp.png','latitude'=>36.204824,'longitude'=>138.252924,'population'=>125000000],
            ['name'=>'United Kingdom','code'=>'GB','capital'=>'London','region'=>'Europe','subregion'=>'Northern Europe','currency'=>'British pound','currency_code'=>'GBP','flag'=>'https://flagcdn.com/w320/gb.png','latitude'=>55.378051,'longitude'=>-3.435973,'population'=>67000000],

            // ── ASIA ──
            ['name'=>'India','code'=>'IN','capital'=>'New Delhi','region'=>'Asia','subregion'=>'Southern Asia','currency'=>'Indian rupee','currency_code'=>'INR','flag'=>'https://flagcdn.com/w320/in.png','latitude'=>20.593684,'longitude'=>78.962880,'population'=>1428000000],
            ['name'=>'South Korea','code'=>'KR','capital'=>'Seoul','region'=>'Asia','subregion'=>'Eastern Asia','currency'=>'South Korean won','currency_code'=>'KRW','flag'=>'https://flagcdn.com/w320/kr.png','latitude'=>35.907757,'longitude'=>127.766922,'population'=>51700000],
            ['name'=>'Malaysia','code'=>'MY','capital'=>'Kuala Lumpur','region'=>'Asia','subregion'=>'South-Eastern Asia','currency'=>'Malaysian ringgit','currency_code'=>'MYR','flag'=>'https://flagcdn.com/w320/my.png','latitude'=>4.210484,'longitude'=>101.975766,'population'=>33000000],
            ['name'=>'Thailand','code'=>'TH','capital'=>'Bangkok','region'=>'Asia','subregion'=>'South-Eastern Asia','currency'=>'Thai baht','currency_code'=>'THB','flag'=>'https://flagcdn.com/w320/th.png','latitude'=>15.870032,'longitude'=>100.992541,'population'=>70000000],
            ['name'=>'Vietnam','code'=>'VN','capital'=>'Hanoi','region'=>'Asia','subregion'=>'South-Eastern Asia','currency'=>'Vietnamese dong','currency_code'=>'VND','flag'=>'https://flagcdn.com/w320/vn.png','latitude'=>14.058324,'longitude'=>108.277199,'population'=>98000000],
            ['name'=>'Saudi Arabia','code'=>'SA','capital'=>'Riyadh','region'=>'Asia','subregion'=>'Western Asia','currency'=>'Saudi riyal','currency_code'=>'SAR','flag'=>'https://flagcdn.com/w320/sa.png','latitude'=>23.885942,'longitude'=>45.079162,'population'=>35000000],
            ['name'=>'United Arab Emirates','code'=>'AE','capital'=>'Abu Dhabi','region'=>'Asia','subregion'=>'Western Asia','currency'=>'UAE dirham','currency_code'=>'AED','flag'=>'https://flagcdn.com/w320/ae.png','latitude'=>23.424076,'longitude'=>53.847818,'population'=>9800000],
            ['name'=>'Turkey','code'=>'TR','capital'=>'Ankara','region'=>'Asia','subregion'=>'Western Asia','currency'=>'Turkish lira','currency_code'=>'TRY','flag'=>'https://flagcdn.com/w320/tr.png','latitude'=>38.963745,'longitude'=>35.243322,'population'=>85000000],

            // ── EUROPE ──
            ['name'=>'France','code'=>'FR','capital'=>'Paris','region'=>'Europe','subregion'=>'Western Europe','currency'=>'Euro','currency_code'=>'EUR','flag'=>'https://flagcdn.com/w320/fr.png','latitude'=>46.227638,'longitude'=>2.213749,'population'=>67750000],
            ['name'=>'Netherlands','code'=>'NL','capital'=>'Amsterdam','region'=>'Europe','subregion'=>'Western Europe','currency'=>'Euro','currency_code'=>'EUR','flag'=>'https://flagcdn.com/w320/nl.png','latitude'=>52.132633,'longitude'=>5.291266,'population'=>17700000],
            ['name'=>'Italy','code'=>'IT','capital'=>'Rome','region'=>'Europe','subregion'=>'Southern Europe','currency'=>'Euro','currency_code'=>'EUR','flag'=>'https://flagcdn.com/w320/it.png','latitude'=>41.871940,'longitude'=>12.567380,'population'=>59000000],
            ['name'=>'Spain','code'=>'ES','capital'=>'Madrid','region'=>'Europe','subregion'=>'Southern Europe','currency'=>'Euro','currency_code'=>'EUR','flag'=>'https://flagcdn.com/w320/es.png','latitude'=>40.463667,'longitude'=>-3.749220,'population'=>47000000],
            ['name'=>'Sweden','code'=>'SE','capital'=>'Stockholm','region'=>'Europe','subregion'=>'Northern Europe','currency'=>'Swedish krona','currency_code'=>'SEK','flag'=>'https://flagcdn.com/w320/se.png','latitude'=>60.128161,'longitude'=>18.643501,'population'=>10500000],
            ['name'=>'Russia','code'=>'RU','capital'=>'Moscow','region'=>'Europe','subregion'=>'Eastern Europe','currency'=>'Russian ruble','currency_code'=>'RUB','flag'=>'https://flagcdn.com/w320/ru.png','latitude'=>61.524010,'longitude'=>105.318756,'population'=>143000000],

            // ── AMERICAS ──
            ['name'=>'Brazil','code'=>'BR','capital'=>'Brasília','region'=>'Americas','subregion'=>'South America','currency'=>'Brazilian real','currency_code'=>'BRL','flag'=>'https://flagcdn.com/w320/br.png','latitude'=>-14.235004,'longitude'=>-51.925280,'population'=>215000000],
            ['name'=>'Canada','code'=>'CA','capital'=>'Ottawa','region'=>'Americas','subregion'=>'Northern America','currency'=>'Canadian dollar','currency_code'=>'CAD','flag'=>'https://flagcdn.com/w320/ca.png','latitude'=>56.130366,'longitude'=>-106.346771,'population'=>38000000],
            ['name'=>'Mexico','code'=>'MX','capital'=>'Mexico City','region'=>'Americas','subregion'=>'Central America','currency'=>'Mexican peso','currency_code'=>'MXN','flag'=>'https://flagcdn.com/w320/mx.png','latitude'=>23.634501,'longitude'=>-102.552784,'population'=>130000000],

            // ── AFRICA ──
            ['name'=>'South Africa','code'=>'ZA','capital'=>'Pretoria','region'=>'Africa','subregion'=>'Southern Africa','currency'=>'South African rand','currency_code'=>'ZAR','flag'=>'https://flagcdn.com/w320/za.png','latitude'=>-30.559482,'longitude'=>22.937506,'population'=>60000000],
            ['name'=>'Nigeria','code'=>'NG','capital'=>'Abuja','region'=>'Africa','subregion'=>'Western Africa','currency'=>'Nigerian naira','currency_code'=>'NGN','flag'=>'https://flagcdn.com/w320/ng.png','latitude'=>9.081999,'longitude'=>8.675277,'population'=>220000000],
            ['name'=>'Egypt','code'=>'EG','capital'=>'Cairo','region'=>'Africa','subregion'=>'Northern Africa','currency'=>'Egyptian pound','currency_code'=>'EGP','flag'=>'https://flagcdn.com/w320/eg.png','latitude'=>26.820553,'longitude'=>30.802498,'population'=>104000000],
        ];


        $countryMap = [];
        foreach ($countries as $c) {
            $created = Country::updateOrCreate(['code' => $c['code']], $c);
            $countryMap[$c['code']] = $created;
        }

        // 4. Seed Ports
        $ports = [
            // Original
            ['name'=>'Port of Hamburg',      'code'=>'DEHAM', 'country_code'=>'DE', 'latitude'=>53.5394,   'longitude'=>9.9782],
            ['name'=>'Port of Shanghai',     'code'=>'CNSHA', 'country_code'=>'CN', 'latitude'=>30.6264,   'longitude'=>122.0645],
            ['name'=>'Tanjung Priok',        'code'=>'IDTPP', 'country_code'=>'ID', 'latitude'=>-6.0988,   'longitude'=>106.8910],
            ['name'=>'Port of Sydney',       'code'=>'AUSYD', 'country_code'=>'AU', 'latitude'=>-33.8608,  'longitude'=>151.2136],
            ['name'=>'Port of Los Angeles',  'code'=>'USLAX', 'country_code'=>'US', 'latitude'=>33.7288,   'longitude'=>-118.2620],
            ['name'=>'Port of Singapore',    'code'=>'SGSGP', 'country_code'=>'SG', 'latitude'=>1.2740,    'longitude'=>103.8010],
            ['name'=>'Port of Tokyo',        'code'=>'JPTYO', 'country_code'=>'JP', 'latitude'=>35.6260,   'longitude'=>139.7820],
            ['name'=>'Port of London',       'code'=>'GBLON', 'country_code'=>'GB', 'latitude'=>51.5034,   'longitude'=>0.0538],
            // New
            ['name'=>'Port of Mumbai',       'code'=>'INBOM', 'country_code'=>'IN', 'latitude'=>18.9333,   'longitude'=>72.8333],
            ['name'=>'Port of Busan',        'code'=>'KRBSN', 'country_code'=>'KR', 'latitude'=>35.0958,   'longitude'=>128.9764],
            ['name'=>'Port Klang',           'code'=>'MYPKG', 'country_code'=>'MY', 'latitude'=>3.0000,    'longitude'=>101.4000],
            ['name'=>'Laem Chabang Port',    'code'=>'THLCB', 'country_code'=>'TH', 'latitude'=>13.0870,   'longitude'=>100.8800],
            ['name'=>'Port of Ho Chi Minh',  'code'=>'VNHCM', 'country_code'=>'VN', 'latitude'=>10.7769,   'longitude'=>106.6890],
            ['name'=>'Port of Jeddah',       'code'=>'SAJED', 'country_code'=>'SA', 'latitude'=>21.4858,   'longitude'=>39.1925],
            ['name'=>'Port of Jebel Ali',    'code'=>'AEJEA', 'country_code'=>'AE', 'latitude'=>24.9852,   'longitude'=>55.0660],
            ['name'=>'Port of Mersin',       'code'=>'TRMER', 'country_code'=>'TR', 'latitude'=>36.8000,   'longitude'=>34.6333],
            ['name'=>'Port of Le Havre',     'code'=>'FRLEH', 'country_code'=>'FR', 'latitude'=>49.4883,   'longitude'=>0.1069],
            ['name'=>'Port of Rotterdam',    'code'=>'NLRTM', 'country_code'=>'NL', 'latitude'=>51.9225,   'longitude'=>4.4792],
            ['name'=>'Port of Genoa',        'code'=>'ITGOA', 'country_code'=>'IT', 'latitude'=>44.4056,   'longitude'=>8.9463],
            ['name'=>'Port of Barcelona',    'code'=>'ESBCN', 'country_code'=>'ES', 'latitude'=>41.3851,   'longitude'=>2.1734],
            ['name'=>'Port of Gothenburg',   'code'=>'SEGOT', 'country_code'=>'SE', 'latitude'=>57.7072,   'longitude'=>11.9668],
            ['name'=>'Port of Novorossiysk', 'code'=>'RUNVS', 'country_code'=>'RU', 'latitude'=>44.7239,   'longitude'=>37.7663],
            ['name'=>'Port of Santos',       'code'=>'BRSSZ', 'country_code'=>'BR', 'latitude'=>-23.9500,  'longitude'=>-46.3389],
            ['name'=>'Port of Vancouver',    'code'=>'CAVAN', 'country_code'=>'CA', 'latitude'=>49.2827,   'longitude'=>-123.1207],
            ['name'=>'Port of Veracruz',     'code'=>'MXVER', 'country_code'=>'MX', 'latitude'=>19.2000,   'longitude'=>-96.1333],
            ['name'=>'Port of Durban',       'code'=>'ZADUR', 'country_code'=>'ZA', 'latitude'=>-29.8587,  'longitude'=>31.0218],
            ['name'=>'Port of Lagos',        'code'=>'NGLOS', 'country_code'=>'NG', 'latitude'=>6.4531,    'longitude'=>3.3958],
            ['name'=>'Port of Alexandria',   'code'=>'EGALX', 'country_code'=>'EG', 'latitude'=>31.2001,   'longitude'=>29.9187],
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
