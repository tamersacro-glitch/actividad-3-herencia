<?php

declare(strict_types=1);

require_once __DIR__ . '/PCEscritorio.php';
require_once __DIR__ . '/Laptop.php';

/** Administra cualquier objeto que herede de EquipoComputo. */
class InventarioComputo
{
    /** @var EquipoComputo[] */
    private array $equipos = [];

    /** Al iniciar la sesión, crea de nuevo las subclases guardadas. */
    public function __construct(array $datosGuardados = [])
    {
        foreach ($datosGuardados as $datos) {
            if (is_array($datos)) {
                $equipo = $this->crearDesdeDatos($datos);
                if ($equipo !== null) {
                    $this->equipos[] = $equipo;
                }
            }
        }
    }

    /** Reconstruye la subclase indicada por el tipo que se guardó. */
    private function crearDesdeDatos(array $datos): ?EquipoComputo
    {
        $comunes = [
            (string) ($datos['numeroSerie'] ?? ''),
            (string) ($datos['marca'] ?? ''),
            (string) ($datos['modelo'] ?? ''),
            (string) ($datos['procesador'] ?? ''),
            (int) ($datos['memoriaRam'] ?? 0),
            (string) ($datos['almacenamiento'] ?? ''),
        ];
        $estado = (string) ($datos['estado'] ?? 'Disponible');

        if (($datos['tipo'] ?? '') === 'Escritorio') {
            return new PCEscritorio(
                $comunes[0],
                $comunes[1],
                $comunes[2],
                $comunes[3],
                $comunes[4],
                $comunes[5],
                (string) ($datos['tipoGabinete'] ?? 'Torre'),
                (int) ($datos['potenciaFuente'] ?? 500),
                $estado
            );
        }

        if (($datos['tipo'] ?? '') === 'Laptop') {
            return new Laptop(
                $comunes[0],
                $comunes[1],
                $comunes[2],
                $comunes[3],
                $comunes[4],
                $comunes[5],
                (float) ($datos['tamanoPantalla'] ?? 15.6),
                (int) ($datos['duracionBateria'] ?? 4),
                $estado
            );
        }

        return null;
    }

    /** Agrega una subclase de EquipoComputo si la serie no está repetida. */
    public function agregar(EquipoComputo $equipo): bool
    {
        if ($this->buscarPorSerie($equipo->getNumeroSerie()) !== null) {
            return false;
        }

        $this->equipos[] = $equipo;
        return true;
    }

    /** Busca por serie sin distinguir mayúsculas y minúsculas. */
    public function buscarPorSerie(string $numeroSerie): ?EquipoComputo
    {
        foreach ($this->equipos as $equipo) {
            if (strcasecmp($equipo->getNumeroSerie(), trim($numeroSerie)) === 0) {
                return $equipo;
            }
        }

        return null;
    }

    /** Elimina un equipo y avisa si encontró la serie. */
    public function eliminar(string $numeroSerie): bool
    {
        foreach ($this->equipos as $indice => $equipo) {
            if (strcasecmp($equipo->getNumeroSerie(), trim($numeroSerie)) === 0) {
                unset($this->equipos[$indice]);
                $this->equipos = array_values($this->equipos);
                return true;
            }
        }

        return false;
    }

    /** Filtra los objetos derivados por el tipo elegido. */
    public function filtrarPorTipo(string $tipo): array
    {
        if ($tipo === '') {
            return $this->equipos;
        }

        return array_values(array_filter(
            $this->equipos,
            static fn (EquipoComputo $equipo): bool => $equipo->getTipo() === $tipo
        ));
    }

    /** Convierte cada subclase en datos para guardarla en la sesión. */
    public function obtenerTodos(): array
    {
        return array_map(static fn (EquipoComputo $equipo): array => $equipo->toArray(), $this->equipos);
    }

    public function contar(): int
    {
        return count($this->equipos);
    }
}
