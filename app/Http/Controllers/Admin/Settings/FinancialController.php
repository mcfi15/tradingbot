<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    // index
    public function index()
    {
        $page_title = __('Financial Settings');
        $template = config('site.template');

        // Currencies list
        $currencies = json_decode(file_get_contents(public_path('assets/json/currencies.json')), true);

        return view("templates.$template.blades.admin.settings.financial", compact('page_title', 'template', 'currencies'));
    }

    // update
    public function update(Request $request)
    {
        $request->validate([
            // Platform Currency
            'currency_name' => 'required|string|max:50',
            'currency_symbol' => 'required|string|max:10',
            'currency_position' => 'required|in:before,after',
            'decimal_places' => 'required|integer|min:0|max:8',

            // Transactions
            'min_deposit' => 'required|numeric|min:0',
            'max_deposit' => 'required|numeric|min:0',
            'deposit_fee' => 'required|numeric|min:0',
            'deposit_expires_at' => 'required|integer|min:1',
            'min_withdrawal' => 'required|numeric|min:0',
            'max_withdrawal' => 'required|numeric|min:0',
            'withdrawal_fee' => 'required|numeric|min:0',
        ]);

        // Currency Settings
        updateSetting('currency', $request->currency_name);
        updateSetting('currency_symbol', $request->currency_symbol);
        updateSetting('currency_symbol_position', $request->currency_position);
        updateSetting('decimal_places', $request->decimal_places);

        // Transaction Settings
        updateSetting('min_deposit', $request->min_deposit);
        updateSetting('max_deposit', $request->max_deposit);
        updateSetting('deposit_fee', $request->deposit_fee);
        updateSetting('deposit_expires_at', $request->deposit_expires_at);
        updateSetting('min_withdrawal', $request->min_withdrawal);
        updateSetting('max_withdrawal', $request->max_withdrawal);
        updateSetting('withdrawal_fee', $request->withdrawal_fee);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('Financial settings updated successfully.')
            ]);
        }

        return back()->with('success', __('Financial settings updated successfully.'));
    }
}
