# Aplicación base — Sistemas de Información Avanzada (6SC)

Universidad Agraria del Ecuador · Carrera de Computación en línea · 2026-2027 CII
Docente: Ing. Andrea Sinche Guzmán, MSc.

Aplicación web base del proyecto **«De aplicación web a sistema de información»**.
Es un sistema de inventario y ventas (PHP + MySQL + Bootstrap) con un caso de ejemplo
de **agroservicio**. Cada equipo lo adapta a su organización y lo hace crecer, unidad por
unidad, hasta convertirlo en un sistema de información integrado.

---

## 1. Qué trae la aplicación

| Módulo | Qué hace |
| --- | --- |
| Login con roles y permisos | Administrador, vendedor y bodeguero; contraseñas cifradas con `password_hash` |
| Cambiar contraseña | Solo con la sesión iniciada y para el propio usuario; exige la contraseña actual (menú del usuario, arriba a la derecha) |
| Usuarios | El administrador crea usuarios y asigna permisos por módulo |
| Clientes, categorías, productos, proveedores | Registro y listado (CRUD) |
| Compras | Registra la compra, suma stock y calcula el precio de venta con el margen |
| Ventas | Registra la venta, valida el stock y lo descuenta |
| Kardex e inventario | Movimientos de entrada y salida por producto |
| Reportes y exportación CSV | Reportes imprimibles y descarga en CSV |
| **API de datos** (`api/datos.php`) | Entrega ventas, compras y productos en **JSON o CSV** para Power BI, Looker Studio y Postman |

Estructura (patrón MVC):

```
sia-base/
├── api/datos.php          ← endpoint de datos (JSON / CSV)
├── controller/            ← reciben formularios y deciden qué hacer
├── model/                 ← clases que hablan con la base de datos
├── view/                  ← pantallas
├── partials/              ← barra superior y menú lateral
├── sql/sia_base.sql       ← base de datos con datos de ejemplo
├── config.ejemplo.php     ← plantilla de configuración
└── index.php
```

La base de datos de ejemplo tiene 15 productos, 25 clientes, 4 proveedores y
**más de 1 400 ventas entre enero y septiembre de 2026**, suficientes para construir
los indicadores de la Unidad 1. Todos los datos son ficticios.

---

## 2. Instalación en XAMPP (Windows o Mac)

1. Instale [XAMPP](https://www.apachefriends.org) e inicie **Apache** y **MySQL**.
2. Copie la carpeta `sia-base` dentro de `htdocs`
   (Windows: `C:\xampp\htdocs` · Mac: `/Applications/XAMPP/htdocs`).
3. Abra `http://localhost/phpmyadmin`, cree una base de datos llamada **`sia_base`**
   (cotejamiento `utf8mb4_general_ci`), selecciónela y use **Importar** con `sql/sia_base.sql`.
4. Copie `config.ejemplo.php` y renombre la copia como **`config.php`**. Revise los datos
   (en XAMPP: usuario `root`, contraseña vacía) y **cambie el `API_TOKEN`**.
5. Abra `http://localhost/sia-base`.

**Usuarios de prueba**

| Correo | Contraseña | Rol |
| --- | --- | --- |
| admin@sia.test | Admin2026* | Administrador |
| vendedor1@sia.test | Sia2026* | Vendedor |
| vendedor2@sia.test | Sia2026* | Vendedor |
| bodega@sia.test | Sia2026* | Bodeguero |

> Cambie estas contraseñas antes de publicar el sistema en Internet.

---

## 3. Publicación en AwardSpace

1. En el panel de AwardSpace cree una base de datos MySQL y anote **host, nombre, usuario y contraseña**.
2. Abra **phpMyAdmin** desde el panel, seleccione su base e **Importe** `sql/sia_base.sql`.
3. En el **File Manager**, suba el contenido de la carpeta `sia-base` a la carpeta de su dominio.
4. En el hosting, cree `config.php` (copia de `config.ejemplo.php`) con los datos de la base del paso 1.
5. Abra su URL (`http://su-sitio.atwebpages.com`) e inicie sesión.

> El plan gratuito de AwardSpace no permite que PHP se conecte a servicios externos (cURL).
> Si necesita consumir una API externa (por ejemplo, el clima), hágalo con `fetch` en JavaScript.

---

## 4. Repositorio en GitHub

1. Cada integrante crea su cuenta en [github.com](https://github.com) con su correo institucional.
2. Un integrante crea el repositorio: **+ → New repository**, nombre `SIA_6SC_GN_nombresistema`,
   **Private**, y **Create repository**.
3. **Settings → Collaborators → Add people**: agregue a sus compañeros y a la docente (**asinche**).
4. Suba el código con **Add file → Upload files** o con **GitHub Desktop** (Clone → Commit → Push).
5. Cada integrante sube sus cambios con su propia cuenta: los commits evidencian su aporte.

> `config.php` está en `.gitignore` y **no se sube**: contiene la contraseña de la base de datos.
> En GitHub solo va `config.ejemplo.php`.

---

## 5. API de datos

| Petición | Resultado |
| --- | --- |
| `api/datos.php?token=SU_TOKEN` | Ventas en JSON |
| `api/datos.php?token=SU_TOKEN&recurso=compras` | Compras en JSON |
| `api/datos.php?token=SU_TOKEN&recurso=productos` | Productos y stock en JSON |
| `...&formato=csv` | Cualquiera de los anteriores en CSV |

Por la LOPDP, la API **no entrega datos personales** de los clientes (cédula, teléfono, email).

**Power BI Service** (app.powerbi.com, cuenta institucional):
Crear → Obtener datos → **Texto o CSV** → Vínculo al archivo → pegue la URL con `&formato=csv`
→ conexión nueva, sin puerta de enlace, autenticación **Anónima**.
En Power Query, borre el paso automático «Tipo de columna cambiado» y convierta
`precio_unitario` y `total` con **Cambiar tipo → Usar configuración regional → Número decimal → English (United States)**;
si no, `9.1` se lee como `91`. Para traer datos nuevos: **Actualizar ahora** en el modelo semántico.

**Looker Studio**: en Google Sheets escriba en A1
`=IMPORTDATA("https://.../api/datos.php?token=SU_TOKEN&formato=csv")`
y conecte esa hoja desde Looker Studio.

---

## 6. Lo que construye cada equipo

Siga la **Guía del Proyecto** publicada en Moodle. En resumen:

| Unidad | Lo que se agrega al sistema |
| --- | --- |
| 1. Inteligencia de negocios | KPI y dashboard conectado a la API |
| 2. Comercio electrónico | Tienda pública: catálogo, carrito y pedido; riesgos y política LOPDP |
| 3. SIRH | Módulo de personal, asistencias e indicadores de People Analytics |
| 4. SIG e interoperabilidad | Coordenadas, capas en QGIS, mapa en el sistema y endpoint documentado en Postman |

Antes de empezar, adapten la aplicación a su caso: nombre del sistema (busque «Mi Proyecto»),
categorías, productos y datos de su organización.
