<?php

namespace App\Http\Controllers;

use App\Filament\Pages\Inventory\Requisitions\AllRequisitions;
use App\Filament\Pages\Inventory\Requisitions\EditRequisition;
use App\Filament\Pages\Inventory\Requisitions\ViewRequisition;
use App\Models\InventoryRequisition;

class InventoryRequisitionController extends Controller
{
    private const FINALIZED_MESSAGE = 'This requisition has already been finalized and cannot be modified.';

    public function show(InventoryRequisition $requisition)
    {
        return redirect()->to(ViewRequisition::urlForRequisition($requisition->id));
    }

    public function edit(InventoryRequisition $requisition)
    {
        if ($requisition->status !== InventoryRequisition::STATUS_PENDING) {
            return redirect()
                ->to(AllRequisitions::getUrl(panel: 'admin'))
                ->with('error', __(self::FINALIZED_MESSAGE));
        }

        return redirect()->to(EditRequisition::urlForRequisition($requisition->id));
    }
}
