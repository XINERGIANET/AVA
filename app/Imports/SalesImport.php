<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\Isle;
use App\Models\Location;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Tank;
use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class SalesImport implements ToCollection, SkipsEmptyRows, WithCalculatedFormulas
{
    public array $errors = [];
    public int $importedCount = 0;

    /**
     * Procesar colección de filas del Excel.
     * La fila 1 es el encabezado (se salta).
     * Las filas están agrupadas por "Ref. venta" (col A).
     */
    public function collection(Collection $rows)
    {
        // Saltar fila de encabezado (fila 1)
        $dataRows = $rows->slice(1)->values();

        if ($dataRows->isEmpty()) {
            $this->errors[] = 'El archivo no contiene filas de datos.';
            return;
        }

        // Agrupar filas por Ref. venta (col 0 / A)
        // Si Ref está vacía, cada fila es una venta individual
        $groups = [];
        $autoRef = 1;

        foreach ($dataRows as $index => $row) {
            // Saltar filas completamente vacías
            $rowValues = array_filter($row->toArray(), fn($v) => $v !== null && $v !== '');
            if (empty($rowValues)) {
                continue;
            }

            $ref = trim((string)($row[0] ?? ''));
            if ($ref === '') {
                $ref = 'auto_' . $autoRef++;
            }

            $groups[$ref][] = ['row' => $index + 2, 'data' => $row]; // +2: fila 1 encabezado + base 1-indexed
        }

        if (empty($groups)) {
            $this->errors[] = 'No se encontraron filas con datos válidos.';
            return;
        }

        // Cachear catálogos para evitar N+1
        $locations   = Location::where('deleted', false)->get()->keyBy(fn($l) => strtolower(trim($l->name)));
        $products    = Product::all()->keyBy(fn($p) => strtolower(trim($p->name)));
        $payMethods  = PaymentMethod::where('deleted', false)->get()->keyBy(fn($pm) => strtolower(trim($pm->name)));
        $currentUser = Auth::user();

        foreach ($groups as $ref => $rowGroup) {
            DB::beginTransaction();
            try {
                $firstRow = $rowGroup[0]['data'];
                $rowNum   = $rowGroup[0]['row'];

                // --- FECHA ---
                $rawDate = $firstRow[1] ?? null;
                if ($rawDate === null || $rawDate === '') {
                    $this->errors[] = "Fila {$rowNum}: La fecha es obligatoria.";
                    DB::rollBack();
                    continue;
                }
                try {
                    // Puede llegar como número serial de Excel o string
                    if (is_numeric($rawDate)) {
                        $date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawDate));
                    } else {
                        $date = Carbon::parse($rawDate);
                    }
                } catch (\Throwable $e) {
                    $this->errors[] = "Fila {$rowNum}: Fecha inválida '{$rawDate}'.";
                    DB::rollBack();
                    continue;
                }

                // --- SEDE ---
                $sedeNombre = strtolower(trim((string)($firstRow[2] ?? '')));
                if ($sedeNombre === '') {
                    // Usar sede del usuario actual si no se especifica
                    $location = Location::find($currentUser->location_id);
                } else {
                    $location = $locations->get($sedeNombre);
                }
                if (!$location) {
                    $this->errors[] = "Fila {$rowNum}: Sede '{$firstRow[2]}' no encontrada.";
                    DB::rollBack();
                    continue;
                }

                // --- CLIENTE ---
                $clienteNombre = trim((string)($firstRow[3] ?? ''));
                $documento     = trim((string)($firstRow[4] ?? ''));
                $telefono      = trim((string)($firstRow[5] ?? ''));
                $clienteId     = null;

                if ($documento !== '') {
                    $cliente = Client::where('document', $documento)->first();
                    if (!$cliente && $clienteNombre !== '') {
                        $esRuc = strlen($documento) === 11;
                        $cliente = Client::create([
                            'document'      => $documento,
                            'contact_name'  => $esRuc ? null : $clienteNombre,
                            'business_name' => $esRuc ? $clienteNombre : null,
                            'phone'         => $telefono ?: null,
                            'deleted'       => 0,
                        ]);
                    }
                    if ($cliente) {
                        $clienteId = $cliente->id;
                    }
                } elseif ($clienteNombre !== '') {
                    // Buscar por nombre / razón social
                    $cliente = Client::where(function ($q) use ($clienteNombre) {
                        $q->whereRaw('LOWER(contact_name) = ?', [strtolower($clienteNombre)])
                          ->orWhereRaw('LOWER(business_name) = ?', [strtolower($clienteNombre)]);
                    })->first();
                    if ($cliente) {
                        $clienteId = $cliente->id;
                    }
                }

                // --- TIPO DE VENTA ---
                $tipoVentaRaw = $firstRow[6] ?? '';
                $typeSale = (int)($tipoVentaRaw === '' ? 0 : $tipoVentaRaw);
                if (!in_array($typeSale, [0, 2])) {
                    $typeSale = 0;
                }

                // --- PLACA / ADICIONAL ---
                $placa     = trim((string)($firstRow[7] ?? ''));
                $adicional = floatval($firstRow[8] ?? 0);

                // Obtener isla por defecto de la sede (primera disponible)
                $isle = Isle::where('location_id', $location->id)->where('deleted', false)->first();
                if (!$isle) {
                    $this->errors[] = "Fila {$rowNum}: La sede '{$location->name}' no tiene islas configuradas.";
                    DB::rollBack();
                    continue;
                }

                // --- PROCESAR LÍNEAS DE PRODUCTO ---
                $productLines = [];
                $total = 0;

                foreach ($rowGroup as $item) {
                    $row    = $item['data'];
                    $rowN   = $item['row'];

                    $productoNombre = strtolower(trim((string)($row[9] ?? '')));
                    if ($productoNombre === '') {
                        $this->errors[] = "Fila {$rowN}: El nombre del producto es obligatorio.";
                        throw new \Exception('producto_vacio');
                    }

                    $product = $products->get($productoNombre);
                    if (!$product) {
                        $this->errors[] = "Fila {$rowN}: Producto '{$row[9]}' no encontrado.";
                        throw new \Exception('producto_no_encontrado');
                    }

                    $cantidad         = floatval($row[10] ?? 1);
                    $precioUnitario   = floatval($row[11] ?? 0);
                    $precioDescuento  = floatval($row[12] ?? 0);
                    $subtotalLinea    = floatval($row[13] ?? 0);

                    // Si no vienen precios, estimar desde el producto/sede
                    if ($precioUnitario <= 0) {
                        $locPrice = \App\Models\LocationPrice::where('product_id', $product->id)
                            ->where('location_id', $location->id)
                            ->first();
                        $precioUnitario = $locPrice ? $locPrice->unit_price : $product->unit_price;
                    }

                    if ($subtotalLinea <= 0) {
                        $precioEfectivo = ($precioDescuento > 0) ? $precioDescuento : $precioUnitario;
                        $subtotalLinea  = round($cantidad * $precioEfectivo, 2);
                    }

                    $total += $subtotalLinea;

                    $productLines[] = [
                        'product'          => $product,
                        'cantidad'         => $cantidad,
                        'precioUnitario'   => $precioUnitario,
                        'precioDescuento'  => $precioDescuento > 0 ? $precioDescuento : null,
                        'subtotal'         => round($subtotalLinea, 2),
                        'placa'            => trim((string)($row[7] ?? $placa)),
                    ];
                }

                // --- CREAR SALE ---
                $sale = Sale::create([
                    'user_id'       => $currentUser->id,
                    'location_id'   => $location->id,
                    'isle_id'       => $isle->id,
                    'client_id'     => $clienteId,
                    'client_name'   => $clienteNombre ?: null,
                    'phone'         => $telefono ?: null,
                    'type_sale'     => $typeSale,
                    'total'         => round($total, 2),
                    'adicional'     => $adicional > 0 ? $adicional : 0,
                    'vehicle_plate' => $placa ?: null,
                    'date'          => $date,
                    'deleted'       => false,
                ]);

                // --- CREAR SALE DETAILS ---
                foreach ($productLines as $line) {
                    $truckId = null;
                    if (!empty($line['placa'])) {
                        $plate = strtoupper(trim($line['placa']));
                        $truck = Truck::whereRaw('UPPER(plate) = ?', [$plate])->where('deleted', 0)->first();
                        if (!$truck) {
                            $truck = Truck::create(['plate' => $plate, 'name' => $plate, 'deleted' => false]);
                        }
                        $truckId = $truck->id;
                    }

                    SaleDetail::create([
                        'sale_id'          => $sale->id,
                        'product_id'       => $line['product']->id,
                        'quantity'         => $line['cantidad'],
                        'unit_price'       => $line['precioUnitario'],
                        'discounted_price' => $line['precioDescuento'],
                        'subtotal'         => $line['subtotal'],
                        'truck_id'         => $truckId,
                        'deleted'          => false,
                    ]);
                }

                // --- PAGO ---
                $metodoNombre = strtolower(trim((string)($firstRow[14] ?? '')));
                $numCreditoOTicket = trim((string)($firstRow[15] ?? ''));

                $payMethod = $payMethods->get($metodoNombre);

                $numeroTicket = $this->generarNumeroTicket();

                if ($typeSale === 0) {
                    // Venta directa
                    if (!$payMethod) {
                        // Fallback: primer método disponible
                        $payMethod = PaymentMethod::where('deleted', false)->orderBy('id')->first();
                    }
                    Payment::create([
                        'sale_id'           => $sale->id,
                        'user_id'           => $currentUser->id,
                        'client_id'         => $clienteId,
                        'client_name'       => $clienteNombre ?: null,
                        'amount'            => round($total, 2),
                        'payment_method_id' => $payMethod ? $payMethod->id : null,
                        'voucher_type'      => 'Ticket',
                        'number'            => $numCreditoOTicket ?: $numeroTicket,
                        'status'            => 'paid',
                        'date'              => $date,
                        'deleted'           => false,
                    ]);

                    // Si es efectivo, actualizar caja de la isla
                    if ($payMethod && strtolower(trim($payMethod->name)) === 'efectivo') {
                        DB::table('isles')->where('id', $isle->id)->increment('cash_amount', round($total, 2));
                    }
                } elseif ($typeSale === 2) {
                    // Venta a crédito
                    Payment::create([
                        'sale_id'           => $sale->id,
                        'user_id'           => $currentUser->id,
                        'client_id'         => $clienteId,
                        'client_name'       => $clienteNombre ?: null,
                        'amount'            => round($total, 2),
                        'payment_method_id' => $this->resolveCreditPendingPaymentMethodId(),
                        'voucher_type'      => 'Ticket',
                        'number'            => $numCreditoOTicket ?: $numeroTicket,
                        'status'            => 'pending',
                        'date'              => $date,
                        'deleted'           => false,
                    ]);
                }

                DB::commit();
                $this->importedCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                if (!in_array($e->getMessage(), ['producto_vacio', 'producto_no_encontrado'])) {
                    $rowNum = $rowGroup[0]['row'] ?? '?';
                    $this->errors[] = "Ref '{$ref}' (fila {$rowNum}): " . $e->getMessage();
                    Log::error("SalesImport error ref {$ref}: " . $e->getMessage());
                }
            }
        }
    }

    private function generarNumeroTicket(): string
    {
        return DB::transaction(function () {
            $registro = DB::table('config')->lockForUpdate()->first();
            if (!$registro) {
                DB::table('config')->insert(['number' => 1]);
                return 'TICKET-00000001';
            }
            $nuevoNumero = $registro->number + 1;
            DB::table('config')->update(['number' => $nuevoNumero]);
            return 'TICKET-' . str_pad($nuevoNumero, 8, '0', STR_PAD_LEFT);
        });
    }

    private function resolveCreditPendingPaymentMethodId(): int
    {
        $creditLikeNames = ['credito', 'crédito', 'pendiente', 'pending'];
        $method = PaymentMethod::query()
            ->where('deleted', 0)
            ->where(function ($q) use ($creditLikeNames) {
                foreach ($creditLikeNames as $name) {
                    $q->orWhereRaw('LOWER(name) = ?', [mb_strtolower($name)]);
                }
            })
            ->orderBy('id')
            ->first();

        if ($method) return (int)$method->id;

        $fallback = PaymentMethod::where('deleted', 0)->orderBy('id')->first();
        if ($fallback) return (int)$fallback->id;

        throw new \Exception('No hay método de pago disponible para crédito.');
    }
}
