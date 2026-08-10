<x-filament-panels::page>

    <div class="space-y-6">

        <div>
            <h2 class="text-xl font-semibold text-gray-950 dark:text-white">
                Generar reportes
            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Seleccione el reporte que desea generar y descargar en formato PDF.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">

                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                        <x-filament::icon
                            icon="heroicon-o-clipboard-document-list"
                            class="h-5 w-5 text-gray-600 dark:text-gray-300"
                        />
                    </div>

                    <div>
                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                            Reporte de pedidos
                        </h3>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Pedidos registrados en el sistema.
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <x-filament::button
                        tag="a"
                        href="{{ route('reports.orders') }}"
                        icon="heroicon-o-arrow-down-tray"
                        color="gray"
                    >
                        Descargar reporte
                    </x-filament::button>
                </div>

            </div>


            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">

                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                        <x-filament::icon
                            icon="heroicon-o-banknotes"
                            class="h-5 w-5 text-gray-600 dark:text-gray-300"
                        />
                    </div>

                    <div>
                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                            Reporte de ventas
                        </h3>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Resumen de ventas realizadas.
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <x-filament::button
                        tag="a"
                        href="{{ route('reports.sales') }}"
                        icon="heroicon-o-arrow-down-tray"
                        color="gray"
                    >
                        Descargar reporte
                    </x-filament::button>
                </div>

            </div>


            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">

                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                        <x-filament::icon
                            icon="heroicon-o-cube"
                            class="h-5 w-5 text-gray-600 dark:text-gray-300"
                        />
                    </div>

                    <div>
                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                            Reporte de productos
                        </h3>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Productos vendidos y sus resultados.
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <x-filament::button
                        tag="a"
                        href="{{ route('reports.products') }}"
                        icon="heroicon-o-arrow-down-tray"
                        color="gray"
                    >
                        Descargar reporte
                    </x-filament::button>
                </div>

            </div>

        </div>

    </div>

</x-filament-panels::page>
