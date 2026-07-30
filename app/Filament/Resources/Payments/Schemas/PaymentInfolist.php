<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextEntry::make('order.tracking_number')
                    ->label('Número de pedido'),

                TextEntry::make('order.user.name')
                    ->label('Cliente'),

                TextEntry::make('method')
                    ->label('Método de pago')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'card' => 'Tarjeta',
                        'paypal' => 'PayPal',
                        default => $state,
                    }),

                TextEntry::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'failed' => 'Fallido',
                        'refunded' => 'Reembolsado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    }),

                TextEntry::make('transaction_id')
                    ->label('ID de transacción')
                    ->placeholder('Sin transacción'),

                TextEntry::make('amount')
                    ->label('Monto')
                    ->formatStateUsing(
                        fn ($state): string =>
                            '₡' . number_format((float) $state, 0, ',', '.')
                    ),

                TextEntry::make('paid_at')
                    ->label('Fecha de pago')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Pago pendiente'),

                TextEntry::make('created_at')
                    ->label('Fecha de registro')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}