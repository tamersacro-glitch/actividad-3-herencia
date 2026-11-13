<?php

declare(strict_types=1);

require_once __DIR__ . '/EquipoComputo.php';

/** Guarda las características propias de una laptop. */
class Laptop extends EquipoComputo
{
    private float $tamanoPantalla;
    private int $duracionBateria;

    /** El constructor aprovecha el de EquipoComputo y agrega pantalla y batería. */
    public function __construct(
        string $numeroSerie,
        string $marca,
        string $modelo,
        string $procesador,
        int $memoriaRam,
        string $almacenamiento,
        float $tamanoPantalla,
        int $duracionBateria,
        string $estado = 'Disponible'
    ) {
        parent::__construct($numeroSerie, $marca, $modelo, $procesador, $memoriaRam, $almacenamiento, $estado);
        $this->tamanoPantalla = $tamanoPantalla;
        $this->duracionBateria = $duracionBateria;
    }

    public function getTipo(): string
    {
        return 'Laptop';
    }

    public function getTamanoPantalla(): float
    {
        return $this->tamanoPantalla;
    }

    public function getDuracionBateria(): int
    {
        return $this->duracionBateria;
    }

    /** Sobrescribe el resumen para mostrar los datos exclusivos de una laptop. */
    public function getResumen(): string
    {
        return parent::getResumen() . sprintf(
            ' | Pantalla: %.1f pulgadas | Batería: %d h',
            $this->tamanoPantalla,
            $this->duracionBateria
        );
    }

    /** Guarda también la pantalla y la duración de la batería. */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'tamanoPantalla' => $this->tamanoPantalla,
            'duracionBateria' => $this->duracionBateria,
        ]);
    }
}
