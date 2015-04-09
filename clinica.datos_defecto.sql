--
-- Dumping data for table `rol`
--

INSERT INTO `rol` (`id`, `nombre`, `descripcion`) VALUES
(1, 'doctor', NULL),
(2, 'recepcionista', NULL),
(3, 'paciente', NULL);

--
-- Dumping data for table `tipo_contacto`
--

INSERT INTO `tipo_contacto` (`id`, `tipo`) VALUES
(1, 'phone'),
(2, 'email');

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `password`, `contrasena_tmp`, `activo`, `admin`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$SPk9qASck2p7d87YGfLY1eNcJNUUzASa/gvjiIz/wdPqbg7zNgVsi', NULL, 1, 1, 'a5HI6FerRZ4uJGCODKuT3CILdmHtg5KnXuXywrO8r4i1B0JIOA1y9qZDOAlt', '2014-09-07 15:41:41', '2015-04-05 23:07:35');