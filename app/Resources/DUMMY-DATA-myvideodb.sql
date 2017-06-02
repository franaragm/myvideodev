# ************************************************************
# Sequel Pro SQL dump
# Versión 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.5.42)
# Base de datos: myvideodb
# Tiempo de Generación: 2017-06-02 10:44:05 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Volcado de tabla mv_user
# ------------------------------------------------------------

LOCK TABLES `mv_user` WRITE;
/*!40000 ALTER TABLE `mv_user` DISABLE KEYS */;

INSERT INTO `mv_user` (`id`, `role`, `user_identifier`, `email`, `name`, `surname`, `password`, `nick`, `description`, `active`, `image_profile`, `image_banner`, `created_at`)
VALUES
	(4,'ROLE_USER','1d16787b0fb7094d9a077a47e8751d66edc03326','juan@lopez.com','juan','lopez','f6ccb3e8d609012238c0b39e60b2c9632b3cdede91e035dad1de43469768f4cc','juanlopez','Me encanta subir videos de gatitos','1',NULL,NULL,'2017-02-25 16:05:25'),
	(5,'ROLE_USER','4eba9fc3e936753f4f75b429e11793d2688f7692','fran@aragon.com','Fran','Aragon','6487cd4b9c7bef8e1b25608d4b833726299c8e4fb59713ee9d21ead1e1958865','franaragm','Pio Pio','1','4eba9fc3e936753f4f75b429e11793d2688f7692_imgprofile_1488140521.jpeg','4eba9fc3e936753f4f75b429e11793d2688f7692_imgbanner_1488140521.jpeg','2017-02-25 16:07:40'),
	(6,'ROLE_USER','9e7a458a8011a82e942860e8625c57a2c18e4e1a','david@aragon.com','David','Aragon','0f14089313b20c1723ec1d660b0aaa4f473cf5b321cd370f2d48b7bcf9a7b234','dvdaragm','Usuario David de la app','1',NULL,NULL,'2017-02-25 16:30:02');

/*!40000 ALTER TABLE `mv_user` ENABLE KEYS */;
UNLOCK TABLES;


# Volcado de tabla mv_video
# ------------------------------------------------------------

LOCK TABLES `mv_video` WRITE;
/*!40000 ALTER TABLE `mv_video` DISABLE KEYS */;

INSERT INTO `mv_video` (`id`, `user`, `video_identifier`, `title`, `description`, `video_source`, `video_image`, `status`, `created_at`, `updated_at`)
VALUES
	(1,5,'cdf3510463819275de9af7006f78777cdacdca97','Mi primer api vlog','Video de symfony','cdf3510463819275de9af7006f78777cdacdca97_vid_1491301392.mp4','cdf3510463819275de9af7006f78777cdacdca97_imgvid_1491301447.png',NULL,'2017-04-03 13:55:04','2017-04-03 15:29:02'),
	(2,5,'df56c175e2f90599e3e675b232e21df4de44355c','Mi segundo vlog sobre angular',NULL,NULL,NULL,NULL,'2017-04-11 12:27:01','2017-04-11 12:27:01');

/*!40000 ALTER TABLE `mv_video` ENABLE KEYS */;
UNLOCK TABLES;



# Volcado de tabla mv_comment
# ------------------------------------------------------------

LOCK TABLES `mv_comment` WRITE;
/*!40000 ALTER TABLE `mv_comment` DISABLE KEYS */;

INSERT INTO `mv_comment` (`id`, `user`, `video`, `body`, `created_at`)
VALUES
	(2,5,2,'otro comentario para este video','2017-04-12 16:18:19');

/*!40000 ALTER TABLE `mv_comment` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
