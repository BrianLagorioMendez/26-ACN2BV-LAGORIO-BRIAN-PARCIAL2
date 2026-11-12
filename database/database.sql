DROP TABLE IF EXISTS `comentarios`;
DROP TABLE IF EXISTS `publicaciones`;
DROP TABLE IF EXISTS `usuarios`;

-- 1. Tabla de Usuarios (Con soporte de Roles RBAC: admin / user)
CREATE TABLE `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin', 'user') DEFAULT 'user',
  `fecha_registro` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabla de Publicaciones
CREATE TABLE `publicaciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `categoria` VARCHAR(50) NOT NULL,
  `contenido` TEXT NOT NULL,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla de Comentarios
CREATE TABLE `comentarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `publicacion_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  `comentario` TEXT NOT NULL,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuarios Reales Exportados (ID 1 = Admin, IDs 2-5 = Usuarios Estándar)
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `fecha_registro`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$t5ZYeFPLAZrtOU6QDeDskuLUOdErOgbePTpfh7Py2yV/c0gXXyO82', 'admin', '2026-11-11 19:50:30'),
(2, 'Gonza_89', 'gonza_cardozo@gmail.com', '$2y$10$GDuzSykQ5RFP1NkpwF47UuMjVOI.Gw2UPblsjV6OWzFYc1e4aoGLS', 'user', '2026-11-11 18:40:21'),
(3, 'juanma_capito', 'juanmacappi@gmail.com', '$2y$10$pekfR4hECtQBL5vGitVJyeQJz0uSNXajNuMJ1svdWoX/JrJiLwaPK', 'user', '2026-11-11 19:28:11'),
(4, 'pelado_bicampeon', 'peladorosarino@gmail.com', '$2y$10$7Ebg1Pn8ufOT9z1rNc5VBOIyRM1kPyduGlagVOGoIcfxo84dX3u1C', 'user', '2026-11-11 20:08:51'),
(5, 'Pibe_Janson', 'pibejanson@gmail.com', '$2y$10$0aMe4jFAL3Q7oPRueWfuYe71yUD.dCD0Sgnhv1eum2Y0EGZ3gVIGS', 'user', '2026-11-11 20:09:25');

-- Publicaciones de Prueba (Creadas por los usuarios 2, 3, 4 y 5)
INSERT INTO `publicaciones` (`id`, `usuario_id`, `titulo`, `categoria`, `contenido`) VALUES
(1, 2, 'No entendí la parte del ojo de Torben Wöller jaja', 'Misterios', 'Buenas gente! Recién termino de ver la tercera temporada por segunda vez y me sigue quedando la duda... ¿alguien entendió al final qué le pasó a Wöller en el ojo? En un momento iba a contarlo y justo lo interrumpen. ¿Hubo alguna explicación oficial o quedó como un chiste de los directores?'),
(2, 3, '¿Vale la pena volver a verla con el árbol genealógico al lado?', 'Teorías', 'Hola a todos! Terminé la serie el año pasado y la verdad me voló la cabeza pero me entreveré bastante en la temporada 2 con las líneas temporales. Estoy pensando en arrancar una re-revisión pero usando la página oficial del mapa familiar para no perderme. ¿A alguien le sirvió o te saca un poco la magia?'),
(3, 4, 'El papel de Claudia en el capítulo final me pareció sublime', 'Ciencia', 'Buenas! Qué pedazo de serie por favor. Quería comentar sobre el episodio final. Me pareció genial cómo Claudia fue la única en darse cuenta de que existía un tercer mundo (el mundo original). ¿Cómo descubrió ella la falla cuando ni Adam ni Eva lo lograron en miles de ciclos?'),
(4, 5, 'Buscando series similares a Dark, ¿qué me recomiendan?', 'Investigación', 'Buenas gente del foro! Me quedé con un vacío enorme después de terminar el último capítulo. Me encantan los viajes en el tiempo bien pensados y sin agujeros de guion. ¿Alguna serie o película que esté a la altura? Ya vi 12 Monkeys y Steins;Gate.');

-- Comentarios de Prueba (Interacción cruzada entre los usuarios)
INSERT INTO `comentarios` (`publicacion_id`, `usuario_id`, `comentario`) VALUES
(1, 3, 'Jaja es un chiste interno de los creadores (Baran bo Odar). En una entrevista dijeron que inventaron una historia re compleja pero prefirieron dejarlo como un misterio que nunca se resuelve.'),
(1, 5, 'Totalmente! En la escena del mundo original casi lo cuenta de nuevo y vuelve a pasar algo. Es excelente ese toque de humor entre tanto drama.'),
(2, 4, 'Recomendadísimo tener la guía a mano. Yo usé la web oficial de Netflix donde seleccionás por qué capítulo vas para no comerte spoilers y me cambió 100% la experiencia.'),
(3, 2, 'Para mí Claudia lo logra porque fue la única que no actuó por amor ciego ni por dolor como Adam o Eva, sino por lógica pura para salvar a Regina.'),
(4, 3, 'Te recomiendo bastante "Severance" de Apple TV o la película "Predestination" con Ethan Hawke. No son exactamente igual pero tienen esa vibra de rompecabezas.');