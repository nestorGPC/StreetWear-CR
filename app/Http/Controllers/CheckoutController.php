<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Services\CartCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RuntimeException;


class CheckoutController extends Controller
{
    public function __construct(
        private CartCalculator $cartCalculator
    ) {
    }

    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $subtotal = $this->cartCalculator->subtotal($cart);
        $tax = $this->cartCalculator->tax($subtotal);
        $shipping = $this->cartCalculator->shipping($subtotal);
        $total = $this->cartCalculator->total($subtotal);

        $checkoutToken = Str::random(32);
        session()->put('checkout_token', $checkoutToken);

        return view('checkout.index', compact(
            'cart',
            'subtotal',
            'tax',
            'shipping',
            'total',
            'checkoutToken'
        ));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $data = $request->validate([
            'shipping_address' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],

            'payment_method' => [
                'required',
                'in:card,paypal',
            ],

            'checkout_token' => [
                'required',
                'string',
            ],
        ]);

        /*
         * Token de idempotencia: evita que un doble POST
         * (doble clic o reenvío del formulario) cree dos pedidos.
         */
        $sessionToken = session()->pull('checkout_token');

        if (
            $sessionToken === null
            || ! hash_equals($sessionToken, $data['checkout_token'])
        ) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'La sesión de compra expiró. Vuelve a intentarlo.');
        }

        try {
            $order = DB::transaction(function () use (
                $request,
                $data,
                $cart
            ) {
                /*
                 * Volvemos a comprobar cada producto usando la base de datos.
                 * No confiamos únicamente en los valores almacenados en sesión.
                 */
                $subtotal = 0;

                foreach ($cart as $item) {
                    $product = Product::query()
                        ->lockForUpdate()
                        ->find($item['id']);

                    if (! $product || ! $product->active) {
                        throw new RuntimeException(
                            'Uno de los productos ya no está disponible.'
                        );
                    }

                    if ($product->stock < $item['quantity']) {
                        throw new RuntimeException(
                            "No hay suficiente inventario de {$product->name}."
                        );
                    }

                    $subtotal += (float) $product->price * $item['quantity'];
                }

                $tax = $this->cartCalculator->tax($subtotal);
                $shipping = $this->cartCalculator->shipping($subtotal);
                $total = $this->cartCalculator->total($subtotal);

                $trackingNumber = $this->generateTrackingNumber();

                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'tracking_number' => $trackingNumber,
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'total' => $total,
                    'shipping_address' => $data['shipping_address'],
                ]);

                foreach ($cart as $item) {
                    $product = Product::findOrFail($item['id']);

                    $itemSubtotal =
                        (float) $product->price * $item['quantity'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $itemSubtotal,
                    ]);

                    $product->decrement(
                        'stock',
                        $item['quantity']
                    );
                }

                /*
                 * Pago local de demostración.
                 * Más adelante esta parte se sustituye por una
                 * respuesta real de Stripe/PayPal sandbox.
                 */
                Payment::create([
                    'order_id' => $order->id,
                    'method' => $data['payment_method'],
                    'status' => 'pending',
                    'transaction_id' => null,
                    'amount' => $total,
                    'paid_at' => null,
                ]);

                return $order;
            });

        } catch (RuntimeException $exception) {

            return redirect()
                ->route('cart.index')
                ->with('error', $exception->getMessage());
        }

        session()->forget('cart');

        return redirect()
            ->route('checkout.success', $order);
    }

    public function success(Order $order)
{
        $userId = Auth::id();

        if ($userId === null || (int) $order->user_id !== (int) $userId) {
            abort(403);
        }

        $order->load([
            'items',
            'payment',
        ]);

        return view(
            'checkout.success',
            compact('order')
        );
    }

    private function generateTrackingNumber(): string
    {
        do {
            $trackingNumber =
                'SWCR-' .
                now()->format('Ymd') .
                '-' .
                Str::upper(Str::random(6));

        } while (
            Order::where(
                'tracking_number',
                $trackingNumber
            )->exists()
        );

        return $trackingNumber;
    }
}