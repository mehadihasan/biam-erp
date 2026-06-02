<?php

namespace App\Http\Controllers;

use App\Filament\Pages\Inventory\Items\AllItems;
use App\Filament\Pages\Inventory\Items\CreateItem;
use App\Filament\Pages\Inventory\Items\EditItem;
use App\Http\Requests\InventoryItemRequest;
use App\Models\InventoryItem;

class InventoryItemController extends Controller
{
    public function index()
    {
        return redirect()->to(AllItems::getUrl(panel: 'admin'));
    }

    public function create()
    {
        return redirect()->to(CreateItem::getUrl(panel: 'admin'));
    }

    public function store(InventoryItemRequest $request)
    {
        $data = $request->validated();
        $data['unit_cost'] ??= 0;
        $data['initial_stock_quantity'] ??= 0;
        $data['current_stock_quantity'] = $data['initial_stock_quantity'];
        $data['is_active'] = $request->boolean('is_active');

        InventoryItem::create($data);

        return redirect()
            ->to(AllItems::getUrl(panel: 'admin'))
            ->with('success', __('Item created successfully.'));
    }

    public function show(InventoryItem $item)
    {
        return redirect()->to(EditItem::urlForItem($item->id));
    }

    public function edit(InventoryItem $item)
    {
        return redirect()->to(EditItem::urlForItem($item->id));
    }

    public function update(InventoryItemRequest $request, InventoryItem $item)
    {
        $data = $request->validated();
        $data['unit_cost'] ??= 0;
        $data['initial_stock_quantity'] ??= 0;
        $data['is_active'] = $request->boolean('is_active');

        $item->update($data);

        return redirect()
            ->to(AllItems::getUrl(panel: 'admin'))
            ->with('success', __('Item updated successfully.'));
    }

    public function destroy(InventoryItem $item)
    {
        $item->delete();

        return redirect()
            ->to(AllItems::getUrl(panel: 'admin'))
            ->with('success', __('Item deleted successfully.'));
    }
}
