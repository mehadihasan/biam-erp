<?php

namespace App\Http\Controllers;

use App\Filament\Pages\Inventory\Settings\CreateUnit;
use App\Filament\Pages\Inventory\Settings\EditUnit;
use App\Filament\Pages\Inventory\Settings\Units;
use App\Http\Requests\InventoryUnitRequest;
use App\Models\InventoryUnit;

class InventoryUnitController extends Controller
{
    public function index()
    {
        return redirect()->to(Units::getUrl(panel: 'admin'));
    }

    public function create()
    {
        return redirect()->to(CreateUnit::getUrl(panel: 'admin'));
    }

    public function store(InventoryUnitRequest $request)
    {
        InventoryUnit::create($request->validated());

        return redirect()
            ->to(Units::getUrl(panel: 'admin'))
            ->with('success', __('Unit created successfully.'));
    }

    public function edit(InventoryUnit $unit)
    {
        return redirect()->to(EditUnit::urlForUnit($unit->id));
    }

    public function update(InventoryUnitRequest $request, InventoryUnit $unit)
    {
        $unit->update($request->validated());

        return redirect()
            ->to(Units::getUrl(panel: 'admin'))
            ->with('success', __('Unit updated successfully.'));
    }

    public function destroy(InventoryUnit $unit)
    {
        $unit->delete();

        return redirect()
            ->to(Units::getUrl(panel: 'admin'))
            ->with('success', __('Unit deleted successfully.'));
    }
}
