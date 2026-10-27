<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Brand;
use App\Models\DeviceModel;
use App\Models\Location;
use App\Models\Group;
use App\Models\Employee;
use App\Models\RamSpec;
use App\Models\RomSpec;
use App\Models\CpuSpec;
use App\Models\VgaSpec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    protected function getMasterData()
    {
        return [
            'brands' => Brand::orderBy('name')->get(),
            'deviceModels' => DeviceModel::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'groups' => Group::orderBy('name')->get(),
            'employees' => Employee::orderBy('name')->get(),
            'ramSpecs' => RamSpec::orderBy('spec')->get(),
            'romSpecs' => RomSpec::orderBy('spec')->get(),
            'cpuSpecs' => CpuSpec::orderBy('spec')->get(),
            'vgaSpecs' => VgaSpec::orderBy('spec')->get(),
        ];
    }

    public function index(string $type)
    {
        abort_unless(array_key_exists($type, Asset::TYPES), 404);
        $assets = Asset::where('asset_type', $type)
            ->with(['brand', 'deviceModel', 'location', 'group', 'employee'])
            ->latest()
            ->paginate(15);

        $typeLabel = Asset::TYPES[$type];
        $typeIcon = Asset::TYPE_ICONS[$type];
        return view('assets.index', compact('assets', 'type', 'typeLabel', 'typeIcon'));
    }

    public function global(Request $request)
    {
        $query = Asset::with(['brand', 'deviceModel', 'location', 'group', 'employee'])
            ->latest();

        // Filter: teks bebas (code, brand, model, employee)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code_asset', 'like', "%{$search}%")
                  ->orWhereHas('brand',       fn($b) => $b->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('deviceModel', fn($b) => $b->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('employee',    fn($b) => $b->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter: tipe asset
        if ($type = $request->input('type')) {
            $query->where('asset_type', $type);
        }

        // Filter: lokasi
        if ($locationId = $request->input('location_id')) {
            $query->where('location_id', $locationId);
        }

        // Filter: group
        if ($groupId = $request->input('group_id')) {
            $query->where('group_id', $groupId);
        }

        // Filter: status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $assets    = $query->paginate(25)->withQueryString();
        $locations = Location::orderBy('name')->get();
        $groups    = Group::orderBy('name')->get();
        $filters   = $request->only(['search', 'type', 'location_id', 'group_id', 'status']);

        return view('assets.global', compact('assets', 'locations', 'groups', 'filters'));
    }

    public function create(string $type)
    {
        abort_unless(array_key_exists($type, Asset::TYPES), 404);
        $typeLabel = Asset::TYPES[$type];
        $masterData = $this->getMasterData();
        return view('assets.create', compact('type', 'typeLabel') + $masterData);
    }

    public function store(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, Asset::TYPES), 404);

        $validated = $request->validate([
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:10240',
            'access_code' => 'nullable|string|max:255',
            'brand_id' => 'nullable|exists:brands,id',
            'device_model_id' => 'nullable|exists:device_models,id',
            'location_id' => 'required|exists:locations,id',
            'group_id' => 'nullable|exists:groups,id',
            'employee_id' => 'nullable|exists:employees,id',
            'ram_id' => 'nullable|exists:ram_specs,id',
            'rom_id' => 'nullable|exists:rom_specs,id',
            'cpu_id' => 'nullable|exists:cpu_specs,id',
            'vga_id' => 'nullable|exists:vga_specs,id',
        ]);

        $validated['asset_type'] = $type;

        // Auto-generate 9-digit code
        $location = \App\Models\Location::findOrFail($validated['location_id']);
        $validated['code_asset'] = Asset::generateCode($location->outlet_code ?? $location->id, $type);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('assets', 'public');
        }

        Asset::create($validated);
        return redirect()->route('assets.type', $type)->with('success', 'Asset berhasil ditambahkan!');
    }

    public function show(Asset $asset)
    {
        $asset->load(['brand', 'deviceModel', 'location', 'group', 'employee', 'ram', 'rom', 'cpu', 'vga']);
        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $type = $asset->asset_type;
        $typeLabel = Asset::TYPES[$type];
        $masterData = $this->getMasterData();
        return view('assets.edit', compact('asset', 'type', 'typeLabel') + $masterData);
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'code_asset' => 'required|unique:assets,code_asset,' . $asset->id,
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:10240',
            'access_code' => 'nullable|string|max:255',
            'brand_id' => 'nullable|exists:brands,id',
            'device_model_id' => 'nullable|exists:device_models,id',
            'location_id' => 'nullable|exists:locations,id',
            'group_id' => 'nullable|exists:groups,id',
            'employee_id' => 'nullable|exists:employees,id',
            'ram_id' => 'nullable|exists:ram_specs,id',
            'rom_id' => 'nullable|exists:rom_specs,id',
            'cpu_id' => 'nullable|exists:cpu_specs,id',
            'vga_id' => 'nullable|exists:vga_specs,id',
        ]);

        if ($request->hasFile('photo')) {
            if ($asset->photo) Storage::disk('public')->delete($asset->photo);
            $validated['photo'] = $request->file('photo')->store('assets', 'public');
        }

        $asset->update($validated);
        return redirect()->route('assets.type', $asset->asset_type)->with('success', 'Asset berhasil diupdate!');
    }

    public function destroy(Asset $asset)
    {
        $type = $asset->asset_type;
        if ($asset->photo) Storage::disk('public')->delete($asset->photo);
        $asset->delete();
        return redirect()->route('assets.type', $type)->with('success', 'Asset berhasil dihapus!');
    }

    public function importForm(string $type, Request $request)
    {
        abort_unless(array_key_exists($type, Asset::TYPES), 404);
        $typeLabel = Asset::TYPES[$type];
        return view('assets.import', compact('type', 'typeLabel'));
    }

    /**
     * Step 1: Upload CSV → detect headers → show mapping UI
     * Step 2 (POST to import.store): Process with mapping
     */
    public function import(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, Asset::TYPES), 404);
        $typeLabel = Asset::TYPES[$type];

        $request->validate(['file' => 'required|file|mimes:csv,txt|max:10240']);

        // Store temp file so it survives to step 2
        \Illuminate\Support\Facades\Storage::disk('local')->makeDirectory('imports_tmp');
        $tmpPath  = $request->file('file')->store('imports_tmp', 'local');
        $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($tmpPath);

        // Parse headers and preview rows
        $handle  = fopen($fullPath, 'r');
        $headers = fgetcsv($handle, 0, ',');
        if (!$headers || count($headers) < 2) {
            // Try semicolon separator
            rewind($handle);
            $headers = fgetcsv($handle, 0, ';');
        }
        $headers = array_map('trim', $headers ?? []);

        // Read a few preview rows
        $preview = [];
        for ($i = 0; $i < 3; $i++) {
            $row = fgetcsv($handle, 0, count($headers) > 1 ? ',' : ';');
            if ($row) $preview[] = array_map('trim', $row);
        }
        fclose($handle);

        return view('assets.import', compact('type', 'typeLabel', 'headers', 'preview'))
            ->with('tmpFile', $tmpPath);
    }

    public function importProcess(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, Asset::TYPES), 404);

        $request->validate([
            'tmp_file'          => 'required|string',
            'mapping'           => 'required|array',
            'mapping.lokasi'    => 'required', // lokasi wajib dipetakan
        ]);

        $tmpPath  = $request->input('tmp_file');
        $mapping  = $request->input('mapping');
        $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($tmpPath);

        if (!file_exists($fullPath)) {
            return back()->withErrors(['tmp_file' => 'File tidak ditemukan. Silakan upload ulang.']);
        }

        $handle  = fopen($fullPath, 'r');
        $headers = fgetcsv($handle, 0, ','); // skip header row
        $headers = array_map('trim', $headers ?? []);

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $rowNum   = 1;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rowNum++;
            $row = array_map('trim', $row);

            // Get value by mapped column index
            $get = function(string $field) use ($row, $mapping): string {
                $idx = $mapping[$field] ?? '';
                if ($idx === '' || $idx === null) return '';
                return isset($row[(int)$idx]) ? trim($row[(int)$idx]) : '';
            };

            try {
                // --- Location (required) ---
                $locationName = $get('lokasi');
                if (!$locationName) { $skipped++; continue; }
                $location = Location::firstOrCreate(
                    ['name' => $locationName],
                    ['address' => '', 'outlet_code' => (Location::max('outlet_code') ?? 0) + 1]
                );

                // --- Group ---
                $groupName = $get('grup');
                $group = $groupName ? Group::firstOrCreate(['name' => $groupName]) : null;

                // --- Employee ---
                $empName  = $get('user');
                $employee = null;
                if ($empName) {
                    $empData = ['name' => $empName];
                    if ($group) $empData['group_id'] = $group->id;
                    $employee = Employee::firstOrCreate($empData, ['jabatan' => '']);
                }

                // --- Brand (from mapping or fallback) ---
                $brandName   = $get('brand');
                $brand       = null;
                $deviceModel = null;

                // --- Device Model & Brand ---
                $modelName = $get('model');
                if ($modelName) {
                    $deviceModel = DeviceModel::where('name', $modelName)->first();
                    if (!$deviceModel) {
                        // Use mapped brand name, or 'Unknown' as fallback
                        $brand = $brandName
                            ? Brand::firstOrCreate(['name' => $brandName])
                            : Brand::firstOrCreate(['name' => 'Unknown']);
                        $deviceModel = DeviceModel::create(['name' => $modelName, 'brand_id' => $brand->id]);
                    } else {
                        $brand = $deviceModel->brand;
                        // Update brand if a more specific one was provided
                        if ($brandName && $brand->name === 'Unknown') {
                            $brand = Brand::firstOrCreate(['name' => $brandName]);
                        }
                    }
                } elseif ($brandName) {
                    // Brand only, no model
                    $brand = Brand::firstOrCreate(['name' => $brandName]);
                }

                // --- Specs ---
                $cpuName = $get('processor');
                $cpu     = $cpuName ? CpuSpec::firstOrCreate(['spec' => $cpuName]) : null;

                $ramRaw = $get('memory_type');
                $ram    = $ramRaw ? RamSpec::firstOrCreate(['spec' => $ramRaw]) : null;

                $romRaw = $get('hard_drive');
                $rom    = $romRaw ? RomSpec::firstOrCreate(['spec' => $romRaw]) : null;

                // --- Asset Code ---
                $code = Asset::generateCode($location->outlet_code ?? $location->id, $type);

                Asset::create([
                    'asset_type'      => $type,
                    'code_asset'      => $code,
                    'location_id'     => $location->id,
                    'group_id'        => $group?->id,
                    'employee_id'     => $employee?->id,
                    'brand_id'        => $brand?->id,
                    'device_model_id' => $deviceModel?->id,
                    'cpu_id'          => $cpu?->id,
                    'ram_id'          => $ram?->id,
                    'rom_id'          => $rom?->id,
                    'description'     => $get('keterangan'),
                ]);

                $imported++;
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = "Baris {$rowNum}: " . $e->getMessage();
            }
        }

        fclose($handle);
        \Illuminate\Support\Facades\Storage::delete($tmpPath); // cleanup

        $msg = "✅ Import selesai! {$imported} data berhasil diimport.";
        if ($skipped) $msg .= " {$skipped} baris dilewati.";
        if ($errors)  $msg .= " Errors: " . implode(' | ', array_slice($errors, 0, 3));

        return redirect()->route('assets.type', $type)->with('success', $msg);
    }
}
