# Estructura de Base de Datos y Casos de Uso

Fecha: 2025-08-12

## Visión General
El modelo implementa planificación, demanda, inventarios y operaciones (traslado, venta, producción y elaboración) con soporte para jerarquías de productos mediante herencia de tablas (CTI) y recetas de elaboración.

## Entidades Principales
### Organización y Geografía
- **provincias**(id, nombre, timestamps)
- **empresas**(id, nombre, siglas, direccion, activo, descripcion, timestamps)
- **uebs**(id, empresa_id FK->empresas, provincia_id FK->provincias, nombre, activo, timestamps)

### Clientes y Unidades
- **clientes**(id, nombre, descripcion, direccion, siglas, activo, timestamps)
- **unidades_medida**(id, nombre, abreviatura, timestamps)

### Catálogo de Ítems (Herencia CTI)
- **items**(id, nombre, descripcion, unidad_medida_id FK->unidades_medida, tipo ['producto','grupo'], activo, timestamps)
- **grupos**(item_id PK/FK->items, grupo_padre_id FK->grupos)
- **productos**(item_id PK/FK->items, codigo UNIQUE, imagen, grupo_id FK->grupos)

### Tiempo y Planificación
- **periodos**(id, nombre UNIQUE, fecha_inicio, fecha_fin, timestamps)
- **planificaciones**(id, ueb_id FK->uebs, periodo_id FK->periodos, item_id FK->items, plan, pronostico, estado, timestamps UNIQUE(ueb_id, periodo_id, item_id))

### Demanda
- **demandas**(id, cliente_id FK->clientes, periodo_id FK->periodos, item_id FK->items, cantidad, timestamps UNIQUE(cliente_id, periodo_id, item_id))

### Inventarios y Recetas
- **inventarios**(id, ueb_id FK->uebs, producto_id FK->productos.item_id, cantidad, costo_unitario, timestamps UNIQUE(ueb_id, producto_id))
- **recetas**(id, producto_id FK->productos.item_id, nombre, descripcion, timestamps UNIQUE(producto_id))
- **receta_items**(id, receta_id FK->recetas, item_id FK->items, cantidad, timestamps UNIQUE(receta_id, item_id))

### Operaciones (Herencia CTI)
- **operaciones**(id, ueb_id FK->uebs, tipo ['traslado','venta','produccion','elaboracion'], fecha, notas, timestamps)
  - **traslados**(operacion_id PK/FK->operaciones, destino_ueb_id FK->uebs)
  - **ventas**(operacion_id PK/FK->operaciones, cliente_id FK->clientes)
  - **producciones**(operacion_id PK/FK->operaciones)
  - **elaboraciones**(operacion_id PK/FK->operaciones, receta_id FK->recetas)
- **operacion_items**(id, operacion_id FK->operaciones, item_id FK->items, cantidad, rol, timestamps INDEX(operacion_id,item_id))

### Seguridad
- **roles**(id, nombre, slug UNIQUE)
- **accesos**(id, nombre, slug UNIQUE, descripcion)
- **acceso_role**(id, role_id FK->roles, acceso_id FK->accesos UNIQUE(role_id, acceso_id))
- **role_user**(id, role_id FK->roles, user_id FK->users UNIQUE(role_id, user_id))

## Relaciones Clave (Resumen)
- Empresa 1--N UEB
- Provincia 1--N UEB (opcional)
- Grupo (self) 1--N Subgrupos
- Grupo 1--N Productos
- Item (producto/grupo) 1--N Planificaciones / Demandas
- Producto 1--1 Receta (actual diseño)
- Receta 1--N RecetaItems (componentes pueden ser productos o grupos)
- Operación 1--1 Especialización (traslado|venta|produccion|elaboracion)
- Operación 1--N OperacionItems
- UEB 1--N Inventarios (por producto)

## Diagrama ASCII Simplificado
```
[empresas] 1--N [uebs] N--1 [provincias]

[unidades_medida] 1--N [items] < CTI > [grupos] & [productos]
 [grupos] self 1--N subgrupos
 [grupos] 1--N [productos]

[periodos]
   |                +-- plan,pronostico
[uebs]--< [planificaciones ] >--[items]
[clientes]--< [demandas ] >--[items]

[productos] 1--1 [recetas] 1--N [receta_items] >-- [items]

[operaciones] <CTI> [traslados|ventas|producciones|elaboraciones]
[operaciones] 1--N [operacion_items] >-- [items]

[uebs]--< [inventarios] >--[productos]
```

## Integridad y Restricciones Destacadas
- Unicidad de combinaciones clave (planificaciones, demandas, inventarios, receta_items).
- CTI asegura separación clara de atributos específicos (productos/grupos y subtipos de operación).
- `productos.item_id` y `grupos.item_id` dependen de `items.id` (cascade). Igual para especializaciones de operaciones.

## Casos de Uso Principales
1. Planificación de producción/abastecimiento: definir periodos, registrar plan y pronóstico por UEB e item.
2. Gestión de demanda: capturar demanda por cliente y periodo; comparar contra plan y pronóstico.
3. Control de inventarios: consultar, ajustar mediante operaciones (traslados, ventas, elaboraciones, producciones).
4. Elaboración con recetas: consumir componentes (productos o grupos) y generar producto final según receta.
5. Producción directa: registrar output sin receta (producciones).
6. Traslados: mover stock entre UEBs (origen/destino) con impactos en inventarios y detalle de items.
7. Ventas: reducir inventario y vincular al cliente para análisis de cumplimiento de demanda.
8. Análisis: diferencias plan vs pronóstico vs demanda vs ventas; eficiencia de recetas (consumo real vs teórico) y rotación de inventario.
9. Jerarquía de catálogo: planificar a nivel grupo y desglosar a productos; recetar componentes genéricos (grupos) para flexibilidad.
10. Seguridad y permisos: roles y accesos para delimitar CRUD de planificación, inventarios y operaciones.

## Ejemplos de Consultas Útiles
- Cumplimiento de plan: SUM(operacion_items.cantidad WHERE rol='producido') vs planificaciones.plan.
- Desviación pronóstico: demandas.cantidad - planificaciones.pronostico.
- Consumo por receta: SUM(operacion_items.cantidad WHERE rol='consumo' AND operacion.tipo='elaboracion').
- Rotación inventario: ventas de producto / inventario promedio periodo.

## Posibles Extensiones Futuras
- Versionado de recetas.
- Estados de operación (borrador, confirmado, contabilizado).
- Auditoría (created_by / updated_by / deleted_at con soft deletes selectivos).
- Métricas de coste estándar vs real.
- KPI de accuracy (MAPE) entre pronóstico y demanda real.

## Notas de Implementación
- Validar en aplicación la coherencia `items.tipo` antes de insertar en tablas especializadas.
- Considerar triggers para automatizar inserciones en tablas CTI (opcional según motor).
- Índices adicionales según patrones de consulta (ej. índices sobre (ueb_id, periodo_id) en planificaciones ya cubiertos parcialmente por UNIQUE).

