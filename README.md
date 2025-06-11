# Reservas Autocaravanas - Proyecto Laravel

## Descripción

Este proyecto es una **aplicación web de reservas de autocaravanas** desarrollada en Laravel. Permite a usuarios registrarse, reservar vehículos, gestionar sus reservas y realizar pagos de depósito mediante Stripe. Incluye un **panel de administración** para gestionar usuarios, vehículos y reservas, así como una **API RESTful** para integración con aplicaciones externas o móviles.

---

## Tabla de Contenidos

- [Requisitos](#requisitos)
- [Configuración](#configuración)
- [Migraciones y Seeders](#migraciones-y-seeders)
- [Uso de la Aplicación Web (Cliente)](#uso-de-la-aplicación-web-cliente)
- [Panel de Administración](#panel-de-administración)
- [API RESTful (App móvil o integración externa)](#api-restful-app-móvil-o-integración-externa)
- [Autenticación y Seguridad](#autenticación-y-seguridad)
- [Pagos con Stripe](#pagos-con-stripe)
- [Estructura de Rutas](#estructura-de-rutas)


---

## Requisitos

- PHP >= 8.1
- Composer
- MySQL
- Node.js y npm
- Extensiones PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo
- Servidor web (Apache/Nginx)
- Cuenta Stripe para pagos

---


1. **Clona el repositorio y descomprime el proyecto:**
   ```bash
   unzip laravel.zip
   cd laravel
   ```

2. **Instala dependencias:**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Copia el archivo de entorno y configura:**
   ```bash
   cp .env.example .env
   # O usa el .env proporcionado
   ```

4. **Genera la clave de la aplicación:**
   ```bash
   php artisan key:generate
   ```

---

## Configuración

- Edita el archivo `.env` con tus datos de base de datos, correo y Stripe.
- Ejemplo de configuración relevante:
  ```
  DB_DATABASE=reservas_autocaravanas
  DB_USERNAME=laravel_user
  DB_PASSWORD=******
  STRIPE_KEY=pk_test_xxx
  STRIPE_SECRET=sk_test_xxx
  ```

---

## Migraciones

1. **Ejecuta las migraciones:**
   ```bash
   php artisan migrate
   ```



## Uso de la Aplicación Web (Cliente)

### **Registro y Login**
- Los usuarios pueden registrarse y loguearse desde `/register` y `/login`.

### **Reservar una Autocaravana**
1. Accede a `/vehiculos` para ver los vehículos disponibles.
2. Haz clic en "Reservar" para acceder al formulario.
3. Selecciona fechas y vehículo, revisa el resumen y confirma.
4. Realiza el pago del depósito mediante Stripe.
5. Consulta tus reservas en `/reservas`.

### **Gestión de Reservas**
- Puedes ver, editar o cancelar tus reservas desde tu panel.
- Solo puedes tener un máximo de 5 reservas futuras activas.
- Restricciones de fechas: mínimo 2 días (julio/agosto mínimo 7 días).
- Se podra reserva con una antelacion de hasta 60 dias.

---

## Panel de Administración

### **Acceso**
- Solo usuarios con rol de administrador pueden acceder a `/admin`.

### **Funciones**
- **Dashboard:** Resumen de actividad.
- **Gestión de usuarios:** Ver, editar, eliminar usuarios.
- **Gestión de vehículos:** Crear, editar, eliminar vehículos.
- **Gestión de reservas:** Confirmar, editar, eliminar reservas de cualquier usuario.
- **Historial:** Ver historial de reservas.

---

## API RESTful (App móvil o integración externa)

### **Autenticación**
- Basada en tokens con Sanctum.
- Endpoints de login y registro:  
  - `POST /api/login`
  - `POST /api/register`

### **Endpoints principales**
- **Reservas:**  
  - `GET /api/reservas`  
  - `POST /api/reservas`  
  - `GET /api/reservas/{id}`  
  - `PUT /api/reservas/{id}`  
  - `DELETE /api/reservas/{id}`  
  - `POST /api/reservas/{id}/pagar`
- **Vehículos:**  
  - `GET /api/vehiculos`  
  - `GET /api/vehiculos/disponibles`
- **Usuario:**  
  - `GET /api/user`  
  - `GET /api/user/profile`

### **Notas**
- Los nombres de las rutas API son únicos (`api.reservas.store`, etc.) para evitar conflictos con las rutas web.
- Todas las rutas API requieren autenticación por token (`auth:sanctum`).

---

## Autenticación y Seguridad

- **Usuarios web:** Sesión tradicional (cookies).
- **API:** Tokens con Sanctum.
- **Protección CSRF:** Todos los formularios web usan `@csrf`.
- **Roles:** Usuario y Administrador.

---

## Pagos con Stripe

- El pago del depósito se realiza mediante Stripe Checkout.
- Tras el pago, la reserva pasa a estado "confirmada".
- Si el pago se cancela, la reserva se elimina automáticamente.
- Llegara un Correo al gmail vinculado Con los detalles de la reserva

---



## Estructura de Rutas

- **Web:**  
  - `/` Página principal
  - `/vehiculos` Listado de vehículos
  - `/reservas` Gestión de reservas del usuario
  - `/dashboard` Panel de usuario
  - `/admin` Panel de administración

- **API:**  
  - `/api/reservas` CRUD de reservas 
  - `/api/vehiculos` Listado de vehículos 
  - `/api/user` Perfil de usuario 

---

