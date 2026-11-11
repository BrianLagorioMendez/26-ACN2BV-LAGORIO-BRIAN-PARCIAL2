CREATE DATABASE IF NOT EXISTS `parcial_dark` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `parcial_dark`;

DROP TABLE IF EXISTS `comentarios`;
DROP TABLE IF EXISTS `publicaciones`;
DROP TABLE IF EXISTS `usuarios`;

-- 1. Tabla de Usuarios (Cuentas de fans / usuarios reales)
CREATE TABLE `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fecha_registro` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabla de Publicaciones del Foro (CRUD Principal)
CREATE TABLE `publicaciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `categoria` VARCHAR(50) NOT NULL,
  `contenido` TEXT NOT NULL,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla de Comentarios (Interacción entre usuarios del foro)
CREATE TABLE `comentarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `publicacion_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  `comentario` TEXT NOT NULL,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuarios de Prueba (Contraseña de todos: 123456 - Almacenada con password_hash)
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`) VALUES
(1, 'Gonzalo_89', 'gonzalo@gmail.com', '$2y$10$44iYc4n/iQo/Sj.BqK/qOO3/zZpLw7v2O0oV8uPqM5/eK9/Nn.x7a'),
(2, 'Sofi_gomez', 'sofi.gomez@hotmail.com', '$2y$10$44iYc4n/iQo/Sj.BqK/qOO3/zZpLw7v2O0oV8uPqM5/eK9/Nn.x7a'),
(3, 'Nico_Marplatense', 'nico_mkt@yahoo.com', '$2y$10$44iYc4n/iQo/Sj.BqK/qOO3/zZpLw7v2O0oV8uPqM5/eK9/Nn.x7a'),
(4, 'Matias_Dev', 'matias.programacion@gmail.com', '$2y$10$44iYc4n/iQo/Sj.BqK/qOO3/zZpLw7v2O0oV8uPqM5/eK9/Nn.x7a'),
(5, 'Camila_rios', 'cami_2001@outlook.com', '$2y$10$44iYc4n/iQo/Sj.BqK/qOO3/zZpLw7v2O0oV8uPqM5/eK9/Nn.x7a'),
(6, 'Lucas_Series', 'lucas_fan@gmail.com', '$2y$10$44iYc4n/iQo/Sj.BqK/qOO3/zZpLw7v2O0oV8uPqM5/eK9/Nn.x7a');

-- Publicaciones de Prueba
INSERT INTO `publicaciones` (`id`, `usuario_id`, `titulo`, `categoria`, `contenido`) VALUES
(1, 1, 'No entendí la parte del ojo de Torben Wöller jaja', 'Misterios', 'Buenas gente! Recién termino de ver la tercera temporada por segunda vez y me sigue quedando la duda... ¿alguien entendió al final qué le pasó a Wöller en el ojo? En un momento iba a contarlo y justo lo interrumpen. ¿Hubo alguna explicación oficial o quedó como un chiste de los directores?'),
(2, 2, '¿Vale la pena volver a verla con el árbol genealógico al lado?', 'Teorías', 'Hola a todos! Terminé la serie el año pasado y la verdad me voló la cabeza pero me entreveré bastante en la temporada 2 con las líneas temporales. Estoy pensando en arrancar una re-revisión pero usando la página oficial del mapa familiar para no perderme. ¿A alguien le sirvió o te saca un poco la magia?'),
(3, 3, 'El papel de Claudia en el capítulo final me pareció sublime', 'Ciencia', 'Buenas! Qué pedazo de serie por favor. Quería comentar sobre el episodio final. Me pareció genial cómo Claudia fue la única en darse cuenta de que existía un tercer mundo (el mundo original). ¿Cómo descubrió ella la falla cuando ni Adam ni Eva lo lograron en miles de ciclos?'),
(4, 4, 'Buscando series similares a Dark, ¿qué me recomiendan?', 'Investigación', 'Buenas gente del foro! Me quedé con un vacío enorme después de terminar el último capítulo. Me encantan los viajes en el tiempo bien pensados y sin agujeros de guion. ¿Alguna serie o película que esté a la altura? Ya vi 12 Monkeys y Steins;Gate.');

-- Comentarios de Prueba
INSERT INTO `comentarios` (`publicacion_id`, `usuario_id`, `comentario`) VALUES
(1, 2, 'Jaja es un chiste interno de los creadores (Baran bo Odar). En una entrevista dijeron que inventaron una historia re compleja pero prefirieron dejarlo como un misterio que nunca se resuelve.'),
(1, 5, 'Totalmente! En la escena del mundo original casi lo cuenta de nuevo y vuelve a pasar algo. Es excelente ese toque de humor entre tanto drama.'),
(2, 3, 'Recomendadísimo tener la guía a mano. Yo usé la web oficial de Netflix donde seleccionás por qué capítulo vas para no comerte spoilers y me cambió 100% la experiencia.'),
(3, 1, 'Para mí Claudia lo logra porque fue la única que no actuó por amor ciego ni por dolor como Adam o Eva, sino por lógica pura para salvar a Regina.'),
(4, 6, 'Te recomiendo bastante "Severance" de Apple TV o la película "Predestination" con Ethan Hawke. No son exactamente igual pero tienen esa vibra de rompecabezas.');