# FinanzasInteligentes

Sistema de gestión financiera personal desarrollado en PHP con SQL Server.

## Características

- ✅ **Autenticación segura** con encriptación de contraseñas
- ✅ **Registro de usuarios** con validaciones
- ✅ **Gestión de activos** (CRUD completo)
- ✅ **Interfaz responsiva** y moderna
- ✅ **Validaciones de entrada** robustas
- ✅ **Manejo de errores** mejorado
- ✅ **Código optimizado** sin duplicaciones

## Estructura del Proyecto

```
finanzasInteligentes/
├── config/
│   ├── config.php      # Configuración centralizada
│   ├── db.php          # Conexión a base de datos
│   └── functions.php   # Funciones de utilidad
├── controllers/
│   ├── login.php       # Controlador de login
│   ├── register.php    # Controlador de registro
│   ├── logout.php      # Controlador de logout
│   └── Activos.php     # Controlador de activos
├── views/
│   ├── usuario.php     # Dashboard principal
│   ├── Activos.php     # Página de activos
│   └── master/
│       ├── header.php  # Header común
│       └── footer.php  # Footer común
├── css/
│   ├── style.css       # Estilos principales
│   ├── header.css      # Estilos del header
│   ├── user.css        # Estilos del dashboard
│   └── activos.css     # Estilos de activos
├── js/
│   └── activos.js      # JavaScript para activos
└── index.html          # Página de login/registro
```

## Mejoras Implementadas

### 1. **Autenticación Mejorada**
- Validación de sesiones en todas las páginas
- Logout funcional
- Registro con inicio de sesión automático

### 2. **Validaciones Robustas**
- Validación de email
- Validación de contraseñas
- Sanitización de entradas
- Validaciones en el frontend y backend

### 3. **Código Optimizado**
- Eliminación de código duplicado
- Funciones de utilidad centralizadas
- Configuración centralizada
- Manejo de errores mejorado

### 4. **Seguridad**
- Encriptación de contraseñas con bcrypt
- Validación de autenticación en todas las páginas
- Sanitización de datos de entrada
- Protección contra inyección SQL

### 5. **Experiencia de Usuario**
- Mensajes de error claros
- Notificaciones de éxito/error
- Interfaz responsiva
- Navegación intuitiva

## Instalación

1. **Configurar la base de datos SQL Server**
   - Crear base de datos `FinanzasInt`
   - Configurar usuario y contraseña en `config/config.php`

2. **Configurar el servidor web**
   - Colocar el proyecto en el directorio web
   - Asegurar que PHP tenga acceso a SQL Server

3. **Crear las tablas necesarias**
   ```sql
   -- Tabla de usuarios
   CREATE TABLE users (
       id INT IDENTITY(1,1) PRIMARY KEY,
       Name NVARCHAR(50) NOT NULL,
       LastName NVARCHAR(50) NOT NULL,
       email NVARCHAR(100) UNIQUE NOT NULL,
       password NVARCHAR(255) NOT NULL,
       created_date DATETIME DEFAULT GETDATE()
   );

   -- Tabla de activos
   CREATE TABLE assets (
       asset_id INT IDENTITY(1,1) PRIMARY KEY,
       user_id INT NOT NULL,
       name NVARCHAR(100) NOT NULL,
       balance DECIMAL(15,2) NOT NULL,
       created_date DATETIME DEFAULT GETDATE(),
       FOREIGN KEY (user_id) REFERENCES users(id)
   );
   ```

## Uso

1. **Acceder al sistema**: Navegar a `index.html`
2. **Registrarse**: Crear una nueva cuenta
3. **Iniciar sesión**: Usar credenciales existentes
4. **Gestionar activos**: Usar la página de activos para agregar, editar o eliminar activos

## Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de datos**: SQL Server
- **Frontend**: HTML5, CSS3, JavaScript
- **Iconos**: Bootstrap Icons
- **Fuentes**: Google Fonts (Poppins)

## Notas de Desarrollo

- El sistema está optimizado para funcionar con XAMPP
- Todas las validaciones están implementadas tanto en frontend como backend
- El código sigue buenas prácticas de seguridad
- La interfaz es completamente responsiva
- Manejo correcto de fechas de SQL Server (objetos DateTime)

## Solución de Problemas

### Error de Fechas
Si encuentras el error `strtotime(): Argument #1 ($datetime) must be of type string, DateTime given`, esto indica que SQL Server está devolviendo objetos DateTime. El sistema ya incluye la función `formatSqlServerDate()` para manejar este problema automáticamente.

### Pruebas
Puedes ejecutar `test_connection.php` para verificar que la conexión y el manejo de fechas funcionan correctamente.
