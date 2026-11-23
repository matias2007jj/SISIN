<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Bien;
use App\Models\Institucion;
use App\Models\Inventario;
use App\Models\Personal;
use App\Models\ProgramaEstudio;
use App\Models\Usuario;
use App\Support\ContextoInstitucion;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    private const ESTADO_INACTIVO = 6;

    public function index()
    {
        /** @var Usuario $user */
        $user           = Auth::user();$ids            = $user->idsProgramasPermitidos();$verInstitucion = $user->veTodaLaInstitucion();$institucion    = $user->institucionActiva();$global         = $user->esSuperusuario() && !$institucion;

        $bienes = Bien::query();$inventarios = Inventario::query()->where('estado_inventario', 'ABIERTO');

        if (! $verInstitucion) {
            $bienes->whereHas('sala', fn ($q) => $q->whereIn('programa_estudio_id',$ids));
            $inventarios->whereIn('programa_estudio_id',$ids);
        }

        $totalBienes = (clone $bienes)->count();$inactivos   = (clone $bienes)->where('estado_id', self::ESTADO_INACTIVO)->count();$operativos  = $totalBienes -$inactivos;

        if ($global) {$resumen = [
                ['label' => 'Instituciones',        'value' => Institucion::count()],
                ['label' => 'Programas de estudio', 'value' => ProgramaEstudio::count()],
                ['label' => 'Personal registrado',  'value' => Personal::count()],
                ['label' => 'Bienes operativos',    'value' => $operativos],
            ];
        } elseif ($verInstitucion) {$resumen = [
                ['label' => 'Programas de estudio', 'value' => count($ids)],
                ['label' => 'Personal',             'value' => Personal::count()],
                ['label' => 'Bienes operativos',    'value' => $operativos],
                ['label' => 'Usuarios',             'value' => $this->totalUsuarios()],
            ];
        } elseif ($user->puede('institucion.ver')) {$resumen = [
                ['label' => 'Mis programas',          'value' => count($ids)],
                ['label' => 'Personal a cargo',       'value' => Asignacion::whereIn('programa_estudio_id', $ids)->distinct()->count('personal_id')],
                ['label' => 'Bienes operativos',      'value' => $operativos],
                ['label' => 'Inventarios abiertos',   'value' => $inventarios->count()],
            ];
        } else {
            $resumen = [
                ['label' => 'Mis programas',        'value' => count($ids)],
                ['label' => 'Bienes operativos',    'value' => $operativos],
                ['label' => 'Bienes inactivos',     'value' => $inactivos, 'warn' => true],
                ['label' => 'Inventarios abiertos', 'value' => $inventarios->count()],
            ];
        }

/////////////////

        return view('menu', [
            'resumen'        => $resumen,
            'accesos'        => $this->accesos($user,$institucion),
            'institucion'    => $institucion,
            'global'         => $global,
            'bienesActivos'  => $operativos,
            'bienesInactivos'=> $inactivos,
        ]);
    }

    private function accesos(Usuario $user, ?Institucion $institucion): array
    {
        $accesos = [];

        if ($user->puede('bienes.ver')) {$accesos[] = [
                'titulo' => 'Bienes',
                'texto'  => $user->puede('bienes.registrar')
                    ? 'Registre, edite y consulte los bienes del programa.'
                    : 'Consulte los bienes del programa.',
                'url'    => route('bienes.index'),
            ];
        }

        if ($user->puede('inventarios.ver')) {$accesos[] = [
                'titulo' => 'Toma de Inventario',
                'texto'  => 'Consulte las tomas de inventario realizadas.',
                'url'    => route('inventarios.index'),
            ];
        }

        if ($user->puede('catalogos.ver')) {$accesos[] = [
                'titulo' => 'Tablas Generales',
                'texto'  => $user->puede('catalogo.sala.crear')
                    ? 'Salas, grupos, tipos, marcas y modelos del programa.'
                    : 'Salas, grupos, tipos, marcas y modelos (consulta y apoyo).',
                'url'    => route('catalogo.index', 'sala'),
            ];
        }

        if ($user->esSuperusuario()) {$accesos[] = [
                'titulo' => 'Instituciones',
                'texto'  => 'Alta de instituciones y de su usuario administrador.',
                'url'    => route('instituciones.index'),
            ];
        } elseif ($user->institucion_id && $user->puede('institucion.ver')) {$gestiona = $user->puede('institucion.gestionar');$accesos[] = [
                'titulo' => $gestiona ? 'Mi institución' : 'Mi programa',
                'texto'  => $gestiona
                    ? 'Programas de estudio y personal de la institución.'
                    : 'Personal de los programas que coordina.',
                'url'    => route('instituciones.show', $user->institucion_id),
            ];
        }

        if ($user->puedeAdministrarUsuarios()) {$accesos[] = [
                'titulo' => 'Usuarios',
                'texto'  => 'Cree usuarios para su personal y asígneles rol y programas.',
                'url'    => route('usuarios.index'),
            ];
        }

        return $accesos;
    }

    private function totalUsuarios(): int
    {
        $q = Usuario::query()->whereNotNull('institucion_id');

        if (($id = ContextoInstitucion::id()) !== null) {
            $q->where('institucion_id',$id);
        }

        return $q->count();
    }
}