<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->query('search');
        $countryId = $request->query('country_id');

        $query = Port::with('country');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        $ports     = $query->get();
        $countries = Country::orderBy('name')->get();

        // ── DB ports for table / cards ──
        $mapPorts = $ports->map(function ($p) {
            return [
                'name'         => $p->name,
                'code'         => $p->code,
                'latitude'     => floatval($p->latitude),
                'longitude'    => floatval($p->longitude),
                'country_name' => optional($p->country)->name ?? '',
                'country_code' => optional($p->country)->code ?? '',
                'region'       => $this->getRegionByCode(optional($p->country)->code ?? ''),
                'type'         => 'database',
            ];
        })->values()->toArray();

        // ── Merge DB + global data for the world map ──
        $globalData  = $this->getGlobalPortData();
        $dbCodes     = array_column($mapPorts, 'code');
        $supplemental = array_values(array_filter($globalData, fn($p) => !in_array($p['code'], $dbCodes)));
        $allMapPorts = array_merge($mapPorts, $supplemental);

        return view('ports.index', compact('ports', 'countries', 'mapPorts', 'allMapPorts', 'search', 'countryId'));
    }

    public function worldMap(Request $request)
    {
        // Fetch ports from DB
        $dbPorts = Port::with('country')->get()->map(function ($p) {
            return [
                'id'           => $p->id,
                'name'         => $p->name,
                'code'         => $p->code,
                'latitude'     => floatval($p->latitude),
                'longitude'    => floatval($p->longitude),
                'country_name' => optional($p->country)->name ?? 'Unknown',
                'country_code' => optional($p->country)->code ?? '',
                'region'       => $this->getRegionByCode(optional($p->country)->code ?? ''),
                'type'         => 'database',
            ];
        })->values()->toArray();

        // Comprehensive world port dataset (fallback + supplemental global data)
        $globalPorts = $this->getGlobalPortData();

        // Merge: DB ports take precedence; fill with global data for coverage
        $dbCodes = array_column($dbPorts, 'code');
        $supplemental = array_filter($globalPorts, fn($p) => !in_array($p['code'], $dbCodes));

        $allPorts = array_merge($dbPorts, array_values($supplemental));

        // Statistics
        $totalPorts    = count($allPorts);
        $totalCountries = count(array_unique(array_column($allPorts, 'country_code')));
        $regionCounts  = array_count_values(array_column($allPorts, 'region'));

        $countries = Country::orderBy('name')->get();

        return view('ports.world_map', compact(
            'allPorts', 'totalPorts', 'totalCountries',
            'regionCounts', 'countries'
        ));
    }

    private function getRegionByCode(string $code): string
    {
        $regions = [
            'Asia' => ['CN', 'JP', 'KR', 'SG', 'MY', 'ID', 'TH', 'VN', 'PH', 'IN', 'PK', 'BD', 'LK', 'MM', 'KH', 'TW', 'HK', 'MO', 'BN', 'TL'],
            'Europe' => ['DE', 'NL', 'BE', 'FR', 'GB', 'ES', 'IT', 'PT', 'GR', 'TR', 'PL', 'RU', 'UA', 'RO', 'BG', 'HR', 'SI', 'SE', 'NO', 'DK', 'FI', 'EE', 'LV', 'LT', 'MT'],
            'Americas' => ['US', 'CA', 'MX', 'BR', 'AR', 'CO', 'CL', 'PE', 'EC', 'VE', 'PA', 'CR', 'HN', 'GT', 'SV', 'NI', 'CU', 'DO', 'JM', 'TT', 'BB'],
            'Middle East' => ['AE', 'SA', 'OM', 'QA', 'KW', 'BH', 'IR', 'IQ', 'IL', 'JO', 'LB', 'YE', 'EG'],
            'Africa' => ['ZA', 'NG', 'GH', 'KE', 'TZ', 'ET', 'MA', 'DZ', 'TN', 'LY', 'SN', 'CI', 'CM', 'AO', 'MZ', 'MG'],
            'Oceania' => ['AU', 'NZ', 'FJ', 'PG', 'SB', 'VU', 'TO', 'WS', 'KI'],
        ];
        foreach ($regions as $region => $codes) {
            if (in_array(strtoupper($code), $codes)) return $region;
        }
        return 'Other';
    }

    private function getGlobalPortData(): array
    {
        return [
            // ===== ASIA =====
            ['id'=>'g1','name'=>'Port of Shanghai','code'=>'CNSHA','latitude'=>31.2304,'longitude'=>121.4737,'country_name'=>'China','country_code'=>'CN','region'=>'Asia','type'=>'global'],
            ['id'=>'g2','name'=>'Port of Singapore','code'=>'SGSIN','latitude'=>1.2897,'longitude'=>103.8501,'country_name'=>'Singapore','country_code'=>'SG','region'=>'Asia','type'=>'global'],
            ['id'=>'g3','name'=>'Port of Shenzhen','code'=>'CNSZX','latitude'=>22.5429,'longitude'=>114.0596,'country_name'=>'China','country_code'=>'CN','region'=>'Asia','type'=>'global'],
            ['id'=>'g4','name'=>'Port of Ningbo-Zhoushan','code'=>'CNNBO','latitude'=>29.8683,'longitude'=>121.5440,'country_name'=>'China','country_code'=>'CN','region'=>'Asia','type'=>'global'],
            ['id'=>'g5','name'=>'Port of Guangzhou','code'=>'CNCAN','latitude'=>23.1291,'longitude'=>113.2644,'country_name'=>'China','country_code'=>'CN','region'=>'Asia','type'=>'global'],
            ['id'=>'g6','name'=>'Port of Busan','code'=>'KRPUS','latitude'=>35.1796,'longitude'=>129.0756,'country_name'=>'South Korea','country_code'=>'KR','region'=>'Asia','type'=>'global'],
            ['id'=>'g7','name'=>'Port of Hong Kong','code'=>'HKHKG','latitude'=>22.3193,'longitude'=>114.1694,'country_name'=>'Hong Kong','country_code'=>'HK','region'=>'Asia','type'=>'global'],
            ['id'=>'g8','name'=>'Port Klang','code'=>'MYPKG','latitude'=>3.0000,'longitude'=>101.4000,'country_name'=>'Malaysia','country_code'=>'MY','region'=>'Asia','type'=>'global'],
            ['id'=>'g9','name'=>'Port of Tanjung Pelepas','code'=>'MYPTP','latitude'=>1.3714,'longitude'=>103.5500,'country_name'=>'Malaysia','country_code'=>'MY','region'=>'Asia','type'=>'global'],
            ['id'=>'g10','name'=>'Port of Qingdao','code'=>'CNTAO','latitude'=>36.0671,'longitude'=>120.3826,'country_name'=>'China','country_code'=>'CN','region'=>'Asia','type'=>'global'],
            ['id'=>'g11','name'=>'Port of Tianjin','code'=>'CNTSN','latitude'=>39.0067,'longitude'=>117.7259,'country_name'=>'China','country_code'=>'CN','region'=>'Asia','type'=>'global'],
            ['id'=>'g12','name'=>'Port of Dalian','code'=>'CNDLC','latitude'=>38.9140,'longitude'=>121.6147,'country_name'=>'China','country_code'=>'CN','region'=>'Asia','type'=>'global'],
            ['id'=>'g13','name'=>'Port of Xiamen','code'=>'CNXMN','latitude'=>24.4798,'longitude'=>118.0894,'country_name'=>'China','country_code'=>'CN','region'=>'Asia','type'=>'global'],
            ['id'=>'g14','name'=>'Port of Tokyo','code'=>'JPTYO','latitude'=>35.6762,'longitude'=>139.6503,'country_name'=>'Japan','country_code'=>'JP','region'=>'Asia','type'=>'global'],
            ['id'=>'g15','name'=>'Port of Nagoya','code'=>'JPNGO','latitude'=>35.1815,'longitude'=>136.9066,'country_name'=>'Japan','country_code'=>'JP','region'=>'Asia','type'=>'global'],
            ['id'=>'g16','name'=>'Port of Yokohama','code'=>'JPYOK','latitude'=>35.4437,'longitude'=>139.6380,'country_name'=>'Japan','country_code'=>'JP','region'=>'Asia','type'=>'global'],
            ['id'=>'g17','name'=>'Port of Osaka','code'=>'JPOSA','latitude'=>34.6937,'longitude'=>135.5022,'country_name'=>'Japan','country_code'=>'JP','region'=>'Asia','type'=>'global'],
            ['id'=>'g18','name'=>'Port of Kaohsiung','code'=>'TWKHH','latitude'=>22.6273,'longitude'=>120.3014,'country_name'=>'Taiwan','country_code'=>'TW','region'=>'Asia','type'=>'global'],
            ['id'=>'g19','name'=>'Port of Laem Chabang','code'=>'THLCH','latitude'=>13.0900,'longitude'=>100.8800,'country_name'=>'Thailand','country_code'=>'TH','region'=>'Asia','type'=>'global'],
            ['id'=>'g20','name'=>'Port of Manila','code'=>'PHMNL','latitude'=>14.5995,'longitude'=>120.9842,'country_name'=>'Philippines','country_code'=>'PH','region'=>'Asia','type'=>'global'],
            ['id'=>'g21','name'=>'Port of Ho Chi Minh','code'=>'VNSGN','latitude'=>10.8231,'longitude'=>106.6297,'country_name'=>'Vietnam','country_code'=>'VN','region'=>'Asia','type'=>'global'],
            ['id'=>'g22','name'=>'Port of Haiphong','code'=>'VNHPH','latitude'=>20.8449,'longitude'=>106.6881,'country_name'=>'Vietnam','country_code'=>'VN','region'=>'Asia','type'=>'global'],
            ['id'=>'g23','name'=>'Port of Colombo','code'=>'LKCMB','latitude'=>6.9271,'longitude'=>79.8612,'country_name'=>'Sri Lanka','country_code'=>'LK','region'=>'Asia','type'=>'global'],
            ['id'=>'g24','name'=>'Port of Nhava Sheva','code'=>'INNSA','latitude'=>18.9387,'longitude'=>72.9493,'country_name'=>'India','country_code'=>'IN','region'=>'Asia','type'=>'global'],
            ['id'=>'g25','name'=>'Port of Chennai','code'=>'INMAA','latitude'=>13.0827,'longitude'=>80.2707,'country_name'=>'India','country_code'=>'IN','region'=>'Asia','type'=>'global'],
            ['id'=>'g26','name'=>'Port of Mundra','code'=>'INMUN','latitude'=>22.8390,'longitude'=>69.7068,'country_name'=>'India','country_code'=>'IN','region'=>'Asia','type'=>'global'],
            ['id'=>'g27','name'=>'Port of Jakarta (Tanjung Priok)','code'=>'IDJKT','latitude'=>-6.1000,'longitude'=>106.8800,'country_name'=>'Indonesia','country_code'=>'ID','region'=>'Asia','type'=>'global'],
            ['id'=>'g28','name'=>'Port of Surabaya (Tanjung Perak)','code'=>'IDSUB','latitude'=>-7.2492,'longitude'=>112.7508,'country_name'=>'Indonesia','country_code'=>'ID','region'=>'Asia','type'=>'global'],
            ['id'=>'g29','name'=>'Port of Makassar','code'=>'IDUPG','latitude'=>-5.1477,'longitude'=>119.4327,'country_name'=>'Indonesia','country_code'=>'ID','region'=>'Asia','type'=>'global'],
            ['id'=>'g30','name'=>'Port of Belawan','code'=>'IDBWN','latitude'=>3.7852,'longitude'=>98.6854,'country_name'=>'Indonesia','country_code'=>'ID','region'=>'Asia','type'=>'global'],
            ['id'=>'g31','name'=>'Port of Chittagong','code'=>'BDCGP','latitude'=>22.3303,'longitude'=>91.8282,'country_name'=>'Bangladesh','country_code'=>'BD','region'=>'Asia','type'=>'global'],
            ['id'=>'g32','name'=>'Port of Karachi','code'=>'PKKHI','latitude'=>24.8607,'longitude'=>67.0011,'country_name'=>'Pakistan','country_code'=>'PK','region'=>'Asia','type'=>'global'],

            // ===== EUROPE =====
            ['id'=>'g33','name'=>'Port of Rotterdam','code'=>'NLRTM','latitude'=>51.9244,'longitude'=>4.4777,'country_name'=>'Netherlands','country_code'=>'NL','region'=>'Europe','type'=>'global'],
            ['id'=>'g34','name'=>'Port of Antwerp','code'=>'BEANR','latitude'=>51.2194,'longitude'=>4.4025,'country_name'=>'Belgium','country_code'=>'BE','region'=>'Europe','type'=>'global'],
            ['id'=>'g35','name'=>'Port of Hamburg','code'=>'DEHAM','latitude'=>53.5753,'longitude'=>10.0153,'country_name'=>'Germany','country_code'=>'DE','region'=>'Europe','type'=>'global'],
            ['id'=>'g36','name'=>'Port of Algeciras','code'=>'ESALG','latitude'=>36.1408,'longitude'=>-5.4537,'country_name'=>'Spain','country_code'=>'ES','region'=>'Europe','type'=>'global'],
            ['id'=>'g37','name'=>'Port of Valencia','code'=>'ESVLC','latitude'=>39.4699,'longitude'=>-0.3763,'country_name'=>'Spain','country_code'=>'ES','region'=>'Europe','type'=>'global'],
            ['id'=>'g38','name'=>'Port of Barcelona','code'=>'ESBCN','latitude'=>41.3851,'longitude'=>2.1734,'country_name'=>'Spain','country_code'=>'ES','region'=>'Europe','type'=>'global'],
            ['id'=>'g39','name'=>'Port of Genoa','code'=>'ITGOA','latitude'=>44.4056,'longitude'=>8.9463,'country_name'=>'Italy','country_code'=>'IT','region'=>'Europe','type'=>'global'],
            ['id'=>'g40','name'=>'Port of Piraeus','code'=>'GRPIR','latitude'=>37.9475,'longitude'=>23.6387,'country_name'=>'Greece','country_code'=>'GR','region'=>'Europe','type'=>'global'],
            ['id'=>'g41','name'=>'Port of Marseille','code'=>'FRMRS','latitude'=>43.2965,'longitude'=>5.3698,'country_name'=>'France','country_code'=>'FR','region'=>'Europe','type'=>'global'],
            ['id'=>'g42','name'=>'Port of Felixstowe','code'=>'GBFXT','latitude'=>51.9589,'longitude'=>1.3517,'country_name'=>'United Kingdom','country_code'=>'GB','region'=>'Europe','type'=>'global'],
            ['id'=>'g43','name'=>'Port of Southampton','code'=>'GBSOU','latitude'=>50.9097,'longitude'=>-1.4044,'country_name'=>'United Kingdom','country_code'=>'GB','region'=>'Europe','type'=>'global'],
            ['id'=>'g44','name'=>'Port of Bremerhaven','code'=>'DEBRV','latitude'=>53.5396,'longitude'=>8.5796,'country_name'=>'Germany','country_code'=>'DE','region'=>'Europe','type'=>'global'],
            ['id'=>'g45','name'=>'Port of Istanbul','code'=>'TRIST','latitude'=>41.0082,'longitude'=>28.9784,'country_name'=>'Turkey','country_code'=>'TR','region'=>'Europe','type'=>'global'],
            ['id'=>'g46','name'=>'Port of Gothenburg','code'=>'SEGOT','latitude'=>57.7089,'longitude'=>11.9746,'country_name'=>'Sweden','country_code'=>'SE','region'=>'Europe','type'=>'global'],
            ['id'=>'g47','name'=>'Port of Gdansk','code'=>'PLGDN','latitude'=>54.3520,'longitude'=>18.6466,'country_name'=>'Poland','country_code'=>'PL','region'=>'Europe','type'=>'global'],
            ['id'=>'g48','name'=>'Port of Novorossiysk','code'=>'RUNVS','latitude'=>44.7238,'longitude'=>37.7689,'country_name'=>'Russia','country_code'=>'RU','region'=>'Europe','type'=>'global'],
            ['id'=>'g49','name'=>'Port of Constanta','code'=>'ROCND','latitude'=>44.1733,'longitude'=>28.6511,'country_name'=>'Romania','country_code'=>'RO','region'=>'Europe','type'=>'global'],
            ['id'=>'g50','name'=>'Port of Le Havre','code'=>'FRLEH','latitude'=>49.4938,'longitude'=>0.1077,'country_name'=>'France','country_code'=>'FR','region'=>'Europe','type'=>'global'],
            ['id'=>'g51','name'=>'Port of Lisbon','code'=>'PTLIS','latitude'=>38.7169,'longitude'=>-9.1399,'country_name'=>'Portugal','country_code'=>'PT','region'=>'Europe','type'=>'global'],
            ['id'=>'g52','name'=>'Port of Helsinki','code'=>'FIHEL','latitude'=>60.1699,'longitude'=>24.9384,'country_name'=>'Finland','country_code'=>'FI','region'=>'Europe','type'=>'global'],

            // ===== AMERICAS =====
            ['id'=>'g53','name'=>'Port of Los Angeles','code'=>'USLAX','latitude'=>33.7291,'longitude'=>-118.2620,'country_name'=>'United States','country_code'=>'US','region'=>'Americas','type'=>'global'],
            ['id'=>'g54','name'=>'Port of Long Beach','code'=>'USLGB','latitude'=>33.7701,'longitude'=>-118.1937,'country_name'=>'United States','country_code'=>'US','region'=>'Americas','type'=>'global'],
            ['id'=>'g55','name'=>'Port of New York','code'=>'USNYC','latitude'=>40.6501,'longitude'=>-74.0100,'country_name'=>'United States','country_code'=>'US','region'=>'Americas','type'=>'global'],
            ['id'=>'g56','name'=>'Port of Savannah','code'=>'USSAV','latitude'=>32.0835,'longitude'=>-81.0998,'country_name'=>'United States','country_code'=>'US','region'=>'Americas','type'=>'global'],
            ['id'=>'g57','name'=>'Port of Houston','code'=>'USHOU','latitude'=>29.7604,'longitude'=>-95.3698,'country_name'=>'United States','country_code'=>'US','region'=>'Americas','type'=>'global'],
            ['id'=>'g58','name'=>'Port of Seattle','code'=>'USSEA','latitude'=>47.6062,'longitude'=>-122.3321,'country_name'=>'United States','country_code'=>'US','region'=>'Americas','type'=>'global'],
            ['id'=>'g59','name'=>'Port of Miami','code'=>'USMIA','latitude'=>25.7617,'longitude'=>-80.1918,'country_name'=>'United States','country_code'=>'US','region'=>'Americas','type'=>'global'],
            ['id'=>'g60','name'=>'Port of Baltimore','code'=>'USBAL','latitude'=>39.2904,'longitude'=>-76.6122,'country_name'=>'United States','country_code'=>'US','region'=>'Americas','type'=>'global'],
            ['id'=>'g61','name'=>'Port of Vancouver','code'=>'CAVAN','latitude'=>49.2827,'longitude'=>-123.1207,'country_name'=>'Canada','country_code'=>'CA','region'=>'Americas','type'=>'global'],
            ['id'=>'g62','name'=>'Port of Santos','code'=>'BRSTS','latitude'=>-23.9608,'longitude'=>-46.3336,'country_name'=>'Brazil','country_code'=>'BR','region'=>'Americas','type'=>'global'],
            ['id'=>'g63','name'=>'Port of Paranaguá','code'=>'BRPNG','latitude'=>-25.5162,'longitude'=>-48.5155,'country_name'=>'Brazil','country_code'=>'BR','region'=>'Americas','type'=>'global'],
            ['id'=>'g64','name'=>'Port of Manzanillo','code'=>'MXZLO','latitude'=>19.0522,'longitude'=>-104.3188,'country_name'=>'Mexico','country_code'=>'MX','region'=>'Americas','type'=>'global'],
            ['id'=>'g65','name'=>'Port of Buenos Aires','code'=>'ARBUE','latitude'=>-34.6037,'longitude'=>-58.3816,'country_name'=>'Argentina','country_code'=>'AR','region'=>'Americas','type'=>'global'],
            ['id'=>'g66','name'=>'Port of Callao','code'=>'PECLL','latitude'=>-12.0431,'longitude'=>-77.0282,'country_name'=>'Peru','country_code'=>'PE','region'=>'Americas','type'=>'global'],
            ['id'=>'g67','name'=>'Port of Cartagena','code'=>'COCTG','latitude'=>10.3910,'longitude'=>-75.4794,'country_name'=>'Colombia','country_code'=>'CO','region'=>'Americas','type'=>'global'],
            ['id'=>'g68','name'=>'Port of Balboa','code'=>'PABLB','latitude'=>8.9942,'longitude'=>-79.5734,'country_name'=>'Panama','country_code'=>'PA','region'=>'Americas','type'=>'global'],
            ['id'=>'g69','name'=>'Port of Kingston','code'=>'JMKIN','latitude'=>17.9784,'longitude'=>-76.7832,'country_name'=>'Jamaica','country_code'=>'JM','region'=>'Americas','type'=>'global'],
            ['id'=>'g70','name'=>'Port of Prince Rupert','code'=>'CAPRP','latitude'=>54.3150,'longitude'=>-130.3208,'country_name'=>'Canada','country_code'=>'CA','region'=>'Americas','type'=>'global'],

            // ===== MIDDLE EAST =====
            ['id'=>'g71','name'=>'Port of Jebel Ali','code'=>'AEJEA','latitude'=>24.9998,'longitude'=>55.0613,'country_name'=>'UAE','country_code'=>'AE','region'=>'Middle East','type'=>'global'],
            ['id'=>'g72','name'=>'Port of Abu Dhabi','code'=>'AEAUH','latitude'=>24.4539,'longitude'=>54.3773,'country_name'=>'UAE','country_code'=>'AE','region'=>'Middle East','type'=>'global'],
            ['id'=>'g73','name'=>'Port of Salalah','code'=>'OMSSL','latitude'=>17.0151,'longitude'=>54.0924,'country_name'=>'Oman','country_code'=>'OM','region'=>'Middle East','type'=>'global'],
            ['id'=>'g74','name'=>'Port of King Abdullah','code'=>'SAKAC','latitude'=>22.5095,'longitude'=>39.1074,'country_name'=>'Saudi Arabia','country_code'=>'SA','region'=>'Middle East','type'=>'global'],
            ['id'=>'g75','name'=>'Port of Hamad','code'=>'QAHMD','latitude'=>24.9667,'longitude'=>51.5670,'country_name'=>'Qatar','country_code'=>'QA','region'=>'Middle East','type'=>'global'],
            ['id'=>'g76','name'=>'Port of Aqaba','code'=>'JOAQJ','latitude'=>29.5269,'longitude'=>35.0059,'country_name'=>'Jordan','country_code'=>'JO','region'=>'Middle East','type'=>'global'],
            ['id'=>'g77','name'=>'Port of Haifa','code'=>'ILHFA','latitude'=>32.8191,'longitude'=>34.9983,'country_name'=>'Israel','country_code'=>'IL','region'=>'Middle East','type'=>'global'],
            ['id'=>'g78','name'=>'Port of Bandar Abbas','code'=>'IRBND','latitude'=>27.1865,'longitude'=>56.2808,'country_name'=>'Iran','country_code'=>'IR','region'=>'Middle East','type'=>'global'],
            ['id'=>'g79','name'=>'Port of Umm Qasr','code'=>'IQUMQ','latitude'=>30.0349,'longitude'=>47.9313,'country_name'=>'Iraq','country_code'=>'IQ','region'=>'Middle East','type'=>'global'],
            ['id'=>'g80','name'=>'Port of Alexandria','code'=>'EGALY','latitude'=>31.2001,'longitude'=>29.9187,'country_name'=>'Egypt','country_code'=>'EG','region'=>'Middle East','type'=>'global'],

            // ===== AFRICA =====
            ['id'=>'g81','name'=>'Port of Durban','code'=>'ZADDR','latitude'=>-29.8587,'longitude'=>31.0218,'country_name'=>'South Africa','country_code'=>'ZA','region'=>'Africa','type'=>'global'],
            ['id'=>'g82','name'=>'Port of Cape Town','code'=>'ZACPT','latitude'=>-33.9249,'longitude'=>18.4241,'country_name'=>'South Africa','country_code'=>'ZA','region'=>'Africa','type'=>'global'],
            ['id'=>'g83','name'=>'Port of Tanger Med','code'=>'MATNG','latitude'=>35.8842,'longitude'=>-5.5009,'country_name'=>'Morocco','country_code'=>'MA','region'=>'Africa','type'=>'global'],
            ['id'=>'g84','name'=>'Port of Lagos Apapa','code'=>'NGAPP','latitude'=>6.4474,'longitude'=>3.3903,'country_name'=>'Nigeria','country_code'=>'NG','region'=>'Africa','type'=>'global'],
            ['id'=>'g85','name'=>'Port of Mombasa','code'=>'KEMBA','latitude'=>-4.0435,'longitude'=>39.6682,'country_name'=>'Kenya','country_code'=>'KE','region'=>'Africa','type'=>'global'],
            ['id'=>'g86','name'=>'Port of Dar es Salaam','code'=>'TZDAR','latitude'=>-6.7924,'longitude'=>39.2083,'country_name'=>'Tanzania','country_code'=>'TZ','region'=>'Africa','type'=>'global'],
            ['id'=>'g87','name'=>'Port of Djibouti','code'=>'DJJIB','latitude'=>11.5720,'longitude'=>43.1456,'country_name'=>'Djibouti','country_code'=>'DJ','region'=>'Africa','type'=>'global'],
            ['id'=>'g88','name'=>'Port of Abidjan','code'=>'CIABJ','latitude'=>5.3364,'longitude'=>-4.0267,'country_name'=>'Ivory Coast','country_code'=>'CI','region'=>'Africa','type'=>'global'],
            ['id'=>'g89','name'=>'Port of Dakar','code'=>'SNDKR','latitude'=>14.6937,'longitude'=>-17.4441,'country_name'=>'Senegal','country_code'=>'SN','region'=>'Africa','type'=>'global'],
            ['id'=>'g90','name'=>'Port of Luanda','code'=>'AOLAD','latitude'=>-8.8159,'longitude'=>13.2306,'country_name'=>'Angola','country_code'=>'AO','region'=>'Africa','type'=>'global'],
            ['id'=>'g91','name'=>'Port of Tema','code'=>'GHTEM','latitude'=>5.6365,'longitude'=>-0.0167,'country_name'=>'Ghana','country_code'=>'GH','region'=>'Africa','type'=>'global'],
            ['id'=>'g92','name'=>'Port of Maputo','code'=>'MZMPM','latitude'=>-25.9686,'longitude'=>32.5732,'country_name'=>'Mozambique','country_code'=>'MZ','region'=>'Africa','type'=>'global'],

            // ===== OCEANIA =====
            ['id'=>'g93','name'=>'Port of Melbourne','code'=>'AUMEL','latitude'=>-37.8136,'longitude'=>144.9631,'country_name'=>'Australia','country_code'=>'AU','region'=>'Oceania','type'=>'global'],
            ['id'=>'g94','name'=>'Port of Sydney','code'=>'AUSYD','latitude'=>-33.8688,'longitude'=>151.2093,'country_name'=>'Australia','country_code'=>'AU','region'=>'Oceania','type'=>'global'],
            ['id'=>'g95','name'=>'Port of Brisbane','code'=>'AUBNE','latitude'=>-27.4698,'longitude'=>153.0251,'country_name'=>'Australia','country_code'=>'AU','region'=>'Oceania','type'=>'global'],
            ['id'=>'g96','name'=>'Port of Fremantle','code'=>'AUFRE','latitude'=>-32.0569,'longitude'=>115.7439,'country_name'=>'Australia','country_code'=>'AU','region'=>'Oceania','type'=>'global'],
            ['id'=>'g97','name'=>'Port of Auckland','code'=>'NZAKL','latitude'=>-36.8509,'longitude'=>174.7645,'country_name'=>'New Zealand','country_code'=>'NZ','region'=>'Oceania','type'=>'global'],
            ['id'=>'g98','name'=>'Port Moresby','code'=>'PGPOM','latitude'=>-9.4438,'longitude'=>147.1803,'country_name'=>'Papua New Guinea','country_code'=>'PG','region'=>'Oceania','type'=>'global'],

            // ===== MORE ASIA =====
            ['id'=>'g99','name'=>'Port of Incheon','code'=>'KRICN','latitude'=>37.4760,'longitude'=>126.6160,'country_name'=>'South Korea','country_code'=>'KR','region'=>'Asia','type'=>'global'],
            ['id'=>'g100','name'=>'Port of Gwangyang','code'=>'KRKWJ','latitude'=>34.9100,'longitude'=>127.7060,'country_name'=>'South Korea','country_code'=>'KR','region'=>'Asia','type'=>'global'],
            ['id'=>'g101','name'=>'Port of Kobe','code'=>'JPUBE','latitude'=>34.6901,'longitude'=>135.1956,'country_name'=>'Japan','country_code'=>'JP','region'=>'Asia','type'=>'global'],
            ['id'=>'g102','name'=>'Port of Shenkou','code'=>'CNSZP','latitude'=>22.4856,'longitude'=>113.9000,'country_name'=>'China','country_code'=>'CN','region'=>'Asia','type'=>'global'],
            ['id'=>'g103','name'=>'Port of Fuzhou','code'=>'CNFOC','latitude'=>26.0745,'longitude'=>119.2965,'country_name'=>'China','country_code'=>'CN','region'=>'Asia','type'=>'global'],
            ['id'=>'g104','name'=>'Port of Wuhan','code'=>'CNWUH','latitude'=>30.5928,'longitude'=>114.3055,'country_name'=>'China','country_code'=>'CN','region'=>'Asia','type'=>'global'],
            ['id'=>'g105','name'=>'Port of Penang','code'=>'MYPEP','latitude'=>5.4141,'longitude'=>100.3288,'country_name'=>'Malaysia','country_code'=>'MY','region'=>'Asia','type'=>'global'],
        ];
    }
}

