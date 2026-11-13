<?php

declare(strict_types=1);

require_once __DIR__ . '/EquipoComputo.php';

/** Agrega los datos que solo se necesitan para una PC de escritorio. */
class PCEscritorio extends EquipoComputo
{
    private string $tipoGabinete;
    private int $potenciaFuente;

    /** parent::__construct() guarda los datos comunes del equipo. */
    public function __construct(
        string $numeroSerie,
        string $marca,
        string $modelo,
        string $procesador,
        int $memoriaRam,
        string $almacenamiento,
        string $tipoGabinete,
        int $potenciaFuente,
        string $estado = 'Disponible'
    ) {
        parent::__construct($numeroSerie, $marca, $modelo, $procesador, $memoriaRam, $almacenamiento, $estado);
        $this->tipoGabinete = trim($tipoGabinete);
        $this->potenciaFuente = $potenciaFuente;
    }

    public function getTipo(): string
    {
        return 'Escritorio';
    }

    public function getTipoGabinete(): string
    {
        return $this->tipoGabinete;
    }

    public function getPotenciaFuente(): int
    {
        return $this->potenciaFuente;
    }

    /** Usa el resumen de la clase base y le añade los datos del gabinete. */
    public function getResumen(): string
    {
        return parent::getResumen() . sprintf(
            ' | Gabinete: %s | Fuente: %d W',
            $this->tipoGabinete,
            $this->potenciaFuente
        );
    }

    /** Conserva los datos comunes y agrega los campos de escritorio. */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'tipoGabinete' => $this->tipoGabinete,
            'potenciaFuente' => $this->potenciaFuente,
        ]);
    }
}
