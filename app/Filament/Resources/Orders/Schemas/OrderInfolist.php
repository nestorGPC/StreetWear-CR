<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextEntry::make('user.name')
                    ->label('Cliente'),

                TextEntry::make('user.email')
                    ->label('Correo electrónico'),

                TextEntry::make('tracking_number')
                    ->label('Número de seguimiento'),

                TextEntry::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'processing' => 'Preparando',
                        'shipped' => 'Enviado',
                        'delivered' => 'Entregado',
                        'cancelled' => 'Cancelado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextEntry::make('subtotal')
                    ->label('Subtotal')
                    ->formatStateUsing(
                        fn ($state): string =>
                            '₡' . number_format((float) $state, 0, ',', '.')
                    ),

                TextEntry::make('tax')
                    ->label('Impuestos')
                    ->formatStateUsing(
                        fn ($state): string =>
                            '₡' . number_format((float) $state, 0, ',', '.')
                    ),

                TextEntry::make('shipping')
                    ->label('Envío')
                    ->formatStateUsing(
                        fn ($state): string =>
                            '₡' . number_format((float) $state, 0, ',', '.')
                    ),

                TextEntry::make('total')
                    ->label('Total')
                    ->formatStateUsing(
                        fn ($state): string =>
                            '₡' . number_format((float) $state, 0, ',', '.')
                    ),

                TextEntry::make('shipping_address')
                    ->label('Dirección de envío')
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('Fecha del pedido')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}