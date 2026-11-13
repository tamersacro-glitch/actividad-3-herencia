<?php

declare(strict_types=1);

/** Guarda los datos que comparten las computadoras del inventario. */
abstract class EquipoComputo
{
    private string $numeroSerie;
    private string $marca;
    private string $modelo;
    private string $procesador;
    private int $memoriaRam;
    private string $almacenamiento;
    private string $estado;

    /** El constructor recibe los datos que tienen en común los equipos. */
    public function __construct(
        string $numeroSerie,
        string $marca,
        string $modelo,
        string $procesador,
        int $memoriaRam,
        string $almacenamiento,
        string $estado = 'Disponible'
    ) {
        $this->numeroSerie = trim($numeroSerie);
        $this->marca = trim($marca);
        $this->modelo = trim($modelo);
        $this->procesador = trim($procesador);
        $this->memoriaRam = $memoriaRam;
        $this->almacenamiento = trim($almacenamiento);
        $this->estado = $estado;
    }

    /** Cada subclase indica qué tipo de equipo representa. */
    abstract public function getTipo(): string;

    public function getNumeroSerie(): string
    {
        return $this->numeroSerie;
    }

    public function getMarca(): string
    {
        return $this->marca;
    }

    public function getModelo(): string
    {
        return $this->modelo;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    /** Resume los datos comunes; las clases derivadas agregan sus propios datos. */
    public function getResumen(): string
    {
        return sprintf(
            '%s %s %s | %s | %d GB RAM | %s | %s',
            $this->getTipo(),
            $this->marca,
            $this->modelo,
            $this->procesador,
            $this->memoriaRam,
            $this->almacenamiento,
            $this->estado
        );
    }

    /** Prepara los datos para guardarlos en la sesión. */
    public function toArray(): array
    {
        return [
            'numeroSerie' => $this->numeroSerie,
            'tipo' => $this->getTipo(),
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'procesador' => $this->procesador,
            'memoriaRam' => $this->memoriaRam,
            'almacenamiento' => $this->almacenamiento,
            'estado' => $this->estado,
            'resumen' => $this->getResumen(),
        ];
    }
}
