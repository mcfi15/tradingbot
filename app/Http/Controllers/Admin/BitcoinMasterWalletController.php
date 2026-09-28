<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\MasterWalletSyncService;
use App\Models\Blockchain;
use App\Services\BitcoinWalletGeneratorService;
use App\Services\BitcoinTransactionSigner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitcoinMasterWalletController extends Controller
{
    public function index()
    {
        $blockchain = Blockchain::where('code', 'bitcoin')->firstOrFail();
        $page_title = __('Bitcoin Master Wallet Setup & Status');
        $template = config('site.template');

        $supportedTokens = $blockchain->tokens()->where('status', 'enabled')->get();
        $supportedTokenBalances = [];
        $nativeBalance = 0.0;
        $rpcStatus = __('Unavailable');

        if (!empty($blockchain->master_wallet_address)) {
            try {
                $rpcUrl = $blockchain->rpc_url ?: 'https://blockstream.info/api';
                $utxos = BitcoinTransactionSigner::fetchUtxos($rpcUrl, $blockchain->master_wallet_address);
                $rpcStatus = __('Connected');

                // Sum UTXOs to get native balance
                $totalSat = 0;
                foreach ($utxos as $utxo) {
                    $totalSat += (int) $utxo['value'];
                }
                $nativeBalance = $totalSat / 100000000;

                foreach ($supportedTokens as $token) {
                    if (empty($token->mint_address)) {
                        $supportedTokenBalances[$token->id] = $nativeBalance;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to fetch Bitcoin index balances: ' . $e->getMessage());
            }
        }

        return view('templates.' . $template . '.blades.admin.bitcoin.index', compact(
            'page_title',
            'blockchain',
            'supportedTokens',
            'supportedTokenBalances',
            'nativeBalance',
            'rpcStatus'
        ));
    }

    public function generate(Request $request)
    {
        $blockchain = Blockchain::where('code', 'bitcoin')->firstOrFail();
        $force = $request->boolean('force', false);

        if ($blockchain->master_wallet_address && !$force) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Bitcoin master wallet already exists.'),
                ], 400);
            }
            return back()->with('error', __('Bitcoin master wallet already exists.'));
        }

        try {
            $generator = new BitcoinWalletGeneratorService();
            $wallet = $generator->generateWallet();

            $blockchain->master_wallet_address = $wallet['address'];
            $blockchain->master_private_key = Crypt::encryptString($wallet['private_key']);
            $blockchain->save();

            // After wallet creation, provision any other missing blockchain master wallets.
            (new MasterWalletSyncService())->syncMissingMasterWallets();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Bitcoin master wallet generated successfully.'),
                    'address' => $wallet['address'],
                ]);
            }

            return back()->with('success', __('Bitcoin master wallet generated successfully.'));
        } catch (\Exception $e) {
            Log::error('Failed to generate Bitcoin master wallet: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Failed to generate Bitcoin master wallet: ') . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', __('Failed to generate Bitcoin master wallet: ') . $e->getMessage());
        }
    }

    public function reveal(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password:admin',
        ]);

        $blockchain = Blockchain::where('code', 'bitcoin')->firstOrFail();
        $privateKeyEncrypted = $blockchain->master_private_key;

        if (!$privateKeyEncrypted) {
            return response()->json([
                'status' => 'error',
                'message' => __('Bitcoin master wallet private key not found.'),
            ], 404);
        }

        try {
            $privateKey = Crypt::decryptString($privateKeyEncrypted);

            return response()->json([
                'status' => 'success',
                'private_key' => $privateKey,
            ]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Failed to decrypt Bitcoin master wallet private key: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => __('Failed to decrypt the private key. Make sure the APP_KEY has not changed.'),
            ], 500);
        }
    }

    public function history()
    {
        $blockchain = Blockchain::where('code', 'bitcoin')->firstOrFail();
        $address = $blockchain->master_wallet_address;

        if (!$address) {
            return redirect()->route('admin.bitcoin-master-wallet.index')
                ->with('error', __('Bitcoin master wallet has not been setup yet.'));
        }

        $page_title = __('Bitcoin Master Wallet Details & History');
        $template = config('site.template');

        $balance = 0.0;
        $formattedBalance = '0.00000000 BTC';
        $rpcStatus = __('Connected');
        $onChainTransactions = [];

        try {
            $rpcUrl = $blockchain->rpc_url ?: 'https://blockstream.info/api';

            // 1. Fetch balance via UTXOs
            $utxos = BitcoinTransactionSigner::fetchUtxos($rpcUrl, $address);
            $totalSat = 0;
            foreach ($utxos as $utxo) {
                $totalSat += (int) $utxo['value'];
            }
            $balance = $totalSat / 100000000;
            $formattedBalance = number_format($balance, 8) . ' BTC';

            // 2. Fetch transactions via Esplora address endpoint
            $txResponse = Http::withoutVerifying()->timeout(12)->get($rpcUrl . '/address/' . $address . '/txs');
            if ($txResponse->successful()) {
                $txList = $txResponse->json() ?: [];
                foreach ($txList as $tx) {
                    $txid = $tx['txid'] ?? '';
                    $blockTime = $tx['status']['block_time'] ?? time();
                    $confirmed = $tx['status']['confirmed'] ?? false;
                    $blockHeight = $tx['status']['block_height'] ?? null;

                    // Calculate net amount for this address
                    $inputVal = 0;
                    $outputVal = 0;
                    $isSender = false;

                    foreach (($tx['vin'] ?? []) as $in) {
                        if (($in['prevout']['scriptpubkey_address'] ?? '') === $address) {
                            $inputVal += $in['prevout']['value'] ?? 0;
                            $isSender = true;
                        }
                    }

                    foreach (($tx['vout'] ?? []) as $out) {
                        if (($out['scriptpubkey_address'] ?? '') === $address) {
                            $outputVal += $out['value'] ?? 0;
                        }
                    }

                    $netValSat = $outputVal - $inputVal;
                    $direction = $netValSat >= 0 ? 'in' : 'out';
                    $amount = abs($netValSat) / 100000000;
                    $fee = ($tx['fee'] ?? 0) / 100000000;

                    $onChainTransactions[] = [
                        'hash' => $txid,
                        'direction' => $direction,
                        'amount' => $amount,
                        'fee' => $fee,
                        'timestamp' => Carbon::createFromTimestamp($blockTime),
                        'status' => $confirmed ? __('Confirmed') : __('Pending'),
                        'block' => $blockHeight,
                        'explorer_url' => str_replace('{address}', $txid, 'https://blockstream.info/tx/{address}'),
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to load Bitcoin on-chain history: ' . $e->getMessage());
            $rpcStatus = __('Unavailable');
        }

        return view('templates.' . $template . '.blades.admin.bitcoin.history', compact(
            'page_title',
            'blockchain',
            'address',
            'balance',
            'formattedBalance',
            'rpcStatus',
            'onChainTransactions'
        ));
    }
}
