<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blockchain;
use App\Models\BlockchainToken;

class BlockchainController extends Controller
{
    public function index()
    {
        $page_title = __('Blockchain Settings');
        $template = config('site.template');

        $blockchains = Blockchain::with(['tokens' => function ($q) {
            $q->orderBy('priority', 'asc')->orderBy('id', 'asc');
        }])
            ->orderByRaw('priority IS NULL, priority ASC')
            ->orderBy('id', 'asc')
            ->get();

        return view('templates.' . $template . '.blades.admin.settings.blockchain.index', compact(
            'page_title',
            'template',
            'blockchains'
        ));
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|exists:blockchains,id'
        ]);

        foreach ($request->items as $index => $id) {
            Blockchain::where('id', $id)->update(['priority' => ($index + 1) * 10]);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('Blockchain priority order updated successfully.')
        ]);
    }

    // Blockchain management actions
    public function blockchainStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:blockchains,code|max:255',
            'rpc_url' => 'required|url',
            'status' => 'required|in:enabled,disabled',
            'instructions' => 'nullable|string',
            'explorer_url_live' => 'nullable|string|max:500',
            'explorer_url_devnet' => 'nullable|string|max:500',
        ]);

        Blockchain::create([
            'name' => $request->name,
            'code' => strtolower($request->code),
            'rpc_url' => $request->rpc_url,
            'status' => $request->status,
            'instructions' => $request->instructions,
            'explorer_url_live' => $request->explorer_url_live,
            'explorer_url_devnet' => $request->explorer_url_devnet,
        ]);

        return back()->with('success', __('Blockchain added successfully.'));
    }

    public function blockchainUpdate(Request $request, $id)
    {
        $blockchain = Blockchain::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:blockchains,code,' . $id,
            'rpc_url' => 'required|url',
            'instructions' => 'nullable|string',
            'explorer_url_live' => 'nullable|string|max:500',
            'explorer_url_devnet' => 'nullable|string|max:500',
        ]);

        $blockchain->update([
            'name' => $request->name,
            'code' => strtolower($request->code),
            'rpc_url' => $request->rpc_url,
            'instructions' => $request->instructions,
            'explorer_url_live' => $request->explorer_url_live,
            'explorer_url_devnet' => $request->explorer_url_devnet,
        ]);

        // If Solana RPC URL is updated, sync it with settings tab key for backward compatibility
        if ($blockchain->code === 'solana') {
            updateSetting('solana_rpc_url', $request->rpc_url);
        }

        return back()->with('success', __('Blockchain updated successfully.'));
    }

    public function blockchainToggleStatus(Request $request, $id)
    {
        $blockchain = Blockchain::findOrFail($id);
        $blockchain->status = $blockchain->status === 'enabled' ? 'disabled' : 'enabled';
        $blockchain->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Blockchain status updated successfully.'),
            'new_status' => $blockchain->status
        ]);
    }

    // Token management actions
    public function tokenStore(Request $request)
    {
        $request->validate([
            'blockchain_id' => 'required|exists:blockchains,id',
            'symbol' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'mint_address' => 'nullable|string|max:255',
            'decimals' => 'required|integer|min:0|max:18',
            'status' => 'required|in:enabled,disabled',
        ]);

        BlockchainToken::create([
            'blockchain_id' => $request->blockchain_id,
            'symbol' => strtoupper($request->symbol),
            'name' => $request->name,
            'mint_address' => $request->mint_address,
            'decimals' => $request->decimals,
            'status' => $request->status,
        ]);

        return back()->with('success', __('Token added successfully.'));
    }

    public function tokenUpdate(Request $request, $id)
    {
        $token = BlockchainToken::findOrFail($id);

        $request->validate([
            'symbol' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'mint_address' => 'nullable|string|max:255',
            'decimals' => 'required|integer|min:0|max:18',
        ]);

        $token->update([
            'symbol' => strtoupper($request->symbol),
            'name' => $request->name,
            'mint_address' => $request->mint_address,
            'decimals' => $request->decimals,
        ]);

        return back()->with('success', __('Token updated successfully.'));
    }

    public function tokenToggleStatus(Request $request, $id)
    {
        $token = BlockchainToken::findOrFail($id);
        $token->status = $token->status === 'enabled' ? 'disabled' : 'enabled';
        $token->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Token status updated successfully.'),
            'new_status' => $token->status
        ]);
    }

    public function tokenDelete($id)
    {
        $token = BlockchainToken::findOrFail($id);
        $token->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Token deleted successfully.')
        ]);
    }
}
