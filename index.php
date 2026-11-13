<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/clases/InventarioComputo.php';

// Aquí se recuperan como objetos los equipos guardados en la sesión.
$inventario = new InventarioComputo($_SESSION['equipos'] ?? []);
$mensaje = '';
$tipoMensaje = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = (string) ($_POST['accion'] ?? '');

    if ($accion === 'agregar') {
        $serie = trim((string) ($_POST['numeroSerie'] ?? ''));
        $tipo = (string) ($_POST['tipo'] ?? '');
        $marca = trim((string) ($_POST['marca'] ?? ''));
        $modelo = trim((string) ($_POST['modelo'] ?? ''));
        $procesador = trim((string) ($_POST['procesador'] ?? ''));
        $ram = filter_var($_POST['memoriaRam'] ?? null, FILTER_VALIDATE_INT);
        $almacenamiento = trim((string) ($_POST['almacenamiento'] ?? ''));
        $estado = (string) ($_POST['estado'] ?? 'Disponible');

        if ($serie === '' || $marca === '' || $modelo === '' || $procesador === '' || $almacenamiento === '' || $ram === false || $ram === null || $ram < 1) {
            $mensaje = 'Completa los datos generales y escribe una cantidad de RAM válida.';
            $tipoMensaje = 'error';
        } elseif (!in_array($tipo, ['Escritorio', 'Laptop'], true) || !in_array($estado, ['Disponible', 'En uso', 'En reparación'], true)) {
            $mensaje = 'Selecciona un tipo y un estado válidos.';
            $tipoMensaje = 'error';
        } else {
            // Según el tipo, se crea una clase derivada distinta.
            $equipo = null;

            if ($tipo === 'Escritorio') {
                $gabinete = trim((string) ($_POST['tipoGabinete'] ?? ''));
                $potencia = filter_var($_POST['potenciaFuente'] ?? null, FILTER_VALIDATE_INT);

                if ($gabinete === '' || $potencia === false || $potencia === null || $potencia < 1) {
                    $mensaje = 'Escribe el tipo de gabinete y una potencia válida para la fuente.';
                    $tipoMensaje = 'error';
                } else {
                    $equipo = new PCEscritorio($serie, $marca, $modelo, $procesador, (int) $ram, $almacenamiento, $gabinete, $potencia, $estado);
                }
            } else {
                $pantalla = filter_var($_POST['tamanoPantalla'] ?? null, FILTER_VALIDATE_FLOAT);
                $bateria = filter_var($_POST['duracionBateria'] ?? null, FILTER_VALIDATE_INT);

                if ($pantalla === false || $pantalla === null || $pantalla <= 0 || $bateria === false || $bateria === null || $bateria < 1) {
                    $mensaje = 'Escribe el tamaño de pantalla y las horas de batería de la laptop.';
                    $tipoMensaje = 'error';
                } else {
                    $equipo = new Laptop($serie, $marca, $modelo, $procesador, (int) $ram, $almacenamiento, (float) $pantalla, $bateria, $estado);
                }
            }

            if ($equipo instanceof EquipoComputo) {
                if ($inventario->agregar($equipo)) {
                    $mensaje = 'Equipo agregado al inventario.';
                    $tipoMensaje = 'success';
                } else {
                    $mensaje = 'Ya existe un equipo con ese número de serie.';
                    $tipoMensaje = 'error';
                }
            }
        }
    } elseif ($accion === 'eliminar') {
        if ($inventario->eliminar((string) ($_POST['numeroSerie'] ?? ''))) {
            $mensaje = 'Equipo eliminado del inventario.';
            $tipoMensaje = 'success';
        } else {
            $mensaje = 'No se encontró ese número de serie.';
            $tipoMensaje = 'error';
        }
    }

    $_SESSION['equipos'] = $inventario->obtenerTodos();
}

$filtro = (string) ($_GET['tipo'] ?? '');
$busqueda = trim((string) ($_GET['buscar'] ?? ''));
$equipos = array_map(
    static fn (EquipoComputo $equipo): array => $equipo->toArray(),
    $inventario->filtrarPorTipo($filtro)
);
if ($busqueda !== '') {
    $equipos = array_values(array_filter($equipos, static function (array $equipo) use ($busqueda): bool {
        $texto = implode(' ', [$equipo['numeroSerie'], $equipo['marca'], $equipo['modelo'], $equipo['tipo']]);
        return stripos($texto, $busqueda) !== false;
    }));
}

/** Escapa los datos antes de imprimirlos en la página. */
function escapar(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventario de equipos | TecnoStock</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php"><span class="brand-mark">TS</span><span>TecnoStock<small>INVENTARIO DE CÓMPUTO</small></span></a>
    <span class="topbar-note">Actividad formativa 3 · Herencia</span>
</header>
<main class="page">
    <section class="intro">
        <div><p class="eyebrow">CLASE BASE Y CLASES DERIVADAS</p><h1>Inventario de cómputo</h1><p class="muted">Cada tipo de equipo comparte datos y agrega sus propias características.</p></div>
        <div class="total"><span>Equipos registrados</span><strong><?= $inventario->contar() ?></strong></div>
    </section>

    <?php if ($mensaje !== ''): ?>
        <div class="notice <?= escapar($tipoMensaje) ?>" role="status"><?= escapar($mensaje) ?></div>
    <?php endif; ?>

    <div class="layout">
        <section class="panel form-panel">
            <div class="panel-heading"><div><p class="eyebrow">NUEVO REGISTRO</p><h2>Agregar equipo</h2></div><span class="plus">+</span></div>
            <form method="post" action="index.php" class="equipment-form">
                <input type="hidden" name="accion" value="agregar">
                <label>Número de serie<input name="numeroSerie" required maxlength="60" placeholder="Ej. PC-2025-001"></label>
                <div class="form-row">
                    <label>Tipo<select name="tipo" id="tipoEquipo" required><option value="">Selecciona</option><option>Laptop</option><option>Escritorio</option></select></label>
                    <label>Estado<select name="estado"><option>Disponible</option><option>En uso</option><option>En reparación</option></select></label>
                </div>
                <div class="form-row">
                    <label>Marca<input name="marca" required maxlength="60" placeholder="Dell"></label>
                    <label>Modelo<input name="modelo" required maxlength="80" placeholder="OptiPlex 7010"></label>
                </div>
                <label>Procesador<input name="procesador" required maxlength="100" placeholder="Intel Core i5"></label>
                <div class="form-row">
                    <label>Memoria RAM (GB)<input type="number" name="memoriaRam" min="1" max="2048" required placeholder="16"></label>
                    <label>Almacenamiento<input name="almacenamiento" required maxlength="80" placeholder="512 GB SSD"></label>
                </div>
                <div id="camposEscritorio" class="tipo-fields" hidden>
                    <div class="form-row">
                        <label>Tipo de gabinete<input name="tipoGabinete" maxlength="40" placeholder="Torre"></label>
                        <label>Fuente (W)<input type="number" name="potenciaFuente" min="1" max="3000" placeholder="650"></label>
                    </div>
                </div>
                <div id="camposLaptop" class="tipo-fields" hidden>
                    <div class="form-row">
                        <label>Pantalla (pulgadas)<input type="number" name="tamanoPantalla" min="1" max="100" step="0.1" placeholder="15.6"></label>
                        <label>Batería (horas)<input type="number" name="duracionBateria" min="1" max="48" placeholder="8"></label>
                    </div>
                </div>
                <button class="button primary" type="submit">Agregar al inventario <span>→</span></button>
            </form>
        </section>

        <section class="panel list-panel">
            <div class="panel-heading list-heading"><div><p class="eyebrow">REGISTRO ACTUAL</p><h2>Equipos</h2></div><span class="count"><?= count($equipos) ?> resultados</span></div>
            <form class="filters" method="get" action="index.php">
                <input type="search" name="buscar" value="<?= escapar($busqueda) ?>" placeholder="Buscar por serie, marca o modelo" aria-label="Buscar equipo">
                <select name="tipo" aria-label="Filtrar por tipo"><option value="">Todos los tipos</option><option value="Escritorio" <?= $filtro === 'Escritorio' ? 'selected' : '' ?>>Escritorio</option><option value="Laptop" <?= $filtro === 'Laptop' ? 'selected' : '' ?>>Laptop</option></select>
                <button class="button secondary" type="submit">Buscar</button>
            </form>
            <?php if ($equipos === []): ?>
                <div class="empty"><span>▤</span><strong>No hay equipos para mostrar</strong><p>Agrega un registro o cambia los filtros.</p></div>
            <?php else: ?>
                <div class="table-wrap"><table><thead><tr><th>Equipo</th><th>Tipo</th><th>Resumen</th><th>Estado</th><th></th></tr></thead><tbody>
                <?php foreach ($equipos as $equipo): ?>
                    <tr>
                        <td><strong><?= escapar($equipo['marca'] . ' ' . $equipo['modelo']) ?></strong><small><?= escapar($equipo['numeroSerie']) ?></small></td>
                        <td><span class="type-pill"><?= escapar($equipo['tipo']) ?></span></td>
                        <td><?= escapar($equipo['resumen']) ?></td>
                        <td><span class="status <?= $equipo['estado'] === 'Disponible' ? 'available' : ($equipo['estado'] === 'En uso' ? 'in-use' : 'repair') ?>"><?= escapar($equipo['estado']) ?></span></td>
                        <td><form method="post" action="index.php" onsubmit="return confirm('¿Eliminar este equipo del inventario?')"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="numeroSerie" value="<?= escapar($equipo['numeroSerie']) ?>"><button class="delete" type="submit" aria-label="Eliminar <?= escapar($equipo['numeroSerie']) ?>">Eliminar</button></form></td>
                    </tr>
                <?php endforeach; ?>
                </tbody></table></div>
            <?php endif; ?>
        </section>
    </div>
    <footer>Los datos de la sesión se vuelven a crear como objetos de su subclase al abrir la página.</footer>
</main>
<script>
function actualizarCamposTipo() {
    const tipo = document.getElementById('tipoEquipo').value;
    const escritorio = document.getElementById('camposEscritorio');
    const laptop = document.getElementById('camposLaptop');
    escritorio.hidden = tipo !== 'Escritorio';
    laptop.hidden = tipo !== 'Laptop';
    escritorio.querySelectorAll('input').forEach((campo) => { campo.required = tipo === 'Escritorio'; });
    laptop.querySelectorAll('input').forEach((campo) => { campo.required = tipo === 'Laptop'; });
}
document.getElementById('tipoEquipo').addEventListener('change', actualizarCamposTipo);
actualizarCamposTipo();
</script>
</body>
</html>
