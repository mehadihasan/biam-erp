<?php

namespace App\Http\Controllers;

use App\Filament\Pages\Inventory\Settings\CreateSupplier;
use App\Filament\Pages\Inventory\Settings\EditSupplier;
use App\Filament\Pages\Inventory\Settings\Suppliers;
use App\Http\Requests\InventorySupplierRequest;
use App\Models\InventorySupplier;

class InventorySupplierController extends Controller
{
    public function index()
    {
        return redirect()->to(Suppliers::getUrl(panel: 'admin'));
    }

    public function create()
    {
        return redirect()->to(CreateSupplier::getUrl(panel: 'admin'));
    }

    public function store(InventorySupplierRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        InventorySupplier::create($data);

        return redirect()
            ->to(Suppliers::getUrl(panel: 'admin'))
            ->with('success', __('Supplier created successfully.'));
    }

    public function edit(InventorySupplier $supplier)
    {
        return redirect()->to(EditSupplier::urlForSupplier($supplier->id));
    }

    public function update(InventorySupplierRequest $request, InventorySupplier $supplier)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $supplier->update($data);

        return redirect()
            ->to(Suppliers::getUrl(panel: 'admin'))
            ->with('success', __('Supplier updated successfully.'));
    }

    public function destroy(InventorySupplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->to(Suppliers::getUrl(panel: 'admin'))
            ->with('success', __('Supplier deleted successfully.'));
    }
}
