<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class PublicAssetController extends Controller
{
    /**
     * Show the public search page
     */
    public function search()
    {
        return view('public.asset_search');
    }

    /**
     * Handle the search submission
     */
    public function find(Request $request)
    {
        $request->validate(['code_asset' => 'required|string']);
        
        $code = preg_replace('/[^0-9a-zA-Z]/', '', $request->code_asset);
        $asset = Asset::where('code_asset', $code)->first();
        if (!$asset) {
            return back()->with('error', 'Asset dengan kode tersebut tidak ditemukan.');
        }

        return redirect()->route('public.asset.show', $asset->code_asset);
    }

    /**
     * Show the public asset details
     */
    public function show($code)
    {
        $asset = Asset::with(['brand', 'deviceModel', 'location', 'group', 'employee', 'ram', 'rom', 'cpu', 'vga'])
            ->where('code_asset', $code)
            ->firstOrFail();

        return view('public.asset_show', compact('asset'));
    }
}
