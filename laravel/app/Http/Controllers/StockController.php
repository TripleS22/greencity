<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockHistory;
use App\Models\AssetPurnaGuna;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    // ── Dashboard ───────────────────────────────
    public function dashboard()
    {
        $totalItems = Stock::count();
        $totalValue = Stock::sum(DB::raw('unit_price * total_stock'));
        $lowStock = Stock::whereColumn('total_stock', '<=', 'minimum_limit')->get();
        $topMoving = StockHistory::select('stock_id', DB::raw('SUM(quantity) as total_qty'))
            ->where('type', 'out')
            ->groupBy('stock_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('stock')
            ->get();

        return view('stocks.dashboard', compact('totalItems', 'totalValue', 'lowStock', 'topMoving'));
    }

    // ── Stock Opname (main CRUD) ────────────────
    public function index()
    {
        $stocks = Stock::with(['location', 'user'])->latest()->get();
        return view('stocks.index', compact('stocks'));
    }

    public function create()
    {
        $locations = Location::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('stocks.create', compact('locations', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'unit_type' => 'nullable|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'stock_in' => 'required|integer|min:0',
            'stock_out' => 'required|integer|min:0',
            'minimum_limit' => 'required|integer|min:0',
            'location_id' => 'nullable|exists:locations,id',
            'user_id' => 'nullable|exists:users,id',
        ]);
        $validated['total_stock'] = $validated['stock_in'] - $validated['stock_out'];

        $stock = Stock::create($validated);

        // Create initial history entries
        if ($validated['stock_in'] > 0) {
            StockHistory::create([
                'stock_id' => $stock->id, 'type' => 'in', 'quantity' => $validated['stock_in'],
                'date' => now(), 'notes' => 'Initial stock in'
            ]);
        }

        return redirect()->route('stocks.index')->with('success', 'Stock berhasil ditambahkan!');
    }

    public function edit(Stock $stock)
    {
        $locations = Location::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('stocks.edit', compact('stock', 'locations', 'users'));
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'unit_type' => 'nullable|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'stock_in' => 'required|integer|min:0',
            'stock_out' => 'required|integer|min:0',
            'minimum_limit' => 'required|integer|min:0',
            'location_id' => 'nullable|exists:locations,id',
            'user_id' => 'nullable|exists:users,id',
        ]);
        $validated['total_stock'] = $validated['stock_in'] - $validated['stock_out'];
        $stock->update($validated);
        return redirect()->route('stocks.index')->with('success', 'Stock berhasil diupdate!');
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();
        return redirect()->route('stocks.index')->with('success', 'Stock berhasil dihapus!');
    }

    public function show(Stock $stock)
    {
        $histories = $stock->histories()->latest('date')->get();
        return view('stocks.show', compact('stock', 'histories'));
    }

    // ── Stock History ───────────────────────────
    public function history()
    {
        $histories = StockHistory::with('stock')->latest()->get();
        $locations = \App\Models\Location::orderBy('name')->get();
        $employees = \App\Models\Employee::orderBy('name')->get();
        $brands = \App\Models\Brand::orderBy('name')->get();
        $deviceModels = \App\Models\DeviceModel::orderBy('name')->get();
        $assetTypes = \App\Models\Asset::TYPES;

        return view('stocks.history', compact('histories', 'locations', 'employees', 'brands', 'deviceModels', 'assetTypes'));
    }

    public function historyStore(Request $request)
    {
        $validated = $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'is_create_asset' => 'nullable|boolean',
            'asset_type' => 'required_if:is_create_asset,1|nullable|string',
            'assets_data' => 'required_if:is_create_asset,1|nullable|array',
            'assets_data.*.location_id' => 'required_if:is_create_asset,1|nullable|exists:locations,id',
            'assets_data.*.employee_id' => 'nullable|exists:employees,id',
            'assets_data.*.brand_id' => 'nullable|exists:brands,id',
            'assets_data.*.device_model_id' => 'nullable|exists:device_models,id',
        ]);

        $stockHistory = StockHistory::create([
            'stock_id' => $validated['stock_id'],
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'date' => $validated['date'],
            'notes' => $validated['notes'],
        ]);

        // Update stock totals
        $stock = Stock::findOrFail($validated['stock_id']);
        if ($validated['type'] === 'in') {
            $stock->increment('stock_in', $validated['quantity']);
            $stock->increment('total_stock', $validated['quantity']);
        } else {
            $stock->increment('stock_out', $validated['quantity']);
            $stock->decrement('total_stock', $validated['quantity']);

            // Handle Asset Creation
            if (!empty($validated['is_create_asset']) && !empty($validated['asset_type']) && !empty($validated['assets_data'])) {
                foreach ($validated['assets_data'] as $data) {
                    $location = Location::findOrFail($data['location_id']);
                    $code = \App\Models\Asset::generateCode($location->outlet_code ?? $location->id, $validated['asset_type']);

                    \App\Models\Asset::create([
                        'asset_type' => $validated['asset_type'],
                        'code_asset' => $code,
                        'description' => "Asset dari Stock Out: " . $stock->item_name,
                        'location_id' => $data['location_id'],
                        'employee_id' => $data['employee_id'] ?? null,
                        'brand_id' => $data['brand_id'] ?? null,
                        'device_model_id' => $data['device_model_id'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('stocks.history')->with('success', 'History berhasil ditambahkan!');
    }

    // ── Asset Purna Guna ────────────────────────
    public function purnaGuna()
    {
        $items = AssetPurnaGuna::latest()->paginate(15);
        return view('stocks.purna_guna.index', compact('items'));
    }

    public function purnaGunaCreate() { return view('stocks.purna_guna.create'); }

    public function purnaGunaStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'price_range' => 'nullable|string',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:sold,disposed,warehouse',
        ]);
        AssetPurnaGuna::create($validated);
        return redirect()->route('stocks.purna_guna')->with('success', 'Data berhasil ditambahkan!');
    }

    public function purnaGunaEdit(AssetPurnaGuna $assetPurnaGuna)
    {
        return view('stocks.purna_guna.edit', ['item' => $assetPurnaGuna]);
    }

    public function purnaGunaUpdate(Request $request, AssetPurnaGuna $assetPurnaGuna)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'price_range' => 'nullable|string',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:sold,disposed,warehouse',
        ]);
        $assetPurnaGuna->update($validated);
        return redirect()->route('stocks.purna_guna')->with('success', 'Data berhasil diupdate!');
    }

    public function purnaGunaDestroy(AssetPurnaGuna $assetPurnaGuna)
    {
        $assetPurnaGuna->delete();
        return redirect()->route('stocks.purna_guna')->with('success', 'Data berhasil dihapus!');
    }
}
