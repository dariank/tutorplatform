-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Окт 21 2026 г., 10:20
-- Версия сервера: 8.0.30
-- Версия PHP: 8.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `tutor_platform`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint UNSIGNED NOT NULL,
  `slot_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `tutor_id` bigint UNSIGNED NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `bookings`
--

INSERT INTO `bookings` (`id`, `slot_id`, `student_id`, `tutor_id`, `status`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 17, 2, 'completed', NULL, '2026-10-19 18:54:54', '2026-10-19 18:54:54'),
(2, 6, 18, 3, 'completed', NULL, '2026-10-19 18:54:54', '2026-10-19 18:54:54'),
(3, 11, 19, 4, 'completed', NULL, '2026-10-19 18:54:54', '2026-10-19 18:54:54'),
(4, 16, 20, 5, 'completed', NULL, '2026-10-19 18:54:54', '2026-10-19 18:54:54'),
(24, 3, 17, 2, 'confirmed', NULL, '2026-10-19 19:31:53', '2026-10-19 19:31:53'),
(26, 12, 17, 4, 'confirmed', NULL, '2026-10-19 19:44:29', '2026-10-20 08:40:57'),
(27, 2, 17, 2, 'confirmed', NULL, '2026-10-20 08:25:57', '2026-10-20 08:41:03'),
(28, 4, 17, 10, 'confirmed', NULL, '2026-10-20 08:27:59', '2026-10-20 08:46:32');

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE `favorites` (
  `student_id` bigint UNSIGNED NOT NULL,
  `tutor_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `favorites`
--

INSERT INTO `favorites` (`student_id`, `tutor_id`, `created_at`) VALUES
(17, 2, '2026-10-19 09:43:20'),
(17, 3, '2026-10-19 09:43:20'),
(17, 4, '2026-10-19 09:43:20'),
(17, 6, '2026-10-19 09:43:20'),
(18, 2, '2026-10-19 09:43:20'),
(18, 3, '2026-10-19 09:43:20'),
(18, 5, '2026-10-19 09:43:20'),
(19, 2, '2026-10-19 09:43:20'),
(19, 6, '2026-10-19 09:43:20'),
(19, 8, '2026-10-19 09:43:20'),
(20, 3, '2026-10-19 09:43:20'),
(20, 5, '2026-10-19 09:43:20'),
(20, 9, '2026-10-19 09:43:20'),
(21, 3, '2026-10-19 09:43:20'),
(21, 7, '2026-10-19 09:43:20'),
(22, 4, '2026-10-19 09:43:20'),
(22, 6, '2026-10-19 09:43:20'),
(23, 4, '2026-10-19 09:43:20'),
(23, 5, '2026-10-19 09:43:20'),
(24, 5, '2026-10-19 09:43:20'),
(24, 11, '2026-10-19 09:43:20'),
(25, 6, '2026-10-19 09:43:20'),
(25, 10, '2026-10-19 09:43:20'),
(26, 2, '2026-10-19 09:43:20'),
(26, 6, '2026-10-19 09:43:20'),
(27, 3, '2026-10-19 09:43:20'),
(27, 7, '2026-10-19 09:43:20'),
(28, 7, '2026-10-19 09:43:20'),
(28, 9, '2026-10-19 09:43:20'),
(29, 3, '2026-10-19 09:43:20'),
(29, 9, '2026-10-19 09:43:20'),
(30, 2, '2026-10-19 09:43:20'),
(30, 10, '2026-10-19 09:43:20');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `booking_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `tutor_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci,
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `booking_id`, `student_id`, `tutor_id`, `rating`, `text`, `is_hidden`, `created_at`) VALUES
(25, 1, 17, 2, 5, 'Отличный репетитор! Всё понятно объясняет, ЕГЭ сдал на 92 балла. Очень рекомендую всем, кто готовится к экзаменам.', 0, '2026-10-19 18:54:54'),
(26, 2, 18, 3, 5, 'Замечательный преподаватель! Занимаемся уже полгода, английский стал намного лучше. Очень доступно объясняет сложные темы.', 0, '2026-10-19 18:54:54'),
(27, 3, 19, 4, 4, 'Хороший специалист, помог разобраться с физикой. Иногда бывает строгим, но это даже помогает собраться.', 0, '2026-10-19 18:54:54'),
(28, 4, 20, 5, 5, 'Прекрасный репетитор по химии! Подготовил к ОГЭ на отлично. Занятия проходят интересно и продуктивно.', 0, '2026-10-19 18:54:54');

-- --------------------------------------------------------

--
-- Структура таблицы `slots`
--

CREATE TABLE `slots` (
  `id` bigint UNSIGNED NOT NULL,
  `tutor_id` bigint UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `is_booked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Дамп данных таблицы `slots`
--

INSERT INTO `slots` (`id`, `tutor_id`, `start_time`, `end_time`, `is_booked`, `created_at`) VALUES
(1, 2, '2026-10-20 10:00:00', '2026-10-20 11:00:00', 0, '2026-10-19 09:42:36'),
(2, 2, '2026-10-20 12:00:00', '2026-10-20 13:00:00', 1, '2026-10-19 09:42:36'),
(3, 2, '2026-10-21 15:00:00', '2026-10-21 16:00:00', 0, '2026-10-19 09:42:36'),
(4, 10, '2026-10-22 10:00:00', '2026-10-22 11:00:00', 1, '2026-10-19 09:42:36'),
(5, 2, '2026-10-23 18:00:00', '2026-10-23 19:00:00', 0, '2026-10-19 09:42:36'),
(6, 3, '2026-10-20 09:00:00', '2026-10-20 10:00:00', 0, '2026-10-19 09:42:36'),
(7, 3, '2026-10-21 14:00:00', '2026-10-21 15:00:00', 0, '2026-10-19 09:42:36'),
(8, 3, '2026-10-22 16:00:00', '2026-10-22 17:00:00', 0, '2026-10-19 09:42:36'),
(9, 3, '2026-10-24 11:00:00', '2026-10-24 12:00:00', 0, '2026-10-19 09:42:36'),
(10, 3, '2026-10-25 19:00:00', '2026-10-25 20:00:00', 0, '2026-10-19 09:42:36'),
(11, 4, '2026-10-20 17:00:00', '2026-10-20 18:00:00', 0, '2026-10-19 09:42:36'),
(12, 4, '2026-10-21 10:00:00', '2026-10-21 11:00:00', 1, '2026-10-19 09:42:36'),
(13, 4, '2026-10-22 13:00:00', '2026-10-22 14:00:00', 0, '2026-10-19 09:42:36'),
(14, 4, '2026-10-23 15:00:00', '2026-10-23 16:00:00', 0, '2026-10-19 09:42:36'),
(15, 4, '2026-10-24 18:00:00', '2026-10-24 19:00:00', 0, '2026-10-19 09:42:36'),
(16, 5, '2026-10-21 11:00:00', '2026-10-21 12:00:00', 0, '2026-10-19 09:42:36'),
(17, 5, '2026-10-22 12:00:00', '2026-10-22 13:00:00', 0, '2026-10-19 09:42:36'),
(18, 5, '2026-10-23 14:00:00', '2026-10-23 15:00:00', 0, '2026-10-19 09:42:36'),
(19, 5, '2026-10-24 16:00:00', '2026-10-24 17:00:00', 0, '2026-10-19 09:42:36'),
(20, 5, '2026-10-25 10:00:00', '2026-10-25 11:00:00', 0, '2026-10-19 09:42:36'),
(21, 6, '2026-10-20 13:00:00', '2026-10-20 14:00:00', 0, '2026-10-19 09:42:36'),
(22, 6, '2026-10-21 17:00:00', '2026-10-21 18:00:00', 0, '2026-10-19 09:42:36'),
(23, 6, '2026-10-22 19:00:00', '2026-10-22 20:00:00', 0, '2026-10-19 09:42:36'),
(24, 6, '2026-10-24 12:00:00', '2026-10-24 13:00:00', 0, '2026-10-19 09:42:36'),
(25, 6, '2026-10-25 15:00:00', '2026-10-25 16:00:00', 0, '2026-10-19 09:42:36'),
(26, 7, '2026-10-20 11:00:00', '2026-10-20 12:00:00', 0, '2026-10-19 09:42:36'),
(27, 7, '2026-10-21 16:00:00', '2026-10-21 17:00:00', 0, '2026-10-19 09:42:36'),
(28, 7, '2026-10-23 10:00:00', '2026-10-23 11:00:00', 0, '2026-10-19 09:42:36'),
(29, 7, '2026-10-24 14:00:00', '2026-10-24 15:00:00', 0, '2026-10-19 09:42:36'),
(30, 7, '2026-10-25 18:00:00', '2026-10-25 19:00:00', 0, '2026-10-19 09:42:36'),
(31, 8, '2026-10-20 15:00:00', '2026-10-20 16:00:00', 0, '2026-10-19 09:42:36'),
(32, 8, '2026-10-22 11:00:00', '2026-10-22 12:00:00', 0, '2026-10-19 09:42:36'),
(33, 8, '2026-10-23 13:00:00', '2026-10-23 14:00:00', 0, '2026-10-19 09:42:36'),
(34, 8, '2026-10-24 17:00:00', '2026-10-24 18:00:00', 0, '2026-10-19 09:42:36'),
(35, 8, '2026-10-26 10:00:00', '2026-10-26 11:00:00', 0, '2026-10-19 09:42:36'),
(36, 9, '2026-10-20 16:00:00', '2026-10-20 17:00:00', 0, '2026-10-19 09:42:36'),
(37, 9, '2026-10-21 12:00:00', '2026-10-21 13:00:00', 0, '2026-10-19 09:42:36'),
(38, 9, '2026-10-22 15:00:00', '2026-10-22 16:00:00', 0, '2026-10-19 09:42:36'),
(39, 9, '2026-10-24 10:00:00', '2026-10-24 11:00:00', 0, '2026-10-19 09:42:36'),
(40, 9, '2026-10-25 17:00:00', '2026-10-25 18:00:00', 0, '2026-10-19 09:42:36'),
(41, 10, '2026-10-20 18:00:00', '2026-10-20 19:00:00', 0, '2026-10-19 09:42:36'),
(42, 10, '2026-10-21 13:00:00', '2026-10-21 14:00:00', 0, '2026-10-19 09:42:36'),
(43, 10, '2026-10-23 16:00:00', '2026-10-23 17:00:00', 0, '2026-10-19 09:42:36'),
(44, 10, '2026-10-24 19:00:00', '2026-10-24 20:00:00', 0, '2026-10-19 09:42:36'),
(45, 10, '2026-10-26 11:00:00', '2026-10-26 12:00:00', 0, '2026-10-19 09:42:36'),
(46, 11, '2026-10-21 18:00:00', '2026-10-21 19:00:00', 0, '2026-10-19 09:42:36'),
(47, 11, '2026-10-22 14:00:00', '2026-10-22 15:00:00', 0, '2026-10-19 09:42:36'),
(48, 11, '2026-10-23 11:00:00', '2026-10-23 12:00:00', 0, '2026-10-19 09:42:36'),
(49, 11, '2026-10-25 13:00:00', '2026-10-25 14:00:00', 0, '2026-10-19 09:42:36'),
(50, 11, '2026-10-26 15:00:00', '2026-10-26 16:00:00', 0, '2026-10-19 09:42:36'),
(51, 4, '2026-10-20 11:40:00', '2026-10-20 12:40:00', 0, '2026-10-20 08:47:13'),
(53, 32, '2026-10-20 12:55:00', '2026-10-20 13:55:00', 0, '2026-10-20 08:53:59');

-- --------------------------------------------------------

--
-- Структура таблицы `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `slug`, `icon_url`) VALUES
(1, 'Математика', 'math', NULL),
(2, 'Английский язык', 'english', NULL),
(3, 'Физика', 'physics', NULL),
(4, 'Химия', 'chemistry', NULL),
(5, 'Информатика', 'it', NULL),
(6, 'Русский язык', 'russian', NULL),
(7, 'История', 'history', NULL),
(8, 'Биология', 'biology', NULL),
(9, 'Обществознание', 'social', NULL),
(10, 'Литература', 'literature', NULL),
(11, 'География', 'geography', NULL),
(12, 'Немецкий язык', 'german', NULL),
(13, 'Французский язык', 'french', NULL),
(14, 'Испанский язык', 'spanish', NULL),
(15, 'Экономика', 'economics', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `tutor_profiles`
--

CREATE TABLE `tutor_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `experience_years` int UNSIGNED NOT NULL DEFAULT '0',
  `price_per_hour` decimal(10,2) NOT NULL DEFAULT '0.00',
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `education` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `rating_avg` decimal(3,2) NOT NULL DEFAULT '0.00',
  `reviews_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `tutor_profiles`
--

INSERT INTO `tutor_profiles` (`id`, `user_id`, `bio`, `experience_years`, `price_per_hour`, `city`, `education`, `is_approved`, `rating_avg`, `reviews_count`, `created_at`, `updated_at`) VALUES
(1, 2, 'Закончил МГУ с красным дипломом. Готовлю к ЕГЭ и ОГЭ по математике и физике. За время обучения разработал собственную методику, которая помогает даже гуманитариям сдать профильную математику на 80+ баллов. Легко нахожу общий язык с подростками.', 7, '1500.00', 'Москва', 'МГУ, мехмат', 1, '4.67', 3, '2026-10-19 09:42:36', '2026-10-19 18:46:32'),
(2, 3, 'Преподаю английский язык уже более 5 лет. Работаю с уровнями от Beginner до Advanced. Готовлю к международным экзаменам IELTS и TOEFL. На своих занятиях делаю упор на разговорную практику, чтобы вы не боялись говорить.', 5, '1200.00', 'Санкт-Петербург', 'СПбГУ, филфак', 1, '4.33', 3, '2026-10-19 09:42:36', '2026-10-19 18:46:32'),
(3, 4, 'Выпускник МФТИ. Объясняю сложные вещи простым языком. Помогаю разобраться в термодинамике, электродинамике и механике. Мои ученики успешно поступают в технические вузы Москвы.', 9, '5000.00', 'Москва', 'МФТИ, ФФКЭ', 1, '5.00', 2, '2026-10-19 09:42:36', '2026-10-20 08:47:20'),
(4, 5, 'Химия и биология, подготовка к ОГЭ/ЕГЭ, олимпиадам.', 6, '1400.00', 'Казань', 'КФУ, химфак', 1, '4.00', 1, '2026-10-19 09:42:36', '2026-10-19 09:42:36'),
(5, 6, 'Программирование на Python и Java, основы алгоритмов, подготовка к олимпиадам.', 8, '2000.00', 'Москва', 'МГТУ им. Баумана', 1, '4.67', 3, '2026-10-19 09:42:36', '2026-10-19 09:42:36'),
(6, 7, 'Русский язык и литература, сочинения, изложения, ЕГЭ.', 11, '1300.00', 'Екатеринбург', 'УрФУ, филфак', 1, '4.50', 2, '2026-10-19 09:42:36', '2026-10-19 09:42:36'),
(7, 8, 'История и обществознание, подготовка к экзаменам, разбор эссе.', 4, '1100.00', 'Новосибирск', 'НГУ, гумфак', 1, '0.00', 0, '2026-10-19 09:42:36', '2026-10-19 09:42:36'),
(8, 9, 'Английский для детей и взрослых, игровой формат для младших.', 3, '900.00', 'Санкт-Петербург', 'РГПУ им. Герцена', 1, '0.00', 0, '2026-10-19 09:42:36', '2026-10-19 09:42:36'),
(9, 10, 'Математика, информатика, подготовка к ОГЭ. Работаю онлайн.', 5, '1250.00', 'Москва', 'НИУ ВШЭ, ФКН', 0, '0.00', 0, '2026-10-19 09:42:36', '2026-10-19 09:42:36'),
(10, 11, 'Биология и химия, медицинские вузы, подготовка к ЕГЭ.', 10, '1600.00', 'Ростов-на-Дону', 'РостГМУ', 1, '0.00', 0, '2026-10-19 09:42:36', '2026-10-19 09:42:36'),
(11, 12, 'Немецкий язык с нуля, разговорная практика, Goethe-Zertifikat.', 6, '1350.00', 'Калининград', 'БФУ им. Канта', 1, '0.00', 0, '2026-10-19 09:42:36', '2026-10-19 09:42:36'),
(12, 13, 'Французский язык, подготовка к DELF, помощь со школьной программой.', 4, '1150.00', 'Москва', 'МГЛУ', 1, '0.00', 0, '2026-10-19 09:42:36', '2026-10-19 09:42:36'),
(13, 14, 'Испанский язык, разговорный и грамматика, для путешествий.', 5, '1200.00', 'Санкт-Петербург', 'СПбГУ, иберо-романская', 1, '0.00', 0, '2026-10-19 09:42:36', '2026-10-19 09:42:36'),
(14, 15, 'Экономика, обществознание, подготовка к олимпиадам и ЕГЭ.', 7, '1700.00', 'Москва', 'НИУ ВШЭ, экономфак', 1, '0.00', 0, '2026-10-19 09:42:36', '2026-10-19 09:42:36'),
(15, 16, 'География и биология, подготовка к ЕГЭ, работа с картами.', 3, '1000.00', 'Краснодар', 'КубГУ, геофак', 1, '0.00', 0, '2026-10-19 09:42:36', '2026-10-19 09:42:36'),
(16, 32, 'Хороший специалист', 6, '1000.00', 'Нижний Новгород', 'МФТИ, ФФКЭ', 1, '0.00', 0, '2026-10-20 08:49:54', '2026-10-20 08:49:54');

-- --------------------------------------------------------

--
-- Структура таблицы `tutor_subjects`
--

CREATE TABLE `tutor_subjects` (
  `tutor_profile_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `tutor_subjects`
--

INSERT INTO `tutor_subjects` (`tutor_profile_id`, `subject_id`) VALUES
(1, 1),
(3, 1),
(5, 1),
(9, 1),
(2, 2),
(8, 2),
(11, 2),
(1, 3),
(3, 3),
(4, 4),
(10, 4),
(5, 5),
(9, 5),
(2, 6),
(6, 6),
(7, 7),
(4, 8),
(10, 8),
(15, 8),
(7, 9),
(14, 9),
(6, 10),
(15, 11),
(12, 13),
(13, 14),
(14, 15);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('student','tutor','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `role`, `full_name`, `phone`, `avatar_url`, `is_blocked`, `created_at`, `updated_at`) VALUES

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_bookings_slot` (`slot_id`),
  ADD KEY `idx_bookings_student` (`student_id`),
  ADD KEY `idx_bookings_tutor` (`tutor_id`),
  ADD KEY `idx_bookings_status` (`status`);

--
-- Индексы таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`student_id`,`tutor_id`),
  ADD KEY `idx_favorites_tutor` (`tutor_id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reviews_booking` (`booking_id`),
  ADD KEY `idx_reviews_tutor` (`tutor_id`),
  ADD KEY `idx_reviews_rating` (`rating`),
  ADD KEY `fk_reviews_student` (`student_id`);

--
-- Индексы таблицы `slots`
--
ALTER TABLE `slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_slots_tutor_time` (`tutor_id`,`start_time`),
  ADD KEY `idx_slots_booked` (`is_booked`);

--
-- Индексы таблицы `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_subjects_name` (`name`),
  ADD UNIQUE KEY `uq_subjects_slug` (`slug`);

--
-- Индексы таблицы `tutor_profiles`
--
ALTER TABLE `tutor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tutor_profiles_user` (`user_id`),
  ADD KEY `idx_tutor_profiles_approved` (`is_approved`),
  ADD KEY `idx_tutor_profiles_city` (`city`),
  ADD KEY `idx_tutor_profiles_price` (`price_per_hour`),
  ADD KEY `idx_tutor_profiles_rating` (`rating_avg`);

--
-- Индексы таблицы `tutor_subjects`
--
ALTER TABLE `tutor_subjects`
  ADD PRIMARY KEY (`tutor_profile_id`,`subject_id`),
  ADD KEY `idx_tutor_subjects_subject` (`subject_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `slots`
--
ALTER TABLE `slots`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `tutor_profiles`
--
ALTER TABLE `tutor_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_slot` FOREIGN KEY (`slot_id`) REFERENCES `slots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_fav_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fav_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `slots`
--
ALTER TABLE `slots`
  ADD CONSTRAINT `fk_slots_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `tutor_profiles`
--
ALTER TABLE `tutor_profiles`
  ADD CONSTRAINT `fk_tutor_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `tutor_subjects`
--
ALTER TABLE `tutor_subjects`
  ADD CONSTRAINT `fk_ts_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ts_tutor` FOREIGN KEY (`tutor_profile_id`) REFERENCES `tutor_profiles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
