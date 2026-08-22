<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Reports extends Page
{
    protected string $view = 'filament.pages.reports';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedDocumentChartBar;

    protected static ?string $navigationLabel = 'Reportes';

    protected static ?string $title = 'Reportes';

    protected static ?int $navigationSort = 6;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public function getTotalPedidos(): int
    {
        return Order::count();
    }

    public function getTotalVendido(): float
    {
        return (float) Order::sum('total');
    }

    public function getProductoMasVendido(): string
    {
        $item = OrderItem::query()
            ->selectRaw('product_name, SUM(quantity) as cantidad')
            ->groupBy('product_name')
            ->orderByDesc('cantidad')
            ->first();

        return $item->product_name ?? 'Sin ventas todavía';
    }

    public function getClientes()
    {
        return User::whereHas('orders')
            ->orderBy('name')
            ->get();
    }

    public function getPedidos()
    {
        return Order::orderByDesc('created_at')->get();
    }

    public function getProductos()
    {
        return \App\Models\Product::orderBy('name')->get();
    }

    public function getEstados(): array
    {
        return [
            'pending' => 'Pendiente',
            'processing' => 'En preparación',
            'shipped' => 'Enviado',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
        ];
    }
}
