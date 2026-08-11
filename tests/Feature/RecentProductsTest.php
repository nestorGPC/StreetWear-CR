<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Cookie;
use Tests\TestCase;

class RecentProductsTest extends TestCase
{
    use RefreshDatabase;


    private function crearProducto(string $nombre): Product
    {
        $category = Category::create([
            'name' => 'Ropa',
            'description' => 'Ropa urbana',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => $nombre,
            'description' => 'Producto de prueba',
            'price' => 15000,
            'stock' => 5,
            'active' => true,
        ]);
    }


    private function cookieDeRespuesta(\Illuminate\Testing\TestResponse $response): Cookie
    {
        $cookie = collect($response->headers->getCookies())->first(
            fn ($c) => $c->getName() === 'recent_products'
        );

        $this->assertNotNull($cookie);

        return $cookie;
    }


    private function idsDeCookie(Cookie $cookie): array
    {
        $descifrado = Crypt::decrypt($cookie->getValue(), false);

        $valor = CookieValuePrefix::remove($descifrado, $cookie->getName());

        $ids = json_decode($valor, true);

        return is_array($ids) ? $ids : [];
    }


    public function test_ver_un_producto_guarda_su_id_en_la_cookie(): void
    {
        $product = $this->crearProducto('Camiseta Oversize Negra');

        $response = $this->get('/productos/' . $product->id);

        $response->assertStatus(200);

        $this->assertContains(
            $product->id,
            $this->idsDeCookie($this->cookieDeRespuesta($response))
        );
    }


    public function test_ver_dos_productos_acumula_ambos_ids_en_la_cookie(): void
    {
        $camiseta = $this->crearProducto('Camiseta Oversize Negra');
        $gorra = $this->crearProducto('Gorra Snapback Negra');

        $primera = $this->get('/productos/' . $camiseta->id);

        $idsIniciales = $this->idsDeCookie(
            $this->cookieDeRespuesta($primera)
        );

        $segunda = $this->withCookie(
            'recent_products',
            json_encode($idsIniciales)
        )->get('/productos/' . $gorra->id);

        $this->assertEquals(
            [$gorra->id, $camiseta->id],
            $this->idsDeCookie($this->cookieDeRespuesta($segunda))
        );
    }


    public function test_la_cookie_de_recientes_no_almacena_mas_de_5(): void
    {
        $productos = [];

        for ($i = 0; $i < 7; $i++) {
            $productos[] = $this->crearProducto('Producto ' . $i);
        }

        $idsAcumulados = [];

        foreach ($productos as $product) {
            $response = $this->withCookie(
                'recent_products',
                json_encode($idsAcumulados)
            )->get('/productos/' . $product->id);

            $idsAcumulados = $this->idsDeCookie(
                $this->cookieDeRespuesta($response)
            );
        }

        $this->assertCount(5, $idsAcumulados);
    }
}
