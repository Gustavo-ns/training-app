# 👤 Sistema de Usuarios - Training App

## 🚀 ¿Cómo se crea tu usuario?

### **Automático al abrir la app:**
Tu usuario se crea **automáticamente** la primera vez que abres la aplicación. No necesitas registrarte ni hacer nada especial.

## 🔢 Tipos de ID de Usuario

### **1. Usuario Personalizado (Recomendado)**
- **Cómo crear**: Usa el campo "Personalizar ID" en la parte inferior de la app
- **Ejemplo**: `juan-2025`, `maria-fitness`, `pedro_gym`
- **Ventajas**: 
  - Fácil de recordar
  - Puedes elegir tu nombre
  - Carpeta identificable: `data/users/juan-2025/`

### **2. Usuario Firebase** 
- **Se crea**: Cuando Firebase está configurado
- **Formato**: `AbCd3fGhIjKlMnOpQrStUv` (aleatorio)
- **Ventajas**: Único y seguro

### **3. Usuario Local Generado**
- **Se crea**: Cuando Firebase NO está disponible
- **Formato**: `user-abc12def`
- **Ventajas**: Funciona sin conexión

## 📁 ¿Dónde se guardan tus datos?

### **Estructura por usuario:**
```
C:\MAMP\htdocs\training-app\data\users\[TU-ID]\
├── progress/
│   └── current_progress.json      ← Tu progreso actual
├── history/
│   ├── 2025-10-15.json           ← Rutina de hoy
│   ├── 2025-10-14.json           ← Rutina de ayer
│   └── ...
└── backups/
    └── progress_backup_*.json     ← Copias de seguridad
```

### **Ejemplos reales:**
- **ID personalizado**: `data/users/juan-2025/`
- **ID Firebase**: `data/users/AbCd3fGhIjKlMnOpQrStUv/`
- **ID local**: `data/users/user-abc12def/`

## 🛠️ Cómo Personalizar tu Usuario

### **Paso 1: Encuentra el campo**
En la parte inferior de la app, verás:
- "ID de Usuario: [tu-id-actual]"
- Campo de texto: "Personalizar ID"
- Botón: "💾 Cambiar ID"

### **Paso 2: Elige tu ID**
**Reglas para el ID:**
- ✅ Entre 3 y 20 caracteres
- ✅ Solo letras, números, guiones (-) y guiones bajos (_)
- ✅ Ejemplos válidos: `juan2025`, `maria-fitness`, `pedro_gym`
- ❌ No válidos: `ju`, `usuario con espacios`, `user@gmail`

### **Paso 3: Cambiar ID**
1. Escribe tu ID deseado
2. Haz clic en "💾 Cambiar ID"
3. Confirma el cambio
4. ¡Listo! Tus nuevos datos se guardarán en la nueva carpeta

## ⚠️ Importante sobre Cambio de ID

### **¿Qué pasa con mis datos anteriores?**
- **Se conservan**: Tus datos anteriores NO se eliminan
- **Nueva carpeta**: Se crea una nueva carpeta con tu nuevo ID
- **Empiezas limpio**: El nuevo ID comienza sin historial

### **¿Cómo recuperar datos anteriores?**
1. **Opción 1**: Cambia temporalmente al ID anterior para descargar datos
2. **Opción 2**: Navega a la carpeta anterior y copia los archivos JSON
3. **Opción 3**: Usa "📤 Importar JSON" para restaurar datos descargados

## 🔍 Cómo Ver tu ID Actual

### **En la aplicación:**
- Parte inferior: "ID de Usuario: [tu-id]"
- Tipo se muestra: "(personalizado)", "(Firebase)", "(local)"

### **En tu computadora:**
- Abre: `C:\MAMP\htdocs\training-app\data\users\`
- Verás carpetas con nombres de usuarios
- Tu carpeta activa es la de tu ID actual

### **En la consola del navegador:**
- Presiona F12
- En Console verás: "✓ Usuario autenticado con ID: [tu-id]"

## 🚀 Recomendaciones

### **Para uso personal:**
```
ID recomendado: tu-nombre-2025
Ejemplo: juan-2025, maria-fitness, pedro-gym
```

### **Para familia:**
```
IDs familiares: nombre-familia-2025
Ejemplo: papa-garcia-2025, mama-garcia-2025, hijo-garcia-2025
```

### **Para compartir computadora:**
```
IDs descriptivos: nombre-deporte-año
Ejemplo: ana-crossfit-2025, luis-cardio-2025
```

## 🔄 Migración de Datos

### **Si quieres cambiar de ID pero conservar datos:**

1. **Con tu ID actual**: Haz clic en "📥 Descargar JSON"
2. **Cambia al nuevo ID**: Usa "💾 Cambiar ID"
3. **Importa tus datos**: Usa "📤 Importar JSON"
4. **¡Listo!**: Todos tus datos están en el nuevo ID

## 🛡️ Privacidad y Seguridad

- **Datos locales**: Todo se guarda en tu computadora
- **Sin registro**: No hay cuentas ni passwords
- **Control total**: Puedes ver, copiar, respaldar todos tus archivos
- **Separación**: Cada usuario tiene su carpeta independiente
- **Sin conexión**: Funciona completamente offline

¡Tu sistema de usuario está diseñado para ser simple, seguro y completamente bajo tu control! 💪