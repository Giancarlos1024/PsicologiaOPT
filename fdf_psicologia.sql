-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 08-07-2024 a las 21:34:31
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS fdf_psicologia;

-- Seleccionar la base de datos para usar
USE fdf_psicologia;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla pacientes
CREATE TABLE pacientes (
  id_paciente INT NOT NULL AUTO_INCREMENT,
  dni VARCHAR(15) NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  apellidos VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  telefono VARCHAR(20),
  direccion VARCHAR(255),
  fecha_nacimiento DATE,
  estado_civil VARCHAR(50),
  ocupacion VARCHAR(100),
  numero_hijos INT,
  grado_instruccion VARCHAR(100),
  PRIMARY KEY (id_paciente),
  UNIQUE KEY dni (dni),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Estructura de tabla para la tabla usuarios
CREATE TABLE usuarios (
  id_usuario INT NOT NULL AUTO_INCREMENT,
  dni VARCHAR(15) NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  apellidos VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  contrasena VARCHAR(100) NOT NULL,
  rol VARCHAR(50) NOT NULL,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY dni (dni),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Estructura de tabla para la tabla citas
CREATE TABLE citas (
  id_cita INT NOT NULL AUTO_INCREMENT,
  dni_paciente VARCHAR(15) NOT NULL,
  fecha_cita DATE NOT NULL,
  hora_cita TIME NOT NULL,
  dni_psicologo VARCHAR(15) NOT NULL,
  estado VARCHAR(50) NOT NULL,
  notas TEXT,
  hora_finalizacion TIME,
  PRIMARY KEY (id_cita),
  KEY dni_paciente (dni_paciente),
  KEY dni_psicologo (dni_psicologo),
  CONSTRAINT citas_fk_paciente FOREIGN KEY (dni_paciente) REFERENCES pacientes (dni),
  CONSTRAINT citas_fk_psicologo FOREIGN KEY (dni_psicologo) REFERENCES usuarios (dni)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Estructura de tabla para la tabla historias_clinicas
CREATE TABLE historias_clinicas (
  id_historia INT NOT NULL AUTO_INCREMENT,
  dni_paciente VARCHAR(15) NOT NULL,
  motivo_consulta TEXT NOT NULL,
  antecedentes TEXT NOT NULL,
  diagnostico_probable TEXT NOT NULL,
  tratamiento TEXT NOT NULL,
  pdf_datos VARCHAR(255),
  fecha_cita DATE NOT NULL,
  hora_cita TIME NOT NULL,
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_historia),
  KEY dni_paciente (dni_paciente),
  CONSTRAINT historias_clinicas_fk_paciente FOREIGN KEY (dni_paciente) REFERENCES pacientes (dni)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Estructura de tabla para la tabla pruebas_aplicadas
CREATE TABLE pruebas_aplicadas (
  id_prueba INT NOT NULL AUTO_INCREMENT,
  dni_paciente VARCHAR(15) NOT NULL,
  nombre_prueba VARCHAR(255) NOT NULL,
  resultado TEXT NOT NULL,
  foto_prueba VARCHAR(255),
  fecha_cita DATE NOT NULL,
  hora_cita TIME NOT NULL,
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_prueba),
  KEY dni_paciente (dni_paciente),
  CONSTRAINT pruebas_aplicadas_fk_paciente FOREIGN KEY (dni_paciente) REFERENCES pacientes (dni)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Volcado de datos para la tabla pacientes
INSERT INTO pacientes (dni, nombre, apellidos, email, telefono, direccion, fecha_nacimiento, estado_civil, ocupacion, numero_hijos, grado_instruccion) VALUES
('03648125', 'Juan Francisco', 'Camacho Ordinola', 'juan@gmail.com', '958745878', 'Calle moquegua 832', '1975-04-25', 'Soltero', 'Ingeniero', 2, 'Universitario'),
('75462308', 'Luisa Maria', 'Leon Villegas', 'luisamarialeon7@hotmail.com', '932866689', 'Calle ancash 530', '1999-11-28', 'Soltera', 'Estudiante', 0, 'Secundaria'),
('03647012', 'Elizabeth', 'Villegas Aguilar', 'eli@gmail.com', '925305069', 'Calle ancash 530', '1968-10-24', 'Casada', 'Docente', 3, 'Superior'),
('74185296', 'Francisco', 'Torres Fermin', 'francisco@gmail.com', '978456145', 'Calle Moquegua 828', '1968-04-14', 'Divorciado', 'Comerciante', 1, 'Primaria');

-- Volcado de datos para la tabla usuarios
INSERT INTO usuarios (dni, nombre, apellidos, email, contrasena, rol) VALUES
('72022204', 'Teresa', 'Agurto Camacho', 'teresa@gmail.com', '12345678', 'psicologo'),
('03647156', 'Juana', 'Carrasco Morales', 'juana@gmail.com', '1234567', 'psicologo'),
('03678945', 'Macarena', 'Lopez Albujar', 'macarena@gmail.com', '1234567', 'psicologo'),
('03612345', 'Juan', 'Camacho Espinoza', 'juan@gmail.com', '1234567', 'psicologo');

-- Volcado de datos para la tabla citas
INSERT INTO citas (dni_paciente, fecha_cita, hora_cita, dni_psicologo, estado, notas) VALUES
('75462308', '2024-07-10', '18:30:00', '03612345', 'programada', 'Urgente'),
('03648125', '2024-07-09', '19:30:00', '03678945', 'programada', 'Urgente'),
('74185296', '2024-07-09', '18:30:00', '03678945', 'programada', 'Urgente'),
('74185296', '2024-07-09', '18:31:00', '03678945', 'programada', 'Urgente'),
('74185296', '2024-07-09', '18:31:00', '03647156', 'programada', 'Urgente'),
('74185296', '2024-07-09', '18:36:00', '03678945', 'programada', 'Urgente'),
('03648125', '2024-07-10', '18:30:00', '03678945', 'programada', 'csa');

-- Volcado de datos para la tabla historias_clinicas
INSERT INTO historias_clinicas (dni_paciente, motivo_consulta, antecedentes, diagnostico_probable,tratamiento, pdf_datos, fecha_cita, hora_cita) VALUES
('03648125', 'sss', 'ddd', 'sdsds','delicado', '000054-24.pdf', '2024-07-09', '19:30:00'),
('74185296', 'asc', 'sac', 'asc','saludable', '005-21.pdf', '2024-07-09', '18:30:00');

-- Volcado de datos para la tabla pruebas_aplicadas
INSERT INTO pruebas_aplicadas (dni_paciente, nombre_prueba, resultado, foto_prueba, fecha_cita, hora_cita) VALUES
('03648125', 'ef', 'wef', '', '2024-07-09', '19:30:00'),
('75462308', 'Nombre de la Prueba', 'Resultado', '', '2024-07-10', '18:30:00');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


SELECT * FROM usuarios;
SELECT * FROM pacientes;
SELECT * FROM citas;
SELECT * FROM historias_clinicas;
SELECT * FROM pruebas_aplicadas;

-- elminar la clave foreana actual
ALTER TABLE historias_clinicas
DROP FOREIGN KEY historias_clinicas_fk_paciente;
-- ejecutar esta para la clave foreana actualizada
ALTER TABLE historias_clinicas
ADD CONSTRAINT historias_clinicas_fk_paciente
FOREIGN KEY (dni_paciente) REFERENCES pacientes(dni) ON DELETE CASCADE;

-- Eliminar la clave foránea existente
ALTER TABLE pruebas_aplicadas
DROP FOREIGN KEY pruebas_aplicadas_fk_paciente;

-- Agregar la clave foránea con la opción de eliminación en cascada
ALTER TABLE pruebas_aplicadas
ADD CONSTRAINT pruebas_aplicadas_fk_paciente
FOREIGN KEY (dni_paciente) REFERENCES pacientes(dni) ON DELETE CASCADE;



