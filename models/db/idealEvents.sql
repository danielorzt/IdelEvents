-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS sistema_eventos;
USE sistema_eventos;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuario (
                                       id_usuario INT AUTO_INCREMENT PRIMARY KEY,
                                       tipo_documento ENUM('Cédula', 'Tarjeta de Identidad') NOT NULL,
    documento VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    genero ENUM('Masculino', 'Femenino') NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') NOT NULL DEFAULT 'cliente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de eventos
CREATE TABLE IF NOT EXISTS evento (
                                      id_evento INT PRIMARY KEY AUTO_INCREMENT,
                                      titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    ubicacion VARCHAR(200) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    imagen_nombre VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado_por INT,
    FOREIGN KEY (creado_por) REFERENCES usuario(id_usuario)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de pagos
CREATE TABLE IF NOT EXISTS pago (
                                    id_pago INT PRIMARY KEY AUTO_INCREMENT,
                                    id_usuario INT NOT NULL,
                                    id_evento INT NOT NULL,
                                    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    monto DECIMAL(10,2) NOT NULL,
    estado_pago ENUM('pendiente', 'completado', 'rechazado') DEFAULT 'pendiente',
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_evento) REFERENCES evento(id_evento)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de inscripciones
CREATE TABLE IF NOT EXISTS inscripcion (
                                           id_inscripcion INT PRIMARY KEY AUTO_INCREMENT,
                                           id_usuario INT NOT NULL,
                                           id_evento INT NOT NULL,
                                           fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                           FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_evento) REFERENCES evento(id_evento),
    UNIQUE KEY unique_usuario_evento (id_usuario, id_evento)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos iniciales
-- Usuario administrador (password: admin123)
INSERT INTO usuario (
    tipo_documento, documento, nombre, apellido,
    fecha_nacimiento, genero, email, password, rol
) VALUES (
             'Cédula', '123456789', 'Admin', 'Sistema',
             '1990-01-01', 'Masculino', 'admin@example.com',
             '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'
         );

-- Evento de ejemplo
INSERT INTO evento (
    titulo, descripcion, fecha, hora, ubicacion,
    categoria, precio, creado_por
) VALUES (
             'Concierto de Prueba', 'Este es un evento de prueba para el sistema',
             '2023-12-15', '20:00:00', 'Teatro Principal',
             'Concierto', 25.50, 1
         );