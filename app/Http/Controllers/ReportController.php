<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureIsAdmin();

        $clientes = User::whereHas('orders')
            ->orderBy('name')
            ->get();

        $estados = [
            'pending' => 'Pendiente',
            'processing' => 'En preparación',
            'shipped' => 'Enviado',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
        ];

        return view('reports.index', compact('clientes', 'estados'));
    }

    public function orders(Request $request)
    {
        $this->ensureIsAdmin();

        $orders = $this->ordenesFiltradas($request)
            ->with('user')
            ->orderBy('created_at')
            ->get();

        $pdf = Pdf::loadView('reports.orders', [
            'orders' => $orders,
            'desde' => $request->desde,
            'hasta' => $request->hasta,
            'estado' => $request->estado,
        ]);

        return $pdf->download('reporte-pedidos.pdf');
    }

    public function sales(Request $request)
    {
        $this->ensureIsAdmin();

        $orders = $this->ordenesFiltradas($request)
            ->with('user')
            ->orderBy('created_at')
            ->get();

        $totalPedidos = $orders->count();
        $totalVendido = $orders->sum('total');

        $ventasPorMes = $orders
            ->groupBy(fn ($order) => $order->created_at->format('Y-m'))
            ->map(fn ($ordersDelMes) => [
                'cantidad_pedidos' => $ordersDelMes->count(),
                'total_vendido' => $ordersDelMes->sum('total'),
            ])
            ->sortKeys();

        $ventasPorCliente = $orders
            ->groupBy(fn ($order) => $order->user->name ?? 'Cliente eliminado')
            ->map(fn ($ordersDelCliente) => [
                'cantidad_pedidos' => $ordersDelCliente->count(),
                'total_vendido' => $ordersDelCliente->sum('total'),
            ])
            ->sortByDesc('total_vendido');

        $pdf = Pdf::loadView('reports.sales', [
            'totalPedidos' => $totalPedidos,
            'totalVendido' => $totalVendido,
            'ventasPorMes' => $ventasPorMes,
            'ventasPorCliente' => $ventasPorCliente,
            'desde' => $request->desde,
            'hasta' => $request->hasta,
            'estado' => $request->estado,
        ]);

        return $pdf->download('reporte-ventas.pdf');
    }

    public function products(Request $request)
    {
        $this->ensureIsAdmin();

        $items = OrderItem::whereHas('order', function ($query) use ($request) {
            $this->aplicarFiltros($query, $request);
        })->get();

        $productos = $items
            ->groupBy('product_name')
            ->map(function ($itemsDelProducto) {
                $cantidad = $itemsDelProducto->sum('quantity');
                $totalGenerado = $itemsDelProducto->sum('subtotal');

                return [
                    'cantidad_vendida' => $cantidad,
                    'precio' => $cantidad > 0 ? $totalGenerado / $cantidad : 0,
                    'total_generado' => $totalGenerado,
                ];
            })
            ->sortByDesc('total_generado');

        $pdf = Pdf::loadView('reports.products', [
            'productos' => $productos,
            'desde' => $request->desde,
            'hasta' => $request->hasta,
            'estado' => $request->estado,
        ]);

        return $pdf->download('reporte-productos.pdf');
    }

    private function ordenesFiltradas(Request $request)
    {
        return $this->aplicarFiltros(Order::query(), $request);
    }

    private function aplicarFiltros($query, Request $request)
    {
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        if ($request->filled('estado')) {
            $query->where('status', $request->estado);
        }

        if ($request->filled('cliente')) {
            $query->where('user_id', $request->cliente);
        }

        return $query;
    }

    private function ensureIsAdmin(): void
    {
        abort_unless(
            auth()->user()?->hasRole('super_admin'),
            403
        );
    }
}
