<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ManagementTeam;
use App\Models\Faq;
use App\Models\ClientReview;
use App\Models\TradingBot;
use App\Models\CopyTrading;
use App\Models\CopyTradingHistory;
use App\Models\Blockchain;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Services\FuturesServices;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $page_title = __("Home");

        $template = config('site.template');
        $path = public_path('assets/templates/' . $template . '/images/mockups/');
        $images = glob($path . '*.{png,jpg,jpeg}', GLOB_BRACE);
        $mockups = array_map(function ($image) use ($template) {
            return asset('assets/templates/' . $template . '/images/mockups/' . basename($image));
        }, $images);

        // Fetch Live Crypto Market Stats for Homepage Deck (Cached for 45s to avoid rate-limits)
        $marketStats = \Illuminate\Support\Facades\Cache::remember('homepage_crypto_market_stats', 45, function () {
            $futures = new FuturesServices();
            $targetSymbols = ['BTCUSDT', 'ETHUSDT', 'SOLUSDT', 'BNBUSDT'];
            $stats = [];

            try {
                $response = $futures->futureTickers();
                if (($response['status'] ?? '') === 'success' && !empty($response['data'])) {
                    $byTicker = [];
                    foreach ($response['data'] as $item) {
                        $byTicker[$item['ticker']] = $item;
                    }

                    foreach ($targetSymbols as $sym) {
                        if (isset($byTicker[$sym])) {
                            $item = $byTicker[$sym];
                            $cleanSymbol = str_replace('USDT', '', $sym);
                            $vol = (float)($item['volume_24h'] ?? 0);
                            $formattedVol = $vol >= 1000000000 
                                ? '$' . number_format($vol / 1000000000, 1) . 'B'
                                : ($vol >= 1000000 ? '$' . number_format($vol / 1000000, 1) . 'M' : '$' . number_format($vol, 0));

                            $stats[$sym] = [
                                'symbol' => $sym,
                                'base' => $cleanSymbol,
                                'name' => $cleanSymbol . '/USDT',
                                'price' => (float)$item['current_price'],
                                'formatted_price' => '$' . number_format($item['current_price'], $item['current_price'] < 10 ? 4 : 2),
                                'change' => (float)($item['change_1d_percentage'] ?? 0),
                                'formatted_change' => (($item['change_1d_percentage'] ?? 0) >= 0 ? '+' : '') . number_format($item['change_1d_percentage'] ?? 0, 2) . '%',
                                'is_positive' => (($item['change_1d_percentage'] ?? 0) >= 0),
                                'volume' => $formattedVol,
                                'logo' => asset('assets/images/tokens/' . strtolower($cleanSymbol) . '.png'),
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Market stats fetch error: " . $e->getMessage());
            }

            // Fallback values if external network completely fails
            if (empty($stats['BTCUSDT'])) {
                $stats['BTCUSDT'] = ['symbol' => 'BTCUSDT', 'base' => 'BTC', 'name' => 'BTC/USDT', 'price' => 96450.00, 'formatted_price' => '$96,450.00', 'change' => 3.42, 'formatted_change' => '+3.42%', 'is_positive' => true, 'volume' => '$28.4B', 'logo' => asset('assets/images/tokens/btc.png')];
            }
            if (empty($stats['ETHUSDT'])) {
                $stats['ETHUSDT'] = ['symbol' => 'ETHUSDT', 'base' => 'ETH', 'name' => 'ETH/USDT', 'price' => 3620.50, 'formatted_price' => '$3,620.50', 'change' => 4.18, 'formatted_change' => '+4.18%', 'is_positive' => true, 'volume' => '$14.2B', 'logo' => asset('assets/images/tokens/eth.png')];
            }
            if (empty($stats['SOLUSDT'])) {
                $stats['SOLUSDT'] = ['symbol' => 'SOLUSDT', 'base' => 'SOL', 'name' => 'SOL/USDT', 'price' => 214.80, 'formatted_price' => '$214.80', 'change' => 8.65, 'formatted_change' => '+8.65%', 'is_positive' => true, 'volume' => '$8.9B', 'logo' => asset('assets/images/tokens/sol.png')];
            }
            if (empty($stats['BNBUSDT'])) {
                $stats['BNBUSDT'] = ['symbol' => 'BNBUSDT', 'base' => 'BNB', 'name' => 'BNB/USDT', 'price' => 685.20, 'formatted_price' => '$685.20', 'change' => 1.95, 'formatted_change' => '+1.95%', 'is_positive' => true, 'volume' => '$4.1B', 'logo' => asset('assets/images/tokens/bnb.png')];
            }

            return $stats;
        });

        $regulatoryCompliance = json_decode(getSetting('regulatory_compliance'), true);

        $aapl = [];
        $btc = [];

        // Bot Trading Data (Loaded locally from JSON)
        $botData = \Illuminate\Support\Facades\Cache::remember('trading_bot_data', 60 * 60 * 24, function () {
            $jsonFile = public_path('assets/json/trading-bot-pairs.json');
            if (file_exists($jsonFile)) {
                $json = json_decode(file_get_contents($jsonFile), true);
                if (!empty($json['data']) && !empty($json['data']['pairs'])) {
                    return $json['data'];
                }
            }
            return null;
        });

        $defaultExchanges = [
            'Binance',
            'Bybit',
            'OKX',
            'Kraken',
            'KuCoin',
            'Bitget',
            'Gate.io',
            'Deribit',
            'Huobi',
        ];

        $botTrading = [
            'exchanges' => !empty($botData['exchanges']) ? $botData['exchanges'] : $defaultExchanges,
            'crypto_markets' => $botData['pairs']['crypto'] ?? [],
            'forex_markets' => $botData['pairs']['forex'] ?? [],
        ];

        $management_team = ManagementTeam::get();
        $reviews = ClientReview::all();

        $featuredBots = TradingBot::active()->get();

        // Copy Trading Stats for Home Page
        $copyTradingStats = [
            'total_profit' => CopyTradingHistory::where('status', 'completed')->sum('profit'),
            'active_traders' => \App\Models\User::whereHas('copyTradingHistories', function ($q) {
                $q->where('status', 'active');
            })->count(),
            'successful_trades' => CopyTradingHistory::where('status', 'completed')->where('profit', '>', 0)->count(),
        ];

        // Recent copy trading winners for home page
        $copyTradingWinners = CopyTradingHistory::with('copyTrading')
            ->where('status', 'completed')
            ->where('profit', '>', 0)
            ->latest('completed_at')
            ->limit(4)
            ->get();

        // Fetch all blockchains regardless of status for hero section display
        $heroBlockchains = Blockchain::orderBy('priority', 'asc')->get();

        // Fetch active FAQs for homepage accordion
        $faqs = Faq::where('status', 1)->orderBy('sort_order', 'asc')->get();

        // Fetch recent completed deposits and withdrawals for live stream marquee
        $recentDeposits = Deposit::with(['user', 'blockchain', 'blockchainToken'])
            ->where('status', 'completed')
            ->latest()
            ->limit(6)
            ->get();

        $recentWithdrawals = Withdrawal::with(['user', 'blockchain', 'blockchainToken'])
            ->where('status', 'completed')
            ->latest()
            ->limit(6)
            ->get();

        return view('templates.' . $template . '.blades.pages.index', compact(
            'mockups',
            'marketStats',
            'regulatoryCompliance',
            'page_title',
            'aapl',
            'btc',
            'management_team',
            'reviews',
            'botTrading',
            'featuredBots',
            'copyTradingStats',
            'copyTradingWinners',
            'heroBlockchains',
            'faqs',
            'recentDeposits',
            'recentWithdrawals'
        ));
    }


    // Trading Bots (Dedicated Page)
    public function tradingBots()
    {
        // check if trading bot module is enabled
        if (!moduleEnabled('trading_bot_module')) {
            abort(404);
        }

        $template = config('site.template');
        $page_title = __("Trading Bots");
        $page_description = __("Automate your trading with our world-class, performance-driven bots. Choose your algorithm and start trading 24/7.");

        // Fetch all active bots
        $allBots = TradingBot::active()->get();
        $recommendedBots = TradingBot::active()->limit(3)->get();

        // Extract filters
        $botTypes = $allBots->pluck('type')->unique();
        $botMarkets = $allBots->pluck('traded_pairs')->flatten()->unique();

        // Bot Trading Data (Loaded locally from JSON)
        $botData = \Illuminate\Support\Facades\Cache::remember('trading_bot_data', 60 * 60 * 24, function () {
            $jsonFile = public_path('assets/json/trading-bot-pairs.json');
            if (file_exists($jsonFile)) {
                $json = json_decode(file_get_contents($jsonFile), true);
                if (!empty($json['data']) && !empty($json['data']['pairs'])) {
                    return $json['data'];
                }
            }
            return null;
        });

        $defaultExchanges = [
            'Binance',
            'Bybit',
            'OKX',
            'Kraken',
            'KuCoin',
            'Bitget',
            'Gate.io',
            'Deribit',
            'Huobi',
        ];

        $botTrading = [
            'exchanges' => !empty($botData['exchanges']) ? $botData['exchanges'] : $defaultExchanges,
            'crypto_markets' => $botData['pairs']['crypto'] ?? [],
            'forex_markets' => $botData['pairs']['forex'] ?? [],
        ];

        return view('templates.' . $template . '.blades.pages.trading-bots', compact(
            'page_title',
            'page_description',
            'allBots',
            'recommendedBots',
            'botTypes',
            'botMarkets',
            'botTrading'
        ));
    }


    // Copy Trading (Dedicated Page)
    public function copyTrading()
    {
        // check if copy trading module is enabled
        if (!moduleEnabled('copy_trading_module')) {
            abort(404);
        }

        $template = config('site.template');
        $page_title = __("Copy Trading");
        $page_description = __("Mirror the success of elite algorithmic strategies. Transparent results, verified performance, and direct market execution.");

        // Statistics
        $totalProfit = CopyTradingHistory::where('status', 'completed')->sum('profit');
        $totalVolume = CopyTradingHistory::sum('amount');
        $successfulTrades = CopyTradingHistory::where('status', 'completed')->where('profit', '>', 0)->count();
        $totalTrades = CopyTradingHistory::where('status', 'completed')->count();
        $successRate = $totalTrades > 0 ? ($successfulTrades / $totalTrades) * 100 : 0;

        $activeTraders = \App\Models\User::whereHas('copyTradingHistories', function ($q) {
            $q->where('status', 'active');
        })->count();

        // Recent winning trades (social proof)
        $recentTrades = CopyTradingHistory::with('copyTrading')
            ->where('status', 'completed')
            ->where('profit', '>', 0)
            ->latest('completed_at')
            ->limit(10)
            ->get();

        $stats = [
            'total_profit' => $totalProfit,
            'total_volume' => $totalVolume,
            'success_rate' => number_format($successRate, 1),
            'active_traders' => $activeTraders,
            'total_trades_count' => $totalTrades,
        ];

        return view('templates.' . $template . '.blades.pages.copy-trading', compact(
            'page_title',
            'page_description',
            'stats',
            'recentTrades'
        ));
    }
    public function aboutUs()
    {
        $template = config('site.template');
        $page_title = __("About Us");
        $page_description = __(":name is a leading financial services company that provides investment opportunities across stocks, crypto, forex, real estate and more.", ['name' => getSetting('name')]);
        $management_team = ManagementTeam::all();
        
        return view('templates.' . $template . '.blades.pages.about', compact(
            'page_title',
            'page_description',
            'management_team'
        ));
    }

    // License
    public function license()
    {
        $template = config('site.template');
        $page_title = __("Regulatory Compliance");
        $regulatoryCompliance = json_decode(getSetting('regulatory_compliance'), true);
        return view('templates.' . $template . '.blades.pages.license', compact(
            'page_title',
            'regulatoryCompliance' 
        ));
    }

    // Contact
    public function contact()
    {
        $template = config('site.template');
        $page_title = __("Contact Us");
        $page_description = __(":name Support is available 24/7 to assist you with any questions or concerns.", ['name' => getSetting('name')]);
        return view('templates.' . $template . '.blades.pages.contact', compact(
            'page_title',
            'page_description'
        ));
    }

    public function contactSend(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:100|regex:/^[a-zA-Z\s\.]+$/',
            'email' => 'required|email:rfc,dns|max:255',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:3000',
        ];

        if (getSetting('google_recaptcha') == 'enabled') {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $request->validate($rules, [
            'name.regex' => __('Name contains invalid characters.'),
            'g-recaptcha-response.required' => __('Please verify that you are not a robot.'),
            'g-recaptcha-response.captcha' => __('Captcha error! try again later or contact site admin.'),
        ]);

        // Sanitization to prevent XSS and injection when rendered in blade/emails
        $name = strip_tags($request->name);
        $subject = strip_tags($request->subject);
        $message = strip_tags($request->message);

        //send email
        try {
            $adminEmail = getSetting('email');
            if ($adminEmail) {
                \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\ContactEmail(
                    $name,
                    $request->email,
                    $message,
                    $subject
                ));
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'action' => 'reset'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('Your message has been dispatched to our support team. We will contact you shortly.'),
            'action' => 'reset'
        ]);
    }


    // privacy policy
    public function privacyPolicy()
    {
        $template = config('site.template');
        $page_title = __("Privacy Policy");
        $page_description = __(":name is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website.", ['name' => getSetting('name')]);
        return view('templates.' . $template . '.blades.pages.privacy-policy', compact(
            'page_title',
            'page_description'
        ));
    }

    // terms and conditions
    public function termsAndConditions()
    {
        $template = config('site.template');
        $page_title = __("Terms and Conditions");
        $page_description = __("Our Terms and Conditions outline the rules and guidelines for using our website and services. By accessing or using our website, you agree to be bound by these terms.", ['name' => getSetting('name')]);
        return view('templates.' . $template . '.blades.pages.terms-and-conditions', compact(
            'page_title',
            'page_description'
        ));
    }

    // risk disclosure
    public function riskDisclosure()
    {
        $template = config('site.template');
        $page_title = __("Risk Disclosure");
        $page_description = __("Our Risk Disclosure outlines the risks associated with investing in our platform. By investing, you acknowledge and accept these risks.", ['name' => getSetting('name')]);
        return view('templates.' . $template . '.blades.pages.risk-disclosure', compact(
            'page_title',
            'page_description'
        ));
    }

}
