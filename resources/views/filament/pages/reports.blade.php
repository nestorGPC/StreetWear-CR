<x-filament-panels::page>

    <div class="space-y-8">

        {{-- Encabezado --}}

        <div>
            <h2 class="fi-header-heading text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                Generar reportes
            </h2>

            <p class="fi-header-subheading mt-1 text-sm text-gray-500 dark:text-gray-400">
                Seleccione los filtros y genere el reporte que necesita.
            </p>
        </div>


        <form method="GET">

            {{-- Filtros --}}

            <x-filament::section icon="heroicon-o-funnel">

                <x-slot name="heading">
                    Filtros
                </x-slot>


                <div class="mt-5 grid grid-cols-1 gap-6 md:grid-cols-3">


                    {{-- Fecha inicial --}}

                    <div>

                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Fecha inicial
                        </label>

                        <x-filament::input.wrapper>

                            <x-filament::input
                                type="date"
                                name="desde"
                            />

                        </x-filament::input.wrapper>

                    </div>


                    {{-- Fecha final --}}

                    <div>

                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Fecha final
                        </label>

                        <x-filament::input.wrapper>

                            <x-filament::input
                                type="date"
                                name="hasta"
                            />

                        </x-filament::input.wrapper>

                    </div>


                    {{-- Estado --}}

                    <div>

                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Estado
                        </label>

                        <x-filament::input.wrapper>

                            <x-filament::input.select name="estado">

                                <option value="">
                                    Todos los estados
                                </option>

                                @foreach ($this->getEstados() as $valor => $etiqueta)

                                    <option value="{{ $valor }}">
                                        {{ $etiqueta }}
                                    </option>

                                @endforeach

                            </x-filament::input.select>

                        </x-filament::input.wrapper>

                    </div>


                    {{-- Cliente --}}

                    <div>

                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cliente
                        </label>

                        <x-filament::input.wrapper>

                            <x-filament::input.select name="cliente">

                                <option value="">
                                    Todos los clientes
                                </option>

                                @foreach ($this->getClientes() as $cliente)

                                    <option value="{{ $cliente->id }}">
                                        {{ $cliente->name }}
                                    </option>

                                @endforeach

                            </x-filament::input.select>

                        </x-filament::input.wrapper>

                    </div>


                    {{-- Pedido --}}

                    <div>

                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Pedido
                        </label>

                        <x-filament::input.wrapper>

                            <x-filament::input.select name="pedido">

                                <option value="">
                                    Todos los pedidos
                                </option>

                                @foreach ($this->getPedidos() as $pedido)

                                    <option value="{{ $pedido->id }}">
                                        {{ $pedido->tracking_number }}
                                    </option>

                                @endforeach

                            </x-filament::input.select>

                        </x-filament::input.wrapper>

                    </div>


                    {{-- Producto --}}

                    <div>

                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Producto
                        </label>

                        <x-filament::input.wrapper>

                            <x-filament::input.select name="producto">

                                <option value="">
                                    Todos los productos
                                </option>

                                @foreach ($this->getProductos() as $producto)

                                    <option value="{{ $producto->id }}">
                                        {{ $producto->name }}
                                    </option>

                                @endforeach

                            </x-filament::input.select>

                        </x-filament::input.wrapper>

                    </div>

                </div>

            </x-filament::section>


            {{-- Reportes --}}

            <div class="mt-10">

                <h3 class="text-lg font-semibold text-gray-950 dark:text-white">
                    Reportes
                </h3>

            </div>


            <div class="mt-5 grid grid-cols-1 gap-6 md:grid-cols-3">


                {{-- Reporte de pedidos --}}

                <x-filament::section icon="heroicon-o-clipboard-document-list">

                    <x-slot name="heading">
                        Reporte de pedidos
                    </x-slot>

                    <x-slot name="description">
                        Pedidos registrados en el sistema.
                    </x-slot>

                    <div class="mt-5">

                        <x-filament::button
                            type="submit"
                            formaction="{{ route('reports.orders') }}"
                            icon="heroicon-o-arrow-down-tray"
                            color="primary"
                            class="w-full"
                        >
                            Descargar reporte
                        </x-filament::button>

                    </div>

                </x-filament::section>


                {{-- Reporte de ventas --}}

                <x-filament::section icon="heroicon-o-banknotes">

                    <x-slot name="heading">
                        Reporte de ventas
                    </x-slot>

                    <x-slot name="description">
                        Resumen de ventas realizadas.
                    </x-slot>

                    <div class="mt-5">

                        <x-filament::button
                            type="submit"
                            formaction="{{ route('reports.sales') }}"
                            icon="heroicon-o-arrow-down-tray"
                            color="primary"
                            class="w-full"
                        >
                            Descargar reporte
                        </x-filament::button>

                    </div>

                </x-filament::section>


                {{-- Reporte de productos --}}

                <x-filament::section icon="heroicon-o-cube">

                    <x-slot name="heading">
                        Reporte de productos
                    </x-slot>

                    <x-slot name="description">
                        Productos vendidos y sus resultados.
                    </x-slot>

                    <div class="mt-5">

                        <x-filament::button
                            type="submit"
                            formaction="{{ route('reports.products') }}"
                            icon="heroicon-o-arrow-down-tray"
                            color="primary"
                            class="w-full"
                        >
                            Descargar reporte
                        </x-filament::button>

                    </div>

                </x-filament::section>


            </div>

        </form>

    </div>

</x-filament-panels::page>
