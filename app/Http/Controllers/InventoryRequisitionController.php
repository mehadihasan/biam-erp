<?php

namespace App\Http\Controllers;

use App\Filament\Pages\Inventory\Requisitions\ViewRequisition;
use App\Models\InventoryRequisition;

class InventoryRequisitionController extends Controller
{
    public function show(InventoryRequisition $requisition)
    {
        return redirect()->to(ViewRequisition::urlForRequisition($requisition->id));
    }
}
