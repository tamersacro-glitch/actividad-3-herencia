<?php

declare(strict_types=1);

require_once __DIR__ . '/../clases/InventarioComputo.php';

/** Detiene la prueba cuando un resultado no es el esperado. */
function comprobar(bool $resultado, string $mensaje): void
{
    if (!$resultado) {
        throw new RuntimeException($mensaje);
    }
}

$escritorio = new PCEscritorio(
    'PC-001', 'Dell', 'OptiPlex 7010', 'Intel Core i5', 16, '512 GB SSD', 'Torre', 650
);
$laptop = new Laptop(
    'LT-001', 'Lenovo', 'ThinkPad E14', 'Intel Core i7', 16, '1 TB SSD', 14.0, 8, 'En uso'
);

comprobar($escritorio instanceof EquipoComputo, 'La PC de escritorio debe heredar de EquipoComputo.');
comprobar($laptop instanceof EquipoComputo, 'La laptop debe heredar de EquipoComputo.');
comprobar($escritorio->getTipoGabinete() === 'Torre', 'El gabinete de escritorio no coincide.');
comprobar($laptop->getDuracionBateria() === 8, 'La duración de batería no coincide.');
comprobar(strpos($escritorio->getResumen(), 'Fuente: 650 W') !== false, 'El resumen de escritorio no fue sobrescrito.');
comprobar(strpos($laptop->getResumen(), 'Pantalla: 14.0 pulgadas') !== false, 'El resumen de laptop no fue sobrescrito.');

$inventario = new InventarioComputo();
comprobar($inventario->agregar($escritorio), 'No se pudo agregar la PC.');
comprobar($inventario->agregar($laptop), 'No se pudo agregar la laptop.');
comprobar(!$inventario->agregar($laptop), 'El inventario aceptó una serie repetida.');
comprobar(count($inventario->filtrarPorTipo('Laptop')) === 1, 'El filtro de laptops no coincide.');

// La sesión guarda arreglos y el constructor los convierte otra vez en subclases.
$inventarioRestaurado = new InventarioComputo($inventario->obtenerTodos());
comprobar($inventarioRestaurado->buscarPorSerie('PC-001') instanceof PCEscritorio, 'No se reconstruyó la subclase de escritorio.');
comprobar($inventarioRestaurado->buscarPorSerie('LT-001') instanceof Laptop, 'No se reconstruyó la subclase laptop.');
comprobar($inventarioRestaurado->eliminar('LT-001'), 'No se pudo eliminar la laptop.');
comprobar($inventarioRestaurado->contar() === 1, 'El conteo después de eliminar no coincide.');

// Las sesiones guardadas desde la actividad anterior no tenían estos campos.
$equiposAnteriores = new InventarioComputo([
    [
        'numeroSerie' => 'PC-ANT', 'tipo' => 'Escritorio', 'marca' => 'HP', 'modelo' => 'ProDesk',
        'procesador' => 'Intel Core i5', 'memoriaRam' => 8, 'almacenamiento' => '256 GB SSD',
    ],
    [
        'numeroSerie' => 'LT-ANT', 'tipo' => 'Laptop', 'marca' => 'Dell', 'modelo' => 'Latitude',
        'procesador' => 'Intel Core i5', 'memoriaRam' => 8, 'almacenamiento' => '256 GB SSD',
    ],
]);
$pcAnterior = $equiposAnteriores->buscarPorSerie('PC-ANT');
$laptopAnterior = $equiposAnteriores->buscarPorSerie('LT-ANT');
comprobar($pcAnterior instanceof PCEscritorio && $pcAnterior->getPotenciaFuente() === 500, 'No se aplicaron los valores iniciales para el equipo anterior.');
comprobar($laptopAnterior instanceof Laptop && $laptopAnterior->getDuracionBateria() === 4, 'No se aplicaron los valores iniciales para la laptop anterior.');

echo "Pruebas de herencia correctas.\n";
