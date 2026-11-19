<?php

namespace App\Support;

use App\Models\Usuario;

/**
 * Única fuente de verdad de "quién puede hacer qué".
 * El superusuario no aparece aquí: Usuario::puede() lo deja pasar siempre.
 *
 * Alcance (a qué datos llega cada rol) se resuelve aparte, en Usuario::idsProgramasPermitidos():
 *   - Superusuario        -> todas las instituciones (o la que elija)
 *   - Admin institución   -> todos los programas de SU institución
 *   - Coordinador / Asistente / Docente -> solo los programas que el administrador les asignó
 */
class Permisos
{
    /** Catálogos que pertenecen a un programa de estudio. "estado" es global y solo lo toca el superusuario. */
    public const CATALOGOS_PROGRAMA = ['grupo', 'sala', 'tipo', 'marca', 'modelo'];

    /** Catálogos de apoyo que el asistente puede ampliar (sin borrar). */
    private const CATALOGOS_APOYO = ['tipo', 'marca', 'modelo'];

    public static function deRol(string $rol): array
    {
        $ver = fn (array $slugs) => array_map(fn ($c) => "catalogo.$c.ver", $slugs);

        $gestionar = fn (array $slugs) => collect($slugs)
            ->flatMap(fn ($c) => ["catalogo.$c.crear", "catalogo.$c.editar", "catalogo.$c.eliminar"])
            ->all();

        $base = ['bienes.ver', 'inventarios.ver'];

        return match ($rol) {
            // Director: todo lo de su institución (programas, personal, usuarios, bienes, catálogos)
            Usuario::ADMIN_INSTITUCION => array_merge(
                $base,
                ['institucion.ver', 'institucion.gestionar', 'usuarios.gestionar',
                 'bienes.registrar', 'bienes.inactivar', 'catalogos.ver'],
                $ver(self::CATALOGOS_PROGRAMA),
                $gestionar(self::CATALOGOS_PROGRAMA),
            ),

            // Coordinador: opera el inventario de sus programas y ve a su personal (solo lectura)
            Usuario::COORDINADOR => array_merge(
                $base,
                ['institucion.ver', 'bienes.registrar', 'bienes.inactivar', 'catalogos.ver'],
                $ver(self::CATALOGOS_PROGRAMA),
                $gestionar(self::CATALOGOS_PROGRAMA),
            ),

            // Asistente: registra y edita bienes; amplía tipo/marca/modelo; no da de baja ni borra
            Usuario::ASISTENTE => array_merge(
                $base,
                ['bienes.registrar', 'catalogos.ver'],
                $ver(self::CATALOGOS_PROGRAMA),
                array_merge(...array_map(
                    fn ($c) => ["catalogo.$c.crear", "catalogo.$c.editar"],
                    self::CATALOGOS_APOYO
                )),
            ),

            // Docente: solo consulta (bienes e inventarios de sus programas)
            Usuario::DOCENTE => $base,

            default => [],
        };
    }
}
