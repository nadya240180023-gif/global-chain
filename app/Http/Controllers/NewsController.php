<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\NewsCache;
use App\Services\SentimentAnalyzer;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        if (!$selectedCountry && $countries->isNotEmpty()) {
            $selectedCountry = $countries->first();
        }

        // If no news exist for this country, generate mock news (no API call)
        if ($selectedCountry && !NewsCache::where('country_id', $selectedCountry->id)->exists()) {
            $this->generateMockNews($selectedCountry);
        }

        // Fetch news for selected country
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

    /**
     * Generate mock news for a country without any API calls.
     */
    private function generateMockNews(Country $country): void
    {
        $templates = [
            'economy' => [
                ['title' => 'Economic growth reported due to stable trade agreements', 'description' => 'Local financial markets show robust recovery and profit gains this quarter, demonstrating solid expansion.', 'source' => 'Financial Pulse'],
                ['title' => 'Inflation crisis deepens as consumer index jumps unexpectedly', 'description' => 'The economy faces severe recession fears with rising inflation rate, weak currency values, and financial drops.', 'source' => 'Global Monitor'],
                ['title' => 'Stable market recovery continues with steady gains', 'description' => 'Industrial leaders report successful recovery operations. Analysts remain optimistic about long-term stability.', 'source' => 'Economy Today'],
            ],
            'logistics' => [
                ['title' => 'Major port congestion delay resolved, restoring supply chain lines', 'description' => 'After days of heavy delays and vessel backlogs, terminal management reports a positive recovery and faster processing.', 'source' => 'Logistics Weekly'],
                ['title' => 'Transportation strike causes massive shipping delay across border gates', 'description' => 'A sudden union strike has caused total disaster for cross-border logistics. Huge delays and backlogs are expected.', 'source' => 'Logistics Weekly'],
            ],
            'shipping' => [
                ['title' => 'Extreme storm forces shipping lines to bypass main port terminals', 'description' => 'Severe storm conditions have triggered maritime warning. Ships face severe delays and route diversions.', 'source' => 'Maritime Gazette'],
                ['title' => 'New container ship route improves cargo capacity and profit margins', 'description' => 'The new lane represents a major improvement in trade, boosting transport efficiency and ensuring stable growth.', 'source' => 'Shipping Global'],
            ],
            'trade' => [
                ['title' => 'Geopolitical conflict escalates, triggering international trade sanctions', 'description' => 'An escalating border conflict has triggered severe trade sanctions, bringing high risk and supply drops.', 'source' => 'Trade Review'],
                ['title' => 'Bilateral trade agreement brings substantial profit and market growth', 'description' => 'The new treaty stabilizes tariffs, promoting economic cooperation, mutual gains, and strong growth.', 'source' => 'Global Trade'],
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
                    'url'             => '#',
                    'source'          => $item['source'],
                    'published_at'    => Carbon::now()->subHours(rand(1, 72)),
                    'category'        => $cat,
                    'sentiment'       => $analysis['sentiment'],
                    'sentiment_score' => $analysis['score'],
                ]);
            }
        }
    }
}
