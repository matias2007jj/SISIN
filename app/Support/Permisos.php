<?php

namespace App\Support;

use App\Models\Usuario;


class Permisos
{
    public const CATALOGOS_PROGRAMA = ['grupo', 'sala', 'tipo', 'marca', 'modelo'];

    private const CATALOGOS_APOYO = ['tipo', 'marca', 'modelo'];

    public static function deRol(string $rol): array
    {
        $ver = fn (array $slugs) => array_map(fn ($c) => "catalogo.$c.ver", $slugs);

        $gestionar = fn (array $slugs) => collect($slugs)
            ->flatMap(fn ($c) => ["catalogo.$c.crear", "catalogo.$c.editar", "catalogo.$c.eliminar"])
            ->all();

        $base = ['bienes.ver', 'inventarios.ver'];

        return match ($rol) {
            Usuario::ADMIN_INSTITUCION => array_merge(
                $base,
                ['institucion.ver', 'institucion.gestionar', 'usuarios.gestionar',
                 'bienes.registrar', 'bienes.inactivar', 'catalogos.ver'],
                $ver(self::CATALOGOS_PROGRAMA),
                $gestionar(self::CATALOGOS_PROGRAMA),
            ),

            Usuario::COORDINADOR => array_merge(
                $base,
                ['institucion.ver', 'bienes.registrar', 'bienes.inactivar', 'catalogos.ver'],
                $ver(self::CATALOGOS_PROGRAMA),
                $gestionar(self::CATALOGOS_PROGRAMA),
            ),

            Usuario::ASISTENTE => array_merge(
                $base,
                ['bienes.registrar', 'catalogos.ver'],
                $ver(self::CATALOGOS_PROGRAMA),
                array_merge(...array_map(
                    fn ($c) => ["catalogo.$c.crear", "catalogo.$c.editar"],
                    self::CATALOGOS_APOYO
                )),
            ),

            Usuario::DOCENTE => $base,

            default => [],
        };
    }
}
