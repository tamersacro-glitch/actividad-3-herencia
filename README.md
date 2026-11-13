# Inventario de equipos de cómputo

Aplicación web escolar en PHP para registrar PCs de escritorio y laptops. Esta versión aplica herencia para compartir los datos comunes y agregar características propias a cada tipo.

La presentación de esta actividad se encuentra en `presentacion/Actividad-formativa-3-Inventario-computo-herencia.pptx`.

## Clases

```text
EquipoComputo (abstracta)
├── PCEscritorio
└── Laptop

InventarioComputo administra objetos EquipoComputo
```

- **`EquipoComputo`** reúne la serie, marca, modelo, procesador, RAM, almacenamiento y estado. También define `getResumen()` y `toArray()`.
- **`PCEscritorio`** hereda de `EquipoComputo`, llama a `parent::__construct()` y agrega el gabinete y la potencia de la fuente.
- **`Laptop`** hereda de `EquipoComputo` y agrega el tamaño de pantalla y las horas de batería.
- Las dos clases derivadas sobrescriben `getResumen()` para añadir sus datos particulares. `parent::getResumen()` conserva los datos comunes.
- **`InventarioComputo`** recibe cualquier subclase, revisa números de serie, busca, filtra y elimina equipos. Al recuperar la sesión, crea de nuevo la subclase correspondiente.
- **`index.php`** selecciona qué clase instanciar según el tipo elegido en el formulario.

## Funciones

- Registrar y consultar PCs de escritorio y laptops.
- Buscar por número de serie, marca o modelo y filtrar por tipo.
- Evitar series repetidas, eliminar equipos y consultar el total.
- Mostrar los datos comunes y los específicos de cada subclase.

## Ejecutar con XAMPP

1. Abre XAMPP Control Panel e inicia Apache.
2. Copia la carpeta del proyecto a `htdocs`, o configura un `Alias` para su ubicación.
3. Abre `http://localhost/actividad-3-herencia/` en el navegador.

## Probar las clases

Desde la carpeta del proyecto, ejecuta el archivo de comprobación con PHP:

```powershell
& "$env:LOCALAPPDATA\XAMPP\php\php.exe" .\pruebas\prueba_herencia.php
```

La prueba crea objetos de ambas subclases y revisa la herencia, el constructor de la clase base, los resúmenes sobrescritos, el inventario, el filtro, la eliminación y la reconstrucción desde los datos de sesión.

Para revisar la sintaxis de PHP:

```powershell
& "$env:LOCALAPPDATA\XAMPP\php\php.exe" -l .\index.php
& "$env:LOCALAPPDATA\XAMPP\php\php.exe" -l .\clases\EquipoComputo.php
& "$env:LOCALAPPDATA\XAMPP\php\php.exe" -l .\clases\PCEscritorio.php
& "$env:LOCALAPPDATA\XAMPP\php\php.exe" -l .\clases\Laptop.php
& "$env:LOCALAPPDATA\XAMPP\php\php.exe" -l .\clases\InventarioComputo.php
```

## Datos

Los registros se guardan en la sesión del navegador, no en una base de datos. Los equipos que ya estaban guardados desde la versión anterior reciben datos predeterminados para los campos nuevos cuando se recupera la sesión.

## Repositorio

El repositorio privado del proyecto ya está creado en GitHub.

[Repositorio en GitHub](https://github.com/tamersacro-glitch/actividad-3-herencia).
