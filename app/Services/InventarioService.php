<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\Inventario;
use App\Models\InventarioDetalle;
use App\Models\ProgramaEstudio;
use App\Models\Sala;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventarioService
{

    public function crear(
        ProgramaEstudio $programa,
        Sala $sala,
        string $serie,
        ?string $fechaEntrega = null,
        ?string $observacion = null,
        bool $copiarBienes = true,
    ): Inventario {
        if ($sala->programa_estudio_id !== $programa->id) {
            throw ValidationException::withMessages([
                'sala_id' => 'La sala no pertenece a este programa de estudio.',
            ]);
        }

        return DB::transaction(function () use ($programa, $sala, $serie, $fechaEntrega, $observacion, $copiarBienes) {
            $programa->load(['coordinador', 'asistenteT1', 'asistenteT2']);

            $inventario = Inventario::create([
                'programa_estudio_id' => $programa->id,
                'serie'               => $serie,
                'numero'              => $this->siguienteNumero($programa->id, $serie),
                'sala_id'             => $sala->id,
                'nombre_sala'         => $sala->nombre,
                'coordinador_id'      => $programa->coordinador_id,
                'nombre_coordinador'  => $programa->coordinador?->nombre_completo,
                'asistente_t1_id'     => $programa->asistente_t1_id,
                'nombre_asistente_t1' => $programa->asistenteT1?->nombre_completo,
                'asistente_t2_id'     => $programa->asistente_t2_id,
                'nombre_asistente_t2' => $programa->asistenteT2?->nombre_completo,
                'fecha_entrega'       => $fechaEntrega,
                'observacion'         => $observacion,
                'estado_inventario'   => 'ABIERTO',
            ]);

            if ($copiarBienes) {
                $sala->bienes()
                    ->with(['grupo', 'tipo', 'marca', 'modelo', 'estado'])
                    ->each(fn (Bien $bien) => $this->agregarBien($inventario, $bien));
            }

            return $inventario;
        });
    }

    public function agregarBien(Inventario $inventario, Bien $bien, ?string $nroDrelm = null): InventarioDetalle
    {
        $this->asegurarAbierto($inventario);

        return $inventario->detalles()->create(
            InventarioDetalle::datosDesdeBien($bien, $nroDrelm)
        );
    }

    public function actualizarNroDrelm(InventarioDetalle $detalle, ?string $nroDrelm): InventarioDetalle
    {
        $this->asegurarAbierto($detalle->inventario);
        $detalle->update(['nro_drelm' => $nroDrelm]);

        return $detalle;
    }

    public function cambiarEstado(Inventario $inventario, string $estado): Inventario
    {
        if (! in_array($estado, ['ABIERTO', 'CERRADO', 'ANULADO'], true)) {
            throw ValidationException::withMessages(['estado' => 'Estado de inventario no válido.']);
        }

        $inventario->update(['estado_inventario' => $estado]);

        return $inventario;
    }


    private function siguienteNumero(int $programaId, string $serie): string
    {
        $max = Inventario::withTrashed()
            ->where('programa_estudio_id', $programaId)
            ->where('serie', $serie)
            ->lockForUpdate()
            ->max(DB::raw('CAST(numero AS UNSIGNED)'));

        return (string) (((int) $max) + 1);
    }

    private function asegurarAbierto(Inventario $inventario): void
    {
        if (! $inventario->estaAbierto()) {
            throw ValidationException::withMessages([
                'inventario' => 'Solo se puede modificar un inventario ABIERTO.',
            ]);
        }
    }
}
