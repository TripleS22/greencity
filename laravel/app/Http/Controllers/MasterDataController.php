<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\DeviceModel;
use App\Models\Location;
use App\Models\Group;
use App\Models\RamSpec;
use App\Models\RomSpec;
use App\Models\CpuSpec;
use App\Models\VgaSpec;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    // ── Brands ──────────────────────────────────
    public function brands() { return view('master.brands.index', ['items' => Brand::latest()->paginate(9999)]); }
    public function brandsCreate() { return view('master.brands.create'); }
    public function brandsStore(Request $r) {
        $r->validate(['name' => 'required|string|max:255']);
        Brand::create($r->only('name'));
        return redirect()->route('master.brands')->with('success', 'Brand berhasil ditambahkan!');
    }
    public function brandsEdit(Brand $brand) { return view('master.brands.edit', compact('brand')); }
    public function brandsUpdate(Request $r, Brand $brand) {
        $r->validate(['name' => 'required|string|max:255']);
        $brand->update($r->only('name'));
        return redirect()->route('master.brands')->with('success', 'Brand berhasil diupdate!');
    }
    public function brandsDestroy(Brand $brand) {
        $brand->delete();
        return redirect()->route('master.brands')->with('success', 'Brand berhasil dihapus!');
    }

    // ── Device Models ───────────────────────────
    public function deviceModels() {
        return view('master.device_models.index', ['items' => DeviceModel::with('brand')->latest()->paginate(9999), 'brands' => Brand::orderBy('name')->get()]);
    }
    public function deviceModelsCreate() {
        return view('master.device_models.create', ['brands' => Brand::orderBy('name')->get()]);
    }
    public function deviceModelsStore(Request $r) {
        $r->validate(['name' => 'required|string|max:255', 'brand_id' => 'required|exists:brands,id']);
        DeviceModel::create($r->only('name', 'brand_id'));
        return redirect()->route('master.device_models')->with('success', 'Model berhasil ditambahkan!');
    }
    public function deviceModelsEdit(DeviceModel $deviceModel) {
        return view('master.device_models.edit', ['item' => $deviceModel, 'brands' => Brand::orderBy('name')->get()]);
    }
    public function deviceModelsUpdate(Request $r, DeviceModel $deviceModel) {
        $r->validate(['name' => 'required|string|max:255', 'brand_id' => 'required|exists:brands,id']);
        $deviceModel->update($r->only('name', 'brand_id'));
        return redirect()->route('master.device_models')->with('success', 'Model berhasil diupdate!');
    }
    public function deviceModelsDestroy(DeviceModel $deviceModel) {
        $deviceModel->delete();
        return redirect()->route('master.device_models')->with('success', 'Model berhasil dihapus!');
    }

    // ── Locations ───────────────────────────────
    public function locations() { return view('master.locations.index', ['items' => Location::latest()->paginate(9999)]); }
    public function locationsCreate() { return view('master.locations.create'); }
    public function locationsStore(Request $r) {
        $r->validate(['name' => 'required|string|max:255', 'address' => 'nullable|string']);
        $location = Location::create($r->only('name', 'address'));

        // Otomatis buat jadwal maintenance pertama (didistribusikan secara acak atau di akhir siklus 90 hari)
        \App\Models\Maintenance::create([
            'location_id' => $location->id,
            'category' => 'all',
            'description' => 'Scheduled Maintenance (Auto-Generated untuk Lokasi Baru)',
            // Tempatkan secara acak dalam 90 hari ke depan agar tidak menumpuk
            'date' => now()->addDays(rand(1, 90)),
        ]);

        return redirect()->route('master.locations')->with('success', 'Lokasi berhasil ditambahkan beserta jadwal perdananya!');
    }
    public function locationsShow(Location $location) {
        $maintenances = \App\Models\Maintenance::where('location_id', $location->id)->orderBy('date', 'desc')->get();
        
        $tickets = \App\Models\Ticket::where('location_id', $location->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('master.locations.show', compact('location', 'maintenances', 'tickets'));
    }
    public function locationsEdit(Location $location) { return view('master.locations.edit', compact('location')); }
    public function locationsUpdate(Request $r, Location $location) {
        $r->validate(['name' => 'required|string|max:255', 'address' => 'nullable|string']);
        $location->update($r->only('name', 'address'));
        return redirect()->route('master.locations')->with('success', 'Lokasi berhasil diupdate!');
    }
    public function locationsDestroy(Location $location) {
        $location->delete();
        return redirect()->route('master.locations')->with('success', 'Lokasi berhasil dihapus!');
    }

    // ── Groups ──────────────────────────────────
    public function groups() { return view('master.groups.index', ['items' => Group::latest()->paginate(20)]); }
    public function groupsCreate() { return view('master.groups.create'); }
    public function groupsStore(Request $r) {
        $r->validate(['name' => 'required|string|max:255']);
        Group::create($r->only('name'));
        return redirect()->route('master.groups')->with('success', 'Group berhasil ditambahkan!');
    }
    public function groupsEdit(Group $group) { return view('master.groups.edit', compact('group')); }
    public function groupsUpdate(Request $r, Group $group) {
        $r->validate(['name' => 'required|string|max:255']);
        $group->update($r->only('name'));
        return redirect()->route('master.groups')->with('success', 'Group berhasil diupdate!');
    }
    public function groupsDestroy(Group $group) {
        $group->delete();
        return redirect()->route('master.groups')->with('success', 'Group berhasil dihapus!');
    }

    // ── Spec tables (RAM, ROM, CPU, VGA) ────────
    private function specIndex(string $model, string $view) {
        $class = "App\\Models\\{$model}";
        return view($view, ['items' => $class::latest()->get()]);
    }
    private function specStore(Request $r, string $model, string $redirect) {
        $r->validate(['spec' => 'required|string|max:255']);
        $class = "App\\Models\\{$model}";
        $class::create($r->only('spec'));
        return redirect()->route($redirect)->with('success', 'Data berhasil ditambahkan!');
    }

    // RAM
    public function ramSpecs() { return view('master.specs.index', ['items' => RamSpec::latest()->paginate(9999), 'specType' => 'RAM']); }
    public function ramSpecsCreate() { return view('master.specs.create', ['specType' => 'RAM', 'storeRoute' => 'master.ram_specs.store']); }
    public function ramSpecsStore(Request $r) { return $this->specStore($r, 'RamSpec', 'master.ram_specs'); }
    public function ramSpecsEdit(RamSpec $ramSpec) { return view('master.specs.edit', ['item' => $ramSpec, 'specType' => 'RAM', 'updateRoute' => 'master.ram_specs.update']); }
    public function ramSpecsUpdate(Request $r, RamSpec $ramSpec) {
        $r->validate(['spec' => 'required|string|max:255']);
        $ramSpec->update($r->only('spec'));
        return redirect()->route('master.ram_specs')->with('success', 'RAM Spec berhasil diupdate!');
    }
    public function ramSpecsDestroy(RamSpec $ramSpec) { $ramSpec->delete(); return redirect()->route('master.ram_specs')->with('success', 'Data berhasil dihapus!'); }

    // ROM
    public function romSpecs() { return view('master.specs.index', ['items' => RomSpec::latest()->paginate(9999), 'specType' => 'ROM']); }
    public function romSpecsCreate() { return view('master.specs.create', ['specType' => 'ROM', 'storeRoute' => 'master.rom_specs.store']); }
    public function romSpecsStore(Request $r) { return $this->specStore($r, 'RomSpec', 'master.rom_specs'); }
    public function romSpecsEdit(RomSpec $romSpec) { return view('master.specs.edit', ['item' => $romSpec, 'specType' => 'ROM', 'updateRoute' => 'master.rom_specs.update']); }
    public function romSpecsUpdate(Request $r, RomSpec $romSpec) {
        $r->validate(['spec' => 'required|string|max:255']);
        $romSpec->update($r->only('spec'));
        return redirect()->route('master.rom_specs')->with('success', 'ROM Spec berhasil diupdate!');
    }
    public function romSpecsDestroy(RomSpec $romSpec) { $romSpec->delete(); return redirect()->route('master.rom_specs')->with('success', 'Data berhasil dihapus!'); }

    // CPU
    public function cpuSpecs() { return view('master.specs.index', ['items' => CpuSpec::latest()->paginate(9999), 'specType' => 'CPU']); }
    public function cpuSpecsCreate() { return view('master.specs.create', ['specType' => 'CPU', 'storeRoute' => 'master.cpu_specs.store']); }
    public function cpuSpecsStore(Request $r) { return $this->specStore($r, 'CpuSpec', 'master.cpu_specs'); }
    public function cpuSpecsEdit(CpuSpec $cpuSpec) { return view('master.specs.edit', ['item' => $cpuSpec, 'specType' => 'CPU', 'updateRoute' => 'master.cpu_specs.update']); }
    public function cpuSpecsUpdate(Request $r, CpuSpec $cpuSpec) {
        $r->validate(['spec' => 'required|string|max:255']);
        $cpuSpec->update($r->only('spec'));
        return redirect()->route('master.cpu_specs')->with('success', 'CPU Spec berhasil diupdate!');
    }
    public function cpuSpecsDestroy(CpuSpec $cpuSpec) { $cpuSpec->delete(); return redirect()->route('master.cpu_specs')->with('success', 'Data berhasil dihapus!'); }

    // VGA
    public function vgaSpecs() { return view('master.specs.index', ['items' => VgaSpec::latest()->paginate(9999), 'specType' => 'VGA']); }
    public function vgaSpecsCreate() { return view('master.specs.create', ['specType' => 'VGA', 'storeRoute' => 'master.vga_specs.store']); }
    public function vgaSpecsStore(Request $r) { return $this->specStore($r, 'VgaSpec', 'master.vga_specs'); }
    public function vgaSpecsEdit(VgaSpec $vgaSpec) { return view('master.specs.edit', ['item' => $vgaSpec, 'specType' => 'VGA', 'updateRoute' => 'master.vga_specs.update']); }
    public function vgaSpecsUpdate(Request $r, VgaSpec $vgaSpec) {
        $r->validate(['spec' => 'required|string|max:255']);
        $vgaSpec->update($r->only('spec'));
        return redirect()->route('master.vga_specs')->with('success', 'VGA Spec berhasil diupdate!');
    }
    public function vgaSpecsDestroy(VgaSpec $vgaSpec) { $vgaSpec->delete(); return redirect()->route('master.vga_specs')->with('success', 'Data berhasil dihapus!'); }

    // ── Employees ──────────────────────────────
    public function employees() {
        return view('master.employees.index', ['items' => \App\Models\Employee::with('group')->latest()->paginate(9999)]);
    }
    public function employeesCreate() {
        return view('master.employees.create', ['groups' => Group::orderBy('name')->get()]);
    }
    public function employeesStore(Request $r) {
        $r->validate(['name' => 'required|string|max:255', 'jabatan' => 'nullable|string|max:255', 'group_id' => 'nullable|exists:groups,id']);
        \App\Models\Employee::create($r->only('name', 'jabatan', 'group_id'));
        return redirect()->route('master.employees')->with('success', 'Employee berhasil ditambahkan!');
    }
    public function employeesEdit(\App\Models\Employee $employee) {
        return view('master.employees.edit', ['employee' => $employee, 'groups' => Group::orderBy('name')->get()]);
    }
    public function employeesUpdate(Request $r, \App\Models\Employee $employee) {
        $r->validate(['name' => 'required|string|max:255', 'jabatan' => 'nullable|string|max:255', 'group_id' => 'nullable|exists:groups,id']);
        $employee->update($r->only('name', 'jabatan', 'group_id'));
        return redirect()->route('master.employees')->with('success', 'Employee berhasil diupdate!');
    }
    public function employeesDestroy(\App\Models\Employee $employee) {
        $employee->delete();
        return redirect()->route('master.employees')->with('success', 'Employee berhasil dihapus!');
    }
}
