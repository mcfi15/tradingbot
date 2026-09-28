<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    private array $purgedModules = [
        'loan_module',
        'p2p_transfer_module',
        'investment_module',
        'stock_module',
        'margin_module',
        'futures_module',
        'forex_module',
        'etf_module',
        'bonds_module',
    ];

    /**
     * Define the available modules and their default configuration.
     * This serves as the source of truth for metadata (name, description, menu_search).
     */
    private function getAvailableModules()
    {
        $modules = getSetting('modules');
        $modulesArray = [];
        if ($modules !== null) {
            $modulesArray = json_decode($modules, true) ?? [];
        } elseif (file_exists(public_path('assets/json/modules.json'))) {
            $modulesArray = json_decode(file_get_contents(public_path('assets/json/modules.json')), true) ?? [];
        }

        foreach ($this->purgedModules as $purged) {
            unset($modulesArray[$purged]);
        }

        return $modulesArray;
    }

    //index
    public function index()
    {
        $page_title = __('Modules');
        $template = config('site.template');
        $modules = $this->getAvailableModules();

        return view("templates.{$template}.blades.admin.settings.modules", compact(
            'page_title',
            'modules',
            'template'
        ));
    }

    public function update(Request $request)
    {
        $availableModules = $this->getAvailableModules();
        $storedModules = json_decode(getSetting('modules'), true) ?? [];
        $inputModules = $request->input('modules', []);

        // Base modules for saving
        $modulesToSave = $availableModules;

        foreach ($modulesToSave as $key => &$module) {
            // Apply stored status first (to preserve existing state for modules not in the request)
            if (isset($storedModules[$key]['status'])) {
                $module['status'] = $storedModules[$key]['status'];
            }

            // Apply new status from request
            if (isset($inputModules[$key]) && $inputModules[$key] === 'enabled') {
                $module['status'] = 'enabled';
            } else {
                $module['status'] = 'disabled';
            }

            // Sync with MenuItem table
            if (isset($module['menu_search'])) {
                $status = ($module['status'] === 'enabled');
                $searches = $module['menu_search']; // Now guaranteed to be array of arrays

                foreach ($searches as $search) {
                    MenuItem::where($search['column'], 'like', '%' . $search['term'] . '%')
                        ->update(['is_active' => $status]);
                }
            }

        }

        updateSetting('modules', $modulesToSave); //moved up because of cache

        // Remove menu from cache
        cache()->forget('admin_menu_items');
        cache()->forget('user_menu_items');

        file_put_contents(public_path('assets/json/modules.json'), json_encode($modulesToSave, JSON_PRETTY_PRINT));



        return response()->json(['message' => __('Modules updated successfully')]);
    }

}
