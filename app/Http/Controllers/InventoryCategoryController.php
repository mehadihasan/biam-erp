<?php

namespace App\Http\Controllers;

use App\Filament\Pages\Inventory\Settings\Categories;
use App\Filament\Pages\Inventory\Settings\CreateCategory;
use App\Filament\Pages\Inventory\Settings\EditCategory;
use App\Http\Requests\InventoryCategoryRequest;
use App\Models\InventoryCategory;

class InventoryCategoryController extends Controller
{
    public function index()
    {
        return redirect()->to(Categories::getUrl(panel: 'admin'));
    }

    public function create()
    {
        return redirect()->to(CreateCategory::getUrl(panel: 'admin'));
    }

    public function store(InventoryCategoryRequest $request)
    {
        InventoryCategory::create($request->validated());

        return redirect()
            ->to(Categories::getUrl(panel: 'admin'))
            ->with('success', __('Category created successfully.'));
    }

    public function edit(InventoryCategory $category)
    {
        return redirect()->to(EditCategory::urlForCategory($category->id));
    }

    public function update(InventoryCategoryRequest $request, InventoryCategory $category)
    {
        $category->update($request->validated());

        return redirect()
            ->to(Categories::getUrl(panel: 'admin'))
            ->with('success', __('Category updated successfully.'));
    }

    public function destroy(InventoryCategory $category)
    {
        $category->delete();

        return redirect()
            ->to(Categories::getUrl(panel: 'admin'))
            ->with('success', __('Category deleted successfully.'));
    }
}
