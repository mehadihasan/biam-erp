<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class InventoryDashboard extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cube';

    protected static string | \UnitEnum | null $navigationGroup = 'Inventory Management';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Inventory Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $slug = 'inventory/dashboard';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.inventory-dashboard';

    public function getViewData(): array
    {
        return [
            'kpis' => [
                ['label' => 'Total Items', 'value' => 450, 'sub' => 'across 18 categories', 'icon' => 'heroicon-o-cube', 'colorClass' => 'text-teal-700', 'bgClass' => 'bg-teal-100 dark:bg-teal-500/15'],
                ['label' => 'Total Stock Value', 'value' => 'BDT 2,450,000', 'sub' => 'current inventory value', 'icon' => 'heroicon-o-banknotes', 'colorClass' => 'text-emerald-700', 'bgClass' => 'bg-emerald-100 dark:bg-emerald-500/15'],
                ['label' => 'Low Stock Alerts', 'value' => 12, 'sub' => 'items need attention', 'icon' => 'heroicon-o-exclamation-triangle', 'colorClass' => 'text-orange-700', 'bgClass' => 'bg-orange-100 dark:bg-orange-500/15'],
                ['label' => 'Total Suppliers', 'value' => 34, 'sub' => 'active suppliers', 'icon' => 'heroicon-o-building-office', 'colorClass' => 'text-cyan-700', 'bgClass' => 'bg-cyan-100 dark:bg-cyan-500/15'],
            ],
            'alerts' => [
                ['name' => 'Printer Paper A4', 'current' => 5, 'min' => 20],
                ['name' => 'USB Mouse', 'current' => 3, 'min' => 10],
            ],
            'lowStockItems' => [
                ['code' => 'ITM-1001', 'name' => 'Printer Paper A4', 'qty' => 5, 'min' => 20],
                ['code' => 'ITM-1020', 'name' => 'USB Mouse', 'qty' => 3, 'min' => 10],
                ['code' => 'ITM-1099', 'name' => 'Marker Pen', 'qty' => 8, 'min' => 15],
            ],
            'recentTransactions' => [
                ['ref' => 'TXN-9011', 'item' => 'Paper A4', 'type' => 'stock_out', 'qty' => 12],
                ['ref' => 'TXN-9010', 'item' => 'Projector Cable', 'type' => 'stock_in', 'qty' => 30],
                ['ref' => 'TXN-9009', 'item' => 'Marker Pen', 'type' => 'adjustment', 'qty' => 4],
            ],
            'categoryData' => [
                ['name' => 'Stationery', 'qty' => 120],
                ['name' => 'Electronics', 'qty' => 80],
                ['name' => 'Furniture', 'qty' => 45],
                ['name' => 'Cleaning', 'qty' => 100],
            ],
        ];
    }
}
