# Menu Lateral Responsive

Plugin de WordPress que agrega un menú lateral (sidebar) responsive, compatible con Elementor y disponible como shortcode.

## Requisitos

- WordPress 5.6+
- PHP 7.4+
- Elementor (opcional, para el widget)

## Instalación

1. Descarga o clona este repositorio en `/wp-content/plugins/menu-lateral-responsive/`
2. Activa el plugin desde **Plugins** en el panel de WordPress
3. Ve a **Apariencia > Menús** y asigna un menú a la ubicación "Menú Lateral Responsive"
4. Configura las opciones en **Menu Lateral** en el panel de administración

## Uso

### Shortcode

Inserta el menú en cualquier página o entrada:

```
[menu_lateral]
```

Con parámetros personalizados:

```
[menu_lateral position="left" width="300" theme="dark" menu="mi-menu"]
```

**Parámetros disponibles:**

| Parámetro  | Valores         | Default | Descripción              |
|------------|-----------------|---------|--------------------------|
| position   | left, right     | left    | Posición del sidebar     |
| width      | 200-600         | 300     | Ancho en píxeles         |
| theme      | dark            | dark    | Tema visual              |
| menu       | slug del menú   | (vacío) | Menú específico a usar   |
| show_logo  | true, false     | false   | Mostrar logo en el header|

### Widget de Elementor

1. Edita una página con Elementor
2. Busca "Menu Lateral Responsive" en el panel de widgets
3. Arrastra el widget a tu diseño
4. Configura posición, colores y menú desde los controles de Elementor

## Configuración

Desde el panel de administración (**Menu Lateral**):

- **Posición**: Izquierda o derecha
- **Ancho**: 200px a 600px
- **Colores**: Fondo, texto, hover, acento
- **Overlay**: Color y comportamiento al hacer click
- **Logo**: Opción de mostrar un logo en la parte superior

## Estructura de archivos

```
menu-lateral-responsive/
├── menu-lateral-responsive.php     # Archivo principal del plugin
├── uninstall.php                   # Limpieza al desinstalar
├── includes/
│   ├── class-mlr-activator.php     # Activación/desactivación
│   ├── class-mlr-shortcode.php     # Registro y render del shortcode
│   └── class-mlr-walker-nav-menu.php # Walker personalizado del menú
├── admin/
│   ├── class-mlr-admin.php         # Página de administración
│   ├── css/mlr-admin.css           # Estilos del admin
│   └── js/mlr-admin.js             # Scripts del admin
├── assets/
│   ├── css/mlr-styles.css          # Estilos del frontend
│   └── js/mlr-scripts.js           # Scripts del frontend
├── elementor/
│   ├── class-mlr-elementor.php     # Integración con Elementor
│   └── class-mlr-elementor-widget.php # Widget de Elementor
└── languages/                      # Archivos de traducción
```

## Características

- Menu lateral responsive con animación suave
- Compatible con Elementor (widget nativo)
- Shortcode configurable
- Soporte de submenús con toggle expandible (hasta 3 niveles)
- Navegación por teclado (Escape para cerrar, Tab trap)
- Accesibilidad (ARIA labels, roles, focus management)
- Personalización completa de colores desde el admin
- Overlay configurable
- Logo opcional en el header del menú
- Animación hamburger a X en el botón toggle
- Diseño responsive (desktop, tablet, mobile)
- Soporte para `prefers-reduced-motion`
- Limpieza completa al desinstalar
