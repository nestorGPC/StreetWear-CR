<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Models\Payment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('order_id')
                    ->label('Pedido')
                    ->relationship('order', 'tracking_number')
                    ->disabled(),

                TextInput::make('method')
                    ->label('Método de pago')
                    ->disabled(),

                Select::make('status')
                    ->label('Estado')
                    ->options([
                        Payment::STATUS_PENDING => 'Pendiente',
                        Payment::STATUS_PAID => 'Pagado',
                        Payment::STATUS_FAILED => 'Fallido',
                        Payment::STATUS_REFUNDED => 'Reembolsado',
                    ])
                    ->required()
                    ->native(false),

                TextInput::make('transaction_id')
                    ->label('ID de transacción')
                    ->disabled(),

                TextInput::make('amount')
                    ->label('Monto')
                    ->prefix('₡')
                    ->disabled(),

                TextInput::make('paid_at')
                    ->label('Fecha de pago')
                    ->disabled(),
            ]);
    }
}