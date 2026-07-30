<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('order.tracking_number')
                    ->label('Pedido')
                    ->searchable(),

                TextColumn::make('method')
                    ->label('Método')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'card' => 'Tarjeta',
                        'paypal' => 'PayPal',
                        default => $state,
                    }),

                TextColumn::make('status')
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

                TextColumn::make('transaction_id')
                    ->label('Transacción')
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->formatStateUsing(
                        fn ($state): string =>
                            '₡' . number_format((float) $state, 0, ',', '.')
                    ),

                TextColumn::make('paid_at')
                    ->label('Fecha de pago')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Pendiente'),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([

                ViewAction::make()
                    ->label('Ver'),

                EditAction::make()
                    ->label('Cambiar estado'),
            ]);
    }
}