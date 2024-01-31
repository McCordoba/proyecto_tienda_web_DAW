CREATE DATABASE Tienda;

USE Tienda;

CREATE TABLE Restaurantes (
    codRest INT AUTO_INCREMENT, 
    correo VARCHAR(90) UNIQUE NOT NULL,
    clave VARCHAR(50) NOT NULL,
    pais VARCHAR(50),
    CP INT,
    ciudad VARCHAR(50),
    direccion VARCHAR(200),
    PRIMARY KEY (codRest)
);

CREATE TABLE Pedidos (
    codPed INT AUTO_INCREMENT, 
    fecha DATE,
    enviado BOOLEAN,
    codRest INT NOT NULL,
    PRIMARY KEY (codPed),
    FOREIGN KEY (codRest) REFERENCES Restaurantes (codRest)
);


CREATE TABLE Categorias (
    codCat INT AUTO_INCREMENT, 
    nombre VARCHAR(50) UNIQUE,
    descripcion VARCHAR(200),
    PRIMARY KEY (codCat)
);


CREATE TABLE Productos (
    codProd INT AUTO_INCREMENT, 
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(90),
    peso FLOAT,
    stock INT  NOT NULL,
    codCat INT NOT NULL,
    PRIMARY KEY (codProd),
    FOREIGN KEY (codCat) REFERENCES Categorias (codCat)
);

CREATE TABLE PedidosProductos (
    codPedProd INT AUTO_INCREMENT,
    codPed INT NOT NULL,
    codProd INT NOT NULL,
    unidades INT NOT NULL,
    PRIMARY KEY (codPedProd),
    FOREIGN KEY (codPed) REFERENCES Pedidos (codPed),
    FOREIGN KEY (codProd) REFERENCES Productos (codProd)
);

-- Datos para las tablas
INSERT INTO Restaurantes (correo, clave, pais, CP, ciudad, direccion) 
VALUES
('restaurante1@gmail.com', '12345', 'España', 28001, 'Malaga', 'Calle Falsa, 1234');

INSERT INTO Restaurantes (correo, clave, pais, CP, ciudad, direccion) 
VALUES
('mccorgut@gmail.com', '123456', 'España', 29751, 'Malaga', 'Caleta de valez, 1234');


-- Los pedidos se introducen en la base de datos como no enviados.
INSERT INTO Pedidos (fecha, enviado, codRest) 
VALUES
('2023-11-01', 0, 1);

INSERT INTO Categorias (nombre, descripcion)
VALUES 
('Entrantes', 'Aperitivos y snacks'),
('Carnes', 'Productos carnicos'),
('Pescados', 'Pescados frescos'),
('Frutas y Verduras', 'Productos de temporada'),
('Bebidas', 'Bebidas sin alcohol'),
('Bebidas alcoholicas', 'Bebidas alcoholicas'),
('Postres', 'Postres y dulces');

INSERT INTO Productos (nombre, descripcion, peso, stock, codCat)
VALUES 
('Patatas bravas', 'Bolsa de 500g', 0.5, 100, 1),
('Aceitunas', '5Kg por paquete', 0.3, 80, 1),
('Hamburguesas de ternera', '2Kg por paquete', 0.5, 30, 2),
('Costillas de cerdo', '20Kg por paquete', 0.6, 40, 2),
('Salmón fresco', 'Pieza completa de 10Kg', 0.75, 50, 3),
('Tomates', 'Caja de 1Kg de tomates de temporada', 0.2, 500, 4),
('Agua mineral', '6 botellas de 1L', 0.5, 200, 5),
('Cocacola', '24 latas de 33cl', 0.25, 100, 5),
('Vino tinto', '6 botellas de 0,75cl', 0.75, 50, 6),
('Cerveza Victoria', '24 botellas de 33cl', 0.4, 60, 6),
('Tiramisú', 'Postre italiano de café', 0.3, 30, 7),
('Brownie de chocolate', 'Brownie de chocolate con helado', 0.35, 40, 7);

INSERT INTO Productos (nombre, descripcion, peso, stock, codCat)
VALUES 
('Aguacates', 'Caja de 1Kg ', 1, 0, 4);

INSERT INTO PedidosProductos (codPed, codProd, unidades)
VALUES 
(1, 1, 15);


