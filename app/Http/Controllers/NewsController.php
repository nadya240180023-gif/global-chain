<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\NewsCache;
use App\Services\SentimentAnalyzer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    protected $sentimentAnalyzer;

    public function __construct(SentimentAnalyzer $sentimentAnalyzer)
    {
        $this->sentimentAnalyzer = $sentimentAnalyzer;
    }

    public function index(Request $request)
    {
        $countries = Country::orderBy('name')->get();
        $selectedCode = $request->query('country', 'ID');
        $selectedCountry = Country::where('code', strtoupper($selectedCode))->first();

        if ($selectedCountry) {
            // Check if we need to sync news (if empty or older than 15 minutes to keep it real-time but respect API limits)
            $latestNews = \App\Models\NewsCache::where('country_id', $selectedCountry->id)
                            ->orderBy('created_at', 'desc')
                            ->first();

            $needsSync = !$latestNews || $latestNews->created_at->diffInMinutes(Carbon::now()) >= 15;

            if ($needsSync) {
                // Clear old news for this country
                \App\Models\NewsCache::where('country_id', $selectedCountry->id)->delete();
                $this->fetchGNews($selectedCountry);
            }
        } // Fetch news for selected country
        $news = $selectedCountry
            ? NewsCache::where('country_id', $selectedCountry->id)
                ->orderBy('published_at', 'desc')
                ->get()
            : collect();

        // Calculate sentiment breakdown percentages
        $posCount = $news->where('sentiment', 'Positive')->count();
        $negCount = $news->where('sentiment', 'Negative')->count();
        $neuCount = $news->where('sentiment', 'Neutral')->count();
        $total = $news->count();

        $breakdown = [
            'total'     => $total,
            'Positive'  => $total > 0 ? round(($posCount / $total) * 100) : 0,
            'Negative'  => $total > 0 ? round(($negCount / $total) * 100) : 0,
            'Neutral'   => $total > 0 ? round(($neuCount / $total) * 100) : 0,
            'pos_count' => $posCount,
            'neg_count' => $negCount,
            'neu_count' => $neuCount,
        ];

        // Process news articles with sentiment word highlights
        $analyzedNews = $news->map(function ($article) {
            $analysis = $this->sentimentAnalyzer->analyze($article->title . ' ' . $article->description);
            return [
                'id'               => $article->id,
                'title'            => $article->title,
                'description'      => $article->description,
                'source'           => $article->source,
                'url'              => $article->url,
                'image_url'        => $article->image_url,
                'published_at'     => $article->published_at,
                'category'         => $article->category,
                'sentiment'        => $article->sentiment,
                'sentiment_score'  => $article->sentiment_score,
                'positive_matches' => $analysis['positive_matches'],
                'negative_matches' => $analysis['negative_matches'],
                'pos_count'        => $analysis['pos_count'],
                'neg_count'        => $analysis['neg_count'],
            ];
        });

        return view('news.index', compact('countries', 'selectedCountry', 'analyzedNews', 'breakdown'));
    }

    private function fetchGNews(Country $country): void
    {
        $categories = [
            'ekonomi'  => 'economy ' . $country->name,
            'logistik' => 'logistics supply chain ' . $country->name,
            'shipping' => 'shipping port ' . $country->name,
            'trade'    => 'trade import export ' . $country->name,
        ];

        foreach ($categories as $cat => $query) {
            try {
                // Fetch from Google News RSS
                $url = 'https://news.google.com/rss/search?q=' . urlencode($query) . '&hl=en-US&gl=US&ceid=US:en';
                $response = Http::get($url);

                if ($response->successful()) {
                    $rss = simplexml_load_string($response->body());
                    if ($rss && isset($rss->channel->item)) {
                        $items = $rss->channel->item;
                        // Limit to 3 articles per category to avoid too many requests to DB
                        $count = 0;
                        // Thematic images for fallback
                        $themeImages = [
                            'ekonomi' => [
                                'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&q=80&w=800',
                                'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?auto=format&fit=crop&q=80&w=800',
                                'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?auto=format&fit=crop&q=80&w=800',
                            ],
                            'logistik' => [
                                'https://images.unsplash.com/photo-1586528116311-ad8ed7c663be?auto=format&fit=crop&q=80&w=800',
                                'https://images.unsplash.com/photo-1519003722824-194d4455a60c?auto=format&fit=crop&q=80&w=800',
                            ],
                            'shipping' => [
                                'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?auto=format&fit=crop&q=80&w=800',
                                'https://images.unsplash.com/photo-1577717903315-1691ae25ab3f?auto=format&fit=crop&q=80&w=800',
                            ],
                            'trade' => [
                                'https://images.unsplash.com/photo-1566838332155-21d3f95e54d8?auto=format&fit=crop&q=80&w=800',
                                'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&q=80&w=800',
                            ],
                        ];

                        foreach ($items as $item) {
                            if ($count >= 3) break;
                            
                            $title = (string) $item->title;
                            $link = (string) $item->link;
                            $description = strip_tags((string) $item->description);
                            $pubDate = (string) $item->pubDate;
                            $source = (string) $item->source;

                            // Avoid duplicates
                            if (NewsCache::where('url', $link)->exists()) {
                                continue;
                            }

                            $analysis = $this->sentimentAnalyzer->analyze($title . ' ' . $description);
                            $randomImage = $themeImages[$cat][array_rand($themeImages[$cat])];

                            NewsCache::create([
                                'country_id'      => $country->id,
                                'title'           => $title,
                                'description'     => substr($description, 0, 200),
                                'content'         => substr($description, 0, 500),
                                'url'             => $link,
                                'image_url'       => $randomImage,
                                'source'          => $source ?: 'Google News',
                                'published_at'    => Carbon::parse($pubDate),
                                'category'        => $cat,
                                'sentiment'       => $analysis['sentiment'],
                                'sentiment_score' => $analysis['score'],
                            ]);
                            $count++;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to fetch Google News RSS: " . $e->getMessage());
            }
        }

        // If no news were created, fallback to mock so it's not empty
        if (!NewsCache::where('country_id', $country->id)->exists()) {
            $this->generateMockNews($country);
        }
    }

    /**
     * Generate mock news for a country without any API calls.
     */
    private function generateMockNews(Country $country): void
    {
        $templates = [
            'ekonomi' => [
                ['title' => 'Pertumbuhan ekonomi dilaporkan akibat kesepakatan perdagangan yang stabil', 'description' => 'Pasar keuangan lokal menunjukkan pemulihan yang kuat dan keuntungan kuartal ini, menunjukkan ekspansi yang solid.', 'source' => 'Financial Pulse', 'image' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&q=80&w=800'],
                ['title' => 'Krisis inflasi memburuk karena indeks konsumen meningkat secara tak terduga', 'description' => 'Ekonomi menghadapi ketakutan resesi parah dengan tingkat inflasi yang meningkat, nilai mata uang yang lemah, dan penurunan finansial.', 'source' => 'Global Monitor', 'image' => 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?auto=format&fit=crop&q=80&w=800'],
                ['title' => 'Pemulihan pasar yang stabil terus berlanjut dengan keuntungan konsisten', 'description' => 'Para pemimpin industri melaporkan operasi pemulihan yang sukses. Analis tetap optimis tentang stabilitas jangka panjang.', 'source' => 'Economy Today', 'image' => 'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?auto=format&fit=crop&q=80&w=800'],
            ],
            'logistik' => [
                ['title' => 'Kepadatan pelabuhan utama teratasi, memulihkan jalur rantai pasok', 'description' => 'Setelah berhari-hari penundaan dan penumpukan kapal, manajemen terminal melaporkan pemulihan positif dan proses yang lebih cepat.', 'source' => 'Logistics Weekly', 'image' => 'https://images.unsplash.com/photo-1586528116311-ad8ed7c663be?auto=format&fit=crop&q=80&w=800'],
                ['title' => 'Mogok kerja transportasi menyebabkan penundaan pengiriman besar-besaran di perbatasan', 'description' => 'Mogok serikat pekerja yang tiba-tiba telah menyebabkan bencana total bagi logistik lintas batas. Penundaan besar diperkirakan terjadi.', 'source' => 'Logistics Weekly', 'image' => 'https://images.unsplash.com/photo-1519003722824-194d4455a60c?auto=format&fit=crop&q=80&w=800'],
            ],
            'shipping' => [
                ['title' => 'Badai ekstrem memaksa jalur pelayaran menghindari terminal pelabuhan utama', 'description' => 'Kondisi badai parah telah memicu peringatan maritim. Kapal menghadapi penundaan parah dan pengalihan rute.', 'source' => 'Maritime Gazette', 'image' => 'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?auto=format&fit=crop&q=80&w=800'],
                ['title' => 'Rute kapal kontainer baru meningkatkan kapasitas kargo dan margin keuntungan', 'description' => 'Jalur baru ini merupakan peningkatan besar dalam perdagangan, meningkatkan efisiensi transportasi dan memastikan pertumbuhan yang stabil.', 'source' => 'Shipping Global', 'image' => 'https://images.unsplash.com/photo-1577717903315-1691ae25ab3f?auto=format&fit=crop&q=80&w=800'],
            ],
            'trade' => [
                ['title' => 'Konflik geopolitik memanas, memicu sanksi perdagangan internasional', 'description' => 'Konflik perbatasan yang memanas telah memicu sanksi perdagangan yang parah, membawa risiko tinggi dan penurunan pasokan.', 'source' => 'Trade Review', 'image' => 'https://images.unsplash.com/photo-1566838332155-21d3f95e54d8?auto=format&fit=crop&q=80&w=800'],
                ['title' => 'Perjanjian perdagangan bilateral membawa keuntungan substansial dan pertumbuhan pasar', 'description' => 'Perjanjian baru ini menstabilkan tarif, mendorong kerja sama ekonomi, keuntungan bersama, dan pertumbuhan yang kuat.', 'source' => 'Global Trade', 'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&q=80&w=800'],
            ],
        ];

        foreach ($templates as $cat => $items) {
            foreach ($items as $item) {
                $analysis = $this->sentimentAnalyzer->analyze($item['title'] . ' ' . $item['description']);
                NewsCache::create([
                    'country_id'      => $country->id,
                    'title'           => $item['title'],
                    'description'     => $item['description'],
                    'content'         => 'Full text content: ' . $item['title'],
                    'url'             => 'https://news.google.com/search?q=' . urlencode($item['title']),
                    'image_url'       => $item['image'],
                    'source'          => $item['source'],
                    'published_at'    => Carbon::now()->subMinutes(rand(1, 59)),
                    'category'        => $cat,
                    'sentiment'       => $analysis['sentiment'],
                    'sentiment_score' => $analysis['score'],
                ]);
            }
        }
    }
}
