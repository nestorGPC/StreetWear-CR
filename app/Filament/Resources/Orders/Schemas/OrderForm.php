<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('user_id')
                    ->label('Cliente')
                    ->relationship('user', 'name')
                    ->disabled(),

                TextInput::make('tracking_number')
                    ->label('Número de seguimiento')
                    ->disabled(),

                Select::make('status')
                    ->label('Estado')
                    ->options([
                        Order::STATUS_PENDING => 'Pendiente',
                        Order::STATUS_PROCESSING => 'Preparando',
                        Order::STATUS_SHIPPED => 'Enviado',
                        Order::STATUS_DELIVERED => 'Entregado',
                        Order::STATUS_CANCELLED => 'Cancelado',
                    ])
                    ->required()
                    ->native(false),

                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->prefix('₡')
                    ->disabled(),

                TextInput::make('tax')
                    ->label('Impuestos')
                    ->prefix('₡')
                    ->disabled(),

                TextInput::make('shipping')
                    ->label('Envío')
                    ->prefix('₡')
                    ->disabled(),

                TextInput::make('total')
                    ->label('Total')
                    ->prefix('₡')
                    ->disabled(),

                Textarea::make('shipping_address')
                    ->label('Dirección de envío')
                    ->rows(4)
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }
}