<?php

namespace App\Http\Controllers;

use App\Filament\Pages\Hostel\Users\Designations;
use App\Filament\Pages\Hostel\Users\CreateDesignation;
use App\Filament\Pages\Hostel\Users\EditDesignation;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DesignationController extends Controller
{
    public function index()
    {
        return redirect()->to(Designations::getUrl(panel: 'admin'));
    }

    public function create()
    {
        return redirect()->to(CreateDesignation::getUrl(panel: 'admin'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:designations,name'],
            'description' => ['nullable', 'string'],
        ]);

        Designation::create($validated);

        return redirect()
            ->to(Designations::getUrl(panel: 'admin'))
            ->with('success', __('Designation created successfully.'));
    }

    public function edit(Designation $designation)
    {
        return redirect()->to(EditDesignation::getUrl(['id' => $designation->id], panel: 'admin'));
    }

    public function show(Designation $designation)
    {
        return redirect()->to(EditDesignation::getUrl(['id' => $designation->id], panel: 'admin'));
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('designations', 'name')->ignore($designation->id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $designation->update($validated);

        return redirect()
            ->to(Designations::getUrl(panel: 'admin'))
            ->with('success', __('Designation updated successfully.'));
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();

        return redirect()
            ->to(Designations::getUrl(panel: 'admin'))
            ->with('success', __('Designation deleted successfully.'));
    }
}
