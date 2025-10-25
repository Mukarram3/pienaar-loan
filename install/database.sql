-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2024 at 04:09 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rapidlab`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `username`, `email_verified_at`, `image`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admins', 'admin@site.com', 'admin', NULL, '64883c94e5dbb1686650004.jpg', '$2y$12$t6vLFov.No8isEffaERhj.x8.ydTBM32SbINp68ygkqX5eMh9FrOW', 'b8l0rHAfBbPBFaOSqEqtEj40157cXb4NNcveUaQrnRk7CP6AQa3SLUU6h1bD', NULL, '2024-05-23 00:54:08');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `click_url` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_password_resets`
--

CREATE TABLE `admin_password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(40) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1-> ''Enable'', 0->''Disable''',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cron_jobs`
--

CREATE TABLE `cron_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `alias` varchar(40) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `cron_schedule_id` int(11) NOT NULL DEFAULT 0,
  `next_run` datetime DEFAULT NULL,
  `last_run` datetime DEFAULT NULL,
  `is_running` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cron_jobs`
--

INSERT INTO `cron_jobs` (`id`, `name`, `alias`, `action`, `url`, `cron_schedule_id`, `next_run`, `last_run`, `is_running`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Loan Cron', 'loan_cron', '[\"App\\\\Http\\\\Controllers\\\\CronController\", \"loan\"]', NULL, 1, '2024-05-23 09:26:48', '2024-05-23 09:21:48', 1, 1, '2023-06-21 23:29:14', '2024-05-23 03:21:48');

-- --------------------------------------------------------

--
-- Table structure for table `cron_job_logs`
--

CREATE TABLE `cron_job_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cron_job_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT 0,
  `error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cron_schedules`
--

CREATE TABLE `cron_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `interval` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cron_schedules`
--

INSERT INTO `cron_schedules` (`id`, `name`, `interval`, `status`, `created_at`, `updated_at`) VALUES
(1, '5 Minutes', 300, 1, '2023-07-22 10:03:29', '2023-07-22 10:03:29'),
(2, '10 Minutes', 600, 1, '2023-07-22 10:03:35', '2023-07-22 10:03:35');

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `method_code` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `method_currency` varchar(40) DEFAULT NULL,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `final_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `detail` text DEFAULT NULL,
  `btc_amount` varchar(255) DEFAULT NULL,
  `btc_wallet` varchar(255) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `payment_try` int(10) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>success, 2=>pending, 3=>cancel',
  `from_api` tinyint(1) NOT NULL DEFAULT 0,
  `admin_feedback` varchar(255) DEFAULT NULL,
  `success_url` varchar(255) DEFAULT NULL,
  `failed_url` varchar(255) DEFAULT NULL,
  `last_cron` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_tokens`
--

CREATE TABLE `device_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_app` tinyint(1) NOT NULL DEFAULT 0,
  `token` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `extensions`
--

CREATE TABLE `extensions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `act` varchar(40) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `script` text DEFAULT NULL,
  `shortcode` text DEFAULT NULL COMMENT 'object',
  `support` text DEFAULT NULL COMMENT 'help section',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `extensions`
--

INSERT INTO `extensions` (`id`, `act`, `name`, `description`, `image`, `script`, `shortcode`, `support`, `status`, `created_at`, `updated_at`) VALUES
(1, 'tawk-chat', 'Tawk.to', 'Key location is shown bellow', 'tawky_big.png', '<script>\r\n                        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();\r\n                        (function(){\r\n                        var s1=document.createElement(\"script\"),s0=document.getElementsByTagName(\"script\")[0];\r\n                        s1.async=true;\r\n                        s1.src=\"https://embed.tawk.to/{{app_key}}\";\r\n                        s1.charset=\"UTF-8\";\r\n                        s1.setAttribute(\"crossorigin\",\"*\");\r\n                        s0.parentNode.insertBefore(s1,s0);\r\n                        })();\r\n                    </script>', '{\"app_key\":{\"title\":\"App Key\",\"value\":\"------\"}}', 'twak.png', 0, '2019-10-18 23:16:05', '2022-03-22 05:22:24'),
(2, 'google-recaptcha2', 'Google Recaptcha 2', 'Key location is shown bellow', 'recaptcha3.png', '\n<script src=\"https://www.google.com/recaptcha/api.js\"></script>\n<div class=\"g-recaptcha\" data-sitekey=\"{{site_key}}\" data-callback=\"verifyCaptcha\"></div>\n<div id=\"g-recaptcha-error\"></div>', '{\"site_key\":{\"title\":\"Site Key\",\"value\":\"6LdPC88fAAAAADQlUf_DV6Hrvgm-pZuLJFSLDOWV\"},\"secret_key\":{\"title\":\"Secret Key\",\"value\":\"6LdPC88fAAAAAG5SVaRYDnV2NpCrptLg2XLYKRKB\"}}', 'recaptcha.png', 0, '2019-10-18 23:16:05', '2024-05-20 05:49:13'),
(3, 'custom-captcha', 'Custom Captcha', 'Just put any random string', 'customcaptcha.png', NULL, '{\"random_key\":{\"title\":\"Random String\",\"value\":\"SecureString\"}}', 'na', 0, '2019-10-18 23:16:05', '2023-08-05 09:08:21'),
(4, 'google-analytics', 'Google Analytics', 'Key location is shown bellow', 'google_analytics.png', '<script async src=\"https://www.googletagmanager.com/gtag/js?id={{measurement_id}}\"></script>\n                <script>\n                  window.dataLayer = window.dataLayer || [];\n                  function gtag(){dataLayer.push(arguments);}\n                  gtag(\"js\", new Date());\n                \n                  gtag(\"config\", \"{{measurement_id}}\");\n                </script>', '{\"measurement_id\":{\"title\":\"Measurement ID\",\"value\":\"------\"}}', 'ganalytics.png', 0, NULL, '2021-05-04 10:19:12'),
(5, 'fb-comment', 'Facebook Comment ', 'Key location is shown bellow', 'Facebook.png', '<div id=\"fb-root\"></div><script async defer crossorigin=\"anonymous\" src=\"https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v4.0&appId={{app_key}}&autoLogAppEvents=1\"></script>', '{\"app_key\":{\"title\":\"App Key\",\"value\":\"----\"}}', 'fb_com.png', 0, NULL, '2022-03-22 05:18:36');

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `act` varchar(40) DEFAULT NULL,
  `form_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `frontends`
--

CREATE TABLE `frontends` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `data_keys` varchar(40) DEFAULT NULL,
  `data_values` longtext DEFAULT NULL,
  `seo_content` longtext DEFAULT NULL,
  `tempname` varchar(40) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `frontends`
--

INSERT INTO `frontends` (`id`, `data_keys`, `data_values`, `seo_content`, `tempname`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'seo.data', '{\"seo_image\":\"1\",\"keywords\":[\"loan\",\"bank\",\"bank loan\",\"installment\",\"home loan\",\"lending\",\"money lending\"],\"description\":\"Introducing our Loan Management System, a comprehensive and efficient platform designed to streamline the borrowing process for administrators and borrowers alike. Built with a user-friendly interface, this application empowers administrators to create and manage customized loan plans effortlessly.\\r\\n\\r\\nAdministrators can set interest rates, repayment periods, and other parameters to cater to diverse borrower needs. The system ensures top-notch security with advanced encryption, protecting sensitive financial data and personal information.\",\"social_title\":\"RapidLab - Online Loan Management System\",\"social_description\":\"Introducing our Loan Management System, a comprehensive and efficient platform designed to streamline the borrowing process for administrators and borrowers alike. Built with a user-friendly interface, this application empowers administrators to create and manage customized loan plans effortlessly.\\r\\n\\r\\nAdministrators can set interest rates, repayment periods, and other parameters to cater to diverse borrower needs. The system ensures top-notch security with advanced encryption, protecting sensitive financial data and personal information.\",\"image\":\"664f1d9e837051716460958.jpg\"}', NULL, 'basic', '', '2020-07-04 23:42:52', '2024-05-23 04:42:38'),
(24, 'about.content', '{\"has_image\":\"1\",\"heading\":\"Empowering Financial Freedom and Trusted Solutions\",\"subheading\":\"About\",\"description\":\"<div>We are committed to helping you achieve your financial goals and dreams. Our loan services are designed to provide you with the support and resources you need to take control of your finances and embark on a path towards prosperity.<\\/div><div><br \\/><\\/div><div>Our experienced team of financial experts is dedicated to understanding your unique needs and finding the best loan solutions that align with your goals. Whether you\'re looking to fund a new business venture, consolidate debt, purchase a new home, or cover unexpected expenses, we\'ve got you covered.<\\/div>\",\"button_name\":\"About Us\",\"button_link\":\"about\",\"image\":\"64cb498c47a021691044236.png\"}', NULL, 'basic', NULL, '2020-10-28 00:51:20', '2023-08-06 10:14:13'),
(25, 'blog.content', '{\"heading\":\"Our Latest Blog\",\"subheading\":\"Blog Post\"}', NULL, 'basic', NULL, '2020-10-28 00:51:34', '2023-07-29 11:27:32'),
(26, 'blog.element', '{\"has_image\":[\"1\"],\"title\":\"USA mortgages: \\u2018How did a $42,500 loan turn into a $477,000 debt?\\u2019\",\"description_nic\":\"<p class=\\\"dcr-1kxe4av\\\" style=\\\"margin-right:0px;margin-bottom:16px;margin-left:0px;padding:0px;border:0px;font-size:1.0625rem;line-height:1.5;font-family:GuardianTextEgyptian, \'Guardian Text Egyptian Web\', Georgia, serif;vertical-align:baseline;color:rgb(18,18,18);\\\">Cooper\\u2019s parents died in 2021, and their house was last year valued at $750,000, so \\u2013 as things stand \\u2013 he and his sister will have to hand over most of that to the bank. He says he feels certain his late parents did not realise that that $42,500 loan could spiral to close to $500,000 and \\u201ccost their kids their inheritance\\u201d.<\\/p><p class=\\\"dcr-1kxe4av\\\" style=\\\"margin-right:0px;margin-bottom:16px;margin-left:0px;padding:0px;border:0px;font-size:1.0625rem;line-height:1.5;font-family:GuardianTextEgyptian, \'Guardian Text Egyptian Web\', Georgia, serif;vertical-align:baseline;color:rgb(18,18,18);\\\">However, the bank says it recommended at the time that customers took independent financial advice to ensure they understood the product and that it was right for them, and adds that in this case, solicitors were instructed by the borrowers.<\\/p><p class=\\\"dcr-1kxe4av\\\" style=\\\"margin-right:0px;margin-bottom:16px;margin-left:0px;padding:0px;border:0px;font-size:1.0625rem;line-height:1.5;font-family:GuardianTextEgyptian, \'Guardian Text Egyptian Web\', Georgia, serif;vertical-align:baseline;color:rgb(18,18,18);\\\">The Coopers are among hundreds \\u2013 probably thousands \\u2013 of families whose lives have been blighted by shared appreciation mortgages (Sams). This was a type of home loan that was only on sale for a brief period, between 1996 and 1998, and only available from two banks, Bank of Scotland and Barclays.<\\/p><p class=\\\"dcr-1kxe4av\\\" style=\\\"margin-right:0px;margin-bottom:16px;margin-left:0px;padding:0px;border:0px;font-size:1.0625rem;line-height:1.5;font-family:GuardianTextEgyptian, \'Guardian Text Egyptian Web\', Georgia, serif;vertical-align:baseline;color:rgb(18,18,18);\\\">These loans were ostensibly aimed at helping \\u201casset-rich, cash-poor\\u201d older people release some of the value locked up in their homes. They typically allowed people to borrow up to 25% of the property\\u2019s value, and usually there were no repayments to make during the lifetime of the loan.<\\/p><p class=\\\"dcr-1kxe4av\\\" style=\\\"margin-right:0px;margin-bottom:16px;margin-left:0px;padding:0px;border:0px;font-size:1.0625rem;line-height:1.5;font-family:GuardianTextEgyptian, \'Guardian Text Egyptian Web\', Georgia, serif;vertical-align:baseline;color:rgb(18,18,18);\\\">In return, people were required to pay back the original amount when the mortgage was repaid, or when they died and the house was sold, plus a share of any increase in the value of their home.<\\/p><p class=\\\"dcr-1kxe4av\\\" style=\\\"margin-right:0px;margin-bottom:16px;margin-left:0px;padding:0px;border:0px;font-size:1.0625rem;line-height:1.5;font-family:GuardianTextEgyptian, \'Guardian Text Egyptian Web\', Georgia, serif;vertical-align:baseline;color:rgb(18,18,18);\\\">This share was usually worked out on a three-to-one basis \\u2013 so if you borrowed 25% of the value, you would be in line to hand over 75% of the future growth in value.<\\/p><p class=\\\"dcr-1kxe4av\\\" style=\\\"margin-right:0px;margin-bottom:16px;margin-left:0px;padding:0px;border:0px;font-size:1.0625rem;line-height:1.5;font-family:GuardianTextEgyptian, \'Guardian Text Egyptian Web\', Georgia, serif;vertical-align:baseline;color:rgb(18,18,18);\\\">Of course, in the years since those mortgages were sold, house prices have rocketed, leaving people facing massive repayments if they want to move \\u2013 or, as in the case of Cooper, leaving the offspring of those who signed up with a huge and costly headache.<\\/p>\",\"image\":\"64cf8674baddf1691321972.jpg\"}', NULL, 'basic', 'usa-mortgages:-‘how-did-a-$42500-loan-turn-into-a-$477000-debt?’', '2020-10-28 00:57:19', '2023-08-06 10:09:32'),
(27, 'contact_us.content', '{\"title\":\"Auctor gravida vestibulu\",\"short_details\":\"55f55\",\"email_address\":\"5555f\",\"contact_details\":\"5555h\",\"contact_number\":\"5555a\",\"latitude\":\"5555h\",\"longitude\":\"5555s\",\"website_footer\":\"5555qqq\"}', NULL, 'basic', NULL, '2020-10-28 00:59:19', '2020-11-01 04:51:54'),
(28, 'counter.content', '{\"has_image\":\"1\",\"image\":\"64cb500304a771691045891.png\"}', NULL, 'basic', NULL, '2020-10-28 01:04:02', '2023-08-03 04:28:11'),
(31, 'social_icon.element', '{\"title\":\"Facebook\",\"social_icon\":\"<i class=\\\"fab fa-facebook\\\"><\\/i>\",\"url\":\"https:\\/\\/www.facebook.com\\/\"}', NULL, 'basic', NULL, '2020-11-12 04:07:30', '2023-07-30 03:11:15'),
(33, 'feature.content', '{\"has_image\":\"1\",\"heading\":\"Unlocking the Power of Our Loan Features\",\"subheading\":\"Feature\",\"content\":\"<span style=\\\"color:rgb(78,78,78);font-family:Roboto, sans-serif;\\\">We believe in transparency, and that\'s why we offer competitive interest rates and flexible repayment options. Our user-friendly loan management platform makes it easy for you to monitor your loan status, make payments, and stay on top of your financial journey.<\\/span><br \\/><div><ol><li class=\\\"list__item list--column__item-sm\\\" style=\\\"margin-bottom:0.5rem;padding-left:20px;\\\">Flexible Repayment : Customize your loan with easy payment plans.<\\/li><li class=\\\"list__item list--column__item-sm\\\" style=\\\"margin-bottom:0.5rem;padding-left:20px;\\\">Low-Interest Rates: Enjoy competitive rates for affordable borrowing.<\\/li><li class=\\\"list__item list--column__item-sm\\\" style=\\\"margin-bottom:0.5rem;padding-left:20px;\\\">Quick Approval Process: Get funds swiftly with fast approvals.<\\/li><li class=\\\"list__item list--column__item-sm\\\" style=\\\"margin-bottom:0.5rem;padding-left:20px;\\\">No Hidden Fees: Transparent loan terms, no surprises or extras.<\\/li><li class=\\\"list__item list--column__item-sm\\\" style=\\\"padding-left:20px;\\\">Loan Assistance: Our team is here to guide and support you.<\\/li><\\/ol><\\/div>\",\"image\":\"64cb4da7b5efa1691045287.png\"}', NULL, 'basic', NULL, '2021-01-03 23:40:54', '2023-08-05 02:44:51'),
(35, 'service.element', '{\"trx_type\":\"withdraw\",\"service_icon\":\"<i class=\\\"las la-highlighter\\\"><\\/i>\",\"title\":\"asdfasdf\",\"description\":\"asdfasdfasdfasdf\"}', NULL, 'basic', NULL, '2021-03-06 01:12:10', '2021-03-06 01:12:10'),
(36, 'service.content', '{\"trx_type\":\"deposit\",\"heading\":\"asdf fffff\",\"subheading\":\"555\"}', NULL, 'basic', NULL, '2021-03-06 01:27:34', '2022-03-30 08:07:06'),
(39, 'banner.content', '{\"has_image\":\"1\",\"heading\":\"Empower your dreams with our flexible loan options!\",\"button_name\":\"Get Started\",\"button_link\":\"user\\/register\",\"image\":\"64cf71278f3bd1691316519.jpg\"}', NULL, 'basic', NULL, '2021-05-02 06:09:30', '2023-08-06 08:38:39'),
(41, 'cookie.data', '{\"short_desc\":\"We may use cookies or any other tracking technologies when you visit our website, including any other media form, mobile website, or mobile application related or connected to help customize the Site and improve your experience.\",\"description\":\"<h4>Cookie Policy<\\/h4>\\r\\n\\r\\n<p>This Cookie Policy explains how to use cookies and similar technologies to recognize you when you visit our website. It explains what these technologies are and why we use them, as well as your rights to control our use of them.<\\/p>\\r\\n<br>\\r\\n<h4>What are cookies?<\\/h4>\\r\\n\\r\\n<p>Cookies are small pieces of data stored on your computer or mobile device when you visit a website. Cookies are widely used by website owners to make their websites work, or to work more efficiently, as well as to provide reporting information.<\\/p>\\r\\n<br>\\r\\n<h4>Why do we use cookies?<\\/h4>\\r\\n\\r\\n<p>We use cookies for several reasons. Some cookies are required for technical reasons for our Website to operate, and we refer to these as \\\"essential\\\" or \\\"strictly necessary\\\" cookies. Other cookies enable us to track and target the interests of our users to enhance the experience on our Website. Third parties serve cookies through our Website for advertising, analytics, and other purposes.<\\/p>\\r\\n<br>\\r\\n<h4>What types of cookies do we use?<\\/h4>\\r\\n\\r\\n<div>\\r\\n    <ul style=\\\"list-style: unset;\\\">\\r\\n        <li>\\r\\n            <strong>Essential Website Cookies:<\\/strong> \\r\\n            These cookies are strictly necessary to provide you with services available through our Website and to use some of its features.\\r\\n        <\\/li>\\r\\n        <li>\\r\\n            <strong>Analytics and Performance Cookies:<\\/strong> \\r\\n            These cookies allow us to count visits and traffic sources to measure and improve our Website\'s performance.\\r\\n        <\\/li>\\r\\n        <li>\\r\\n            <strong>Advertising Cookies:<\\/strong> \\r\\n            These cookies make advertising messages more relevant to you and your interests. They perform functions like preventing the same ad from continuously reappearing, ensuring that ads are properly displayed, and in some cases selecting advertisements that are based on your interests.\\r\\n        <\\/li>\\r\\n    <\\/ul>\\r\\n<\\/div>\\r\\n<br>\\r\\n<h4>Data Collected by Cookies<\\/h4>\\r\\n<p>Cookies may collect various types of data, including but not limited to:<\\/p>\\r\\n<ul style=\\\"list-style: unset;\\\">\\r\\n    <li>IP addresses<\\/li>\\r\\n    <li>Browser and device information<\\/li>\\r\\n    <li>Referring website addresses<\\/li>\\r\\n    <li>Pages visited on our website<\\/li>\\r\\n    <li>Interactions with our website, such as clicks and mouse movements<\\/li>\\r\\n    <li>Time spent on our website<\\/li>\\r\\n<\\/ul>\\r\\n<br>\\r\\n<h4>How We Use Collected Data<\\/h4>\\r\\n\\r\\n<p>We may use data collected by cookies for the following purposes:<\\/p>\\r\\n<ul style=\\\"list-style: unset;\\\">\\r\\n    <li>To personalize your experience on our website<\\/li>\\r\\n    <li>To improve our website\'s functionality and performance<\\/li>\\r\\n    <li>To analyze trends and gather demographic information about our user base<\\/li>\\r\\n    <li>To deliver targeted advertising based on your interests<\\/li>\\r\\n    <li>To prevent fraudulent activity and enhance website security<\\/li>\\r\\n<\\/ul>\\r\\n<br>\\r\\n<h4>Third-party cookies<\\/h4>\\r\\n\\r\\n<p>In addition to our cookies, we may also use various third-party cookies to report usage statistics of our Website, deliver advertisements on and through our Website, and so on.<\\/p>\\r\\n<br>\\r\\n<h4>How can we control cookies?<\\/h4>\\r\\n\\r\\n<p>You have the right to decide whether to accept or reject cookies. You can exercise your cookie preferences by clicking on the \\\"Cookie Settings\\\" link in the footer of our website. You can also set or amend your web browser controls to accept or refuse cookies. If you choose to reject cookies, you may still use our Website though your access to some functionality and areas of our Website may be restricted.<\\/p>\\r\\n<br>\\r\\n<h4>Changes to our Cookie Policy<\\/h4>\\r\\n\\r\\n<p>We may update our Cookie Policy from time to time. We will notify you of any changes by posting the new Cookie Policy on this page.<\\/p>\",\"status\":1}', NULL, 'basic', NULL, '2020-07-04 23:42:52', '2024-05-23 03:26:36'),
(42, 'policy_pages.element', '{\"title\":\"Privacy Policy\",\"details\":\"<h4>Introduction<\\/h4>\\r\\n        <p>\\r\\n            This Privacy Policy describes how we collects, uses, and discloses information, including personal information, in connection with your use of our website.\\r\\n        <\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Information We Collect<\\/h4>\\r\\n        <p>We collect two main types of information on the Website:<\\/p>\\r\\n        <ul>\\r\\n            <li><p><strong>Personal Information: <\\/strong>This includes data that can identify you as an individual, such as your name, email address, phone number, or mailing address. We only collect this information when you voluntarily provide it to us, like signing up for a newsletter, contacting us through a form, or making a purchase.<\\/p><\\/li>\\r\\n            <li><p><strong>Non-Personal Information: <\\/strong>This data cannot be used to identify you directly. It includes details like your browser type, device type, operating system, IP address, browsing activity, and usage statistics. We collect this information automatically through cookies and other tracking technologies.<\\/p><\\/li>\\r\\n        <\\/ul>\\r\\n        <br \\/>\\r\\n        <h4>How We Use Information<\\/h4>\\r\\n        <p>The information we collect allows us to:<\\/p>\\r\\n        <ul>\\r\\n            <li>Operate and maintain the Website effectively.<\\/li>\\r\\n            <li>Send you newsletters or marketing communications, but only with your consent.<\\/li>\\r\\n            <li>Respond to your inquiries and fulfill your requests.<\\/li>\\r\\n            <li>Improve the Website and your user experience.<\\/li>\\r\\n            <li>Personalize your experience on the Website based on your browsing habits.<\\/li>\\r\\n            <li>Analyze how the Website is used to improve our services.<\\/li>\\r\\n            <li>Comply with legal and regulatory requirements.<\\/li>\\r\\n        <\\/ul>\\r\\n        <br \\/>\\r\\n        <h4>Sharing of Information<\\/h4>\\r\\n        <p>We may share your information with trusted third-party service providers who assist us in operating the Website and delivering our services. These providers are obligated by contract to keep your information confidential and use it only for the specific purposes we disclose it for.<\\/p>\\r\\n        <p>We will never share your personal information with any third parties for marketing purposes without your explicit consent.<\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Data Retention<\\/h4>\\r\\n        <p>We retain your personal information only for as long as necessary to fulfill the purposes it was collected for. We may retain it for longer periods only if required or permitted by law.<\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Security Measures<\\/h4>\\r\\n        <p>We take reasonable precautions to protect your information from unauthorized access, disclosure, alteration, or destruction. However, complete security cannot be guaranteed for any website or internet transmission.<\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Changes to this Privacy Policy<\\/h4>\\r\\n        <p>We may update this Privacy Policy periodically. We will notify you of any changes by posting the revised policy on the Website. We recommend reviewing this policy regularly to stay informed of any updates.<\\/p>\\r\\n        <p><strong>Remember:<\\/strong>  This is a sample policy and may need adjustments to comply with specific laws and reflect your website\'s unique data practices. Consider consulting with a legal professional to ensure your policy is fully compliant.<\\/p>\"}', NULL, 'basic', 'privacy-policy', '2021-06-09 08:50:42', '2024-05-20 05:50:57'),
(43, 'policy_pages.element', '{\"title\":\"Terms of Service\",\"details\":\"<h4>Introduction<\\/h4>\\r\\n        <p>\\r\\n            This Privacy Policy describes how we collects, uses, and discloses information, including personal information, in connection with your use of our website.\\r\\n        <\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Information We Collect<\\/h4>\\r\\n        <p>We collect two main types of information on the Website:<\\/p>\\r\\n        <ul>\\r\\n            <li><p><strong>Personal Information: <\\/strong>This includes data that can identify you as an individual, such as your name, email address, phone number, or mailing address. We only collect this information when you voluntarily provide it to us, like signing up for a newsletter, contacting us through a form, or making a purchase.<\\/p><\\/li>\\r\\n            <li><p><strong>Non-Personal Information: <\\/strong>This data cannot be used to identify you directly. It includes details like your browser type, device type, operating system, IP address, browsing activity, and usage statistics. We collect this information automatically through cookies and other tracking technologies.<\\/p><\\/li>\\r\\n        <\\/ul>\\r\\n        <br \\/>\\r\\n        <h4>How We Use Information<\\/h4>\\r\\n        <p>The information we collect allows us to:<\\/p>\\r\\n        <ul>\\r\\n            <li>Operate and maintain the Website effectively.<\\/li>\\r\\n            <li>Send you newsletters or marketing communications, but only with your consent.<\\/li>\\r\\n            <li>Respond to your inquiries and fulfill your requests.<\\/li>\\r\\n            <li>Improve the Website and your user experience.<\\/li>\\r\\n            <li>Personalize your experience on the Website based on your browsing habits.<\\/li>\\r\\n            <li>Analyze how the Website is used to improve our services.<\\/li>\\r\\n            <li>Comply with legal and regulatory requirements.<\\/li>\\r\\n        <\\/ul>\\r\\n        <br \\/>\\r\\n        <h4>Sharing of Information<\\/h4>\\r\\n        <p>We may share your information with trusted third-party service providers who assist us in operating the Website and delivering our services. These providers are obligated by contract to keep your information confidential and use it only for the specific purposes we disclose it for.<\\/p>\\r\\n        <p>We will never share your personal information with any third parties for marketing purposes without your explicit consent.<\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Data Retention<\\/h4>\\r\\n        <p>We retain your personal information only for as long as necessary to fulfill the purposes it was collected for. We may retain it for longer periods only if required or permitted by law.<\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Security Measures<\\/h4>\\r\\n        <p>We take reasonable precautions to protect your information from unauthorized access, disclosure, alteration, or destruction. However, complete security cannot be guaranteed for any website or internet transmission.<\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Changes to this Privacy Policy<\\/h4>\\r\\n        <p>We may update this Privacy Policy periodically. We will notify you of any changes by posting the revised policy on the Website. We recommend reviewing this policy regularly to stay informed of any updates.<\\/p>\\r\\n        <p><strong>Remember:<\\/strong>  This is a sample policy and may need adjustments to comply with specific laws and reflect your website\'s unique data practices. Consider consulting with a legal professional to ensure your policy is fully compliant.<\\/p>\"}', NULL, 'basic', 'terms-of-service', '2021-06-09 08:51:18', '2024-05-20 05:51:10'),
(44, 'maintenance.data', '{\"description\":\"<div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"text-align: center; font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">What information do we collect?<\\/h3><p class=\\\"font-18\\\" style=\\\"text-align: center; margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, nonetheless, visit our site anonymously.<\\/p><\\/div>\",\"image\":\"664df4d47ec811716384980.png\"}', NULL, 'basic', NULL, '2020-07-04 23:42:52', '2024-05-22 07:36:20'),
(45, 'client.element', '{\"has_image\":\"1\",\"image\":\"64c5eee6b21ca1690693350.png\"}', NULL, 'basic', NULL, '2023-07-29 08:01:50', '2023-07-30 02:32:30'),
(46, 'client.element', '{\"has_image\":\"1\",\"image\":\"64c5eeeb441461690693355.png\"}', NULL, 'basic', NULL, '2023-07-29 08:03:18', '2023-07-30 02:32:35'),
(47, 'client.element', '{\"has_image\":\"1\",\"image\":\"64c5eef077cdb1690693360.png\"}', NULL, 'basic', NULL, '2023-07-29 08:03:22', '2023-07-30 02:32:40'),
(48, 'client.element', '{\"has_image\":\"1\",\"image\":\"64c5eef82547e1690693368.png\"}', NULL, 'basic', NULL, '2023-07-29 08:03:29', '2023-07-30 02:32:48'),
(49, 'client.element', '{\"has_image\":\"1\",\"image\":\"64c5eeffc79521690693375.png\"}', NULL, 'basic', NULL, '2023-07-29 08:03:34', '2023-07-30 02:32:55'),
(50, 'client.element', '{\"has_image\":\"1\",\"image\":\"64c5ef0a59be71690693386.png\"}', NULL, 'basic', NULL, '2023-07-29 08:03:45', '2023-07-30 02:33:06'),
(51, 'client.element', '{\"has_image\":\"1\",\"image\":\"64c5ef118a20a1690693393.png\"}', NULL, 'basic', NULL, '2023-07-29 08:03:55', '2023-07-30 02:33:13'),
(52, 'feature.element', '{\"icon\":\"<i class=\\\"las la-headset\\\"><\\/i>\",\"title\":\"24\\/7 Support\",\"details\":\"Flexible Repayment : Customize your loan with easy payment plans.\"}', NULL, 'basic', NULL, '2023-07-29 09:22:25', '2023-08-05 02:46:04'),
(53, 'feature.element', '{\"icon\":\"<i class=\\\"fas fa-hand-holding-usd\\\"><\\/i>\",\"title\":\"Low Cost\",\"details\":\"Low-Interest Rates: Enjoy competitive rates for affordable borrowing.\"}', NULL, 'basic', NULL, '2023-07-29 09:22:36', '2023-08-05 02:46:24'),
(57, 'faq.content', '{\"heading\":\"Frequently Asked Questions\",\"subheading\":\"FAQ\",\"description\":\"Maecenas tempus tellus eget condimentu honemn quam semper libero sit amet adipiscing sem neque sed\"}', NULL, 'basic', NULL, '2023-07-29 09:44:36', '2023-07-29 09:44:36'),
(58, 'faq.element', '{\"question\":\"Why should you take loan from our platform ?\",\"answer\":\"<span style=\\\"color:rgb(78,78,78);font-family:Roboto, sans-serif;\\\">tara ipsum dolor sit amet consectetur adipisicing elit. Natus quos numquam atque exercitationem reprehenderit voluptas dolores ad repudiandae asperiores. Excepturi error dolor at veniam, modi vel quos tempora pariatur eaque.<\\/span><br \\/>\"}', NULL, 'basic', NULL, '2023-07-29 09:45:23', '2023-07-29 09:45:23'),
(59, 'faq.element', '{\"question\":\"Is it given installment easy more than other ?\",\"answer\":\"<span style=\\\"color:rgb(78,78,78);font-family:Roboto, sans-serif;\\\">Yes!\\u00a0 ipsum dolor sit amet consectetur adipisicing elit. Natus quos numquam atque exercitationem reprehenderit voluptas dolores ad repudiandae asperiores. Excepturi error dolor at veniam, modi vel quos tempora pariatur eaque.<\\/span><br \\/>\"}', NULL, 'basic', NULL, '2023-07-29 09:46:08', '2023-07-29 09:46:08'),
(60, 'faq.element', '{\"question\":\"How much you get max amount of loan of this author ?\",\"answer\":\"<span style=\\\"color:rgb(78,78,78);font-family:Roboto, sans-serif;\\\">As per company rules and regulation,\\u00a0 ipsum dolor sit amet consectetur adipisicing elit. Natus quos numquam atque exercitationem reprehenderit voluptas dolores ad repudiandae asperiores. Excepturi error dolor at veniam, modi vel quos tempora pariatur eaque.<\\/span><br \\/>\"}', NULL, 'basic', NULL, '2023-07-29 09:47:15', '2023-07-29 09:47:15'),
(61, 'faq.element', '{\"question\":\"Why people choose us ?\",\"answer\":\"<span style=\\\"color:rgb(78,78,78);font-family:Roboto, sans-serif;\\\">tara ipsum dolor sit amet consectetur adipisicing elit. Natus quos numquam atque exercitationem reprehenderit voluptas dolores ad repudiandae asperiores. Excepturi error dolor at veniam, modi vel quos tempora pariatur eaque.<\\/span><br \\/>\"}', NULL, 'basic', NULL, '2023-07-29 09:47:54', '2023-07-29 09:47:54'),
(62, 'faq.element', '{\"question\":\"Is there any hidden charge of my installment ? and loan amount ?\",\"answer\":\"<span style=\\\"color:rgb(78,78,78);font-family:Roboto, sans-serif;\\\">No!\\u00a0 ipsum dolor sit amet consectetur adipisicing elit. Natus quos numquam atque exercitationem reprehenderit voluptas dolores ad repudiandae asperiores. Excepturi error dolor at veniam, modi vel quos tempora pariatur eaque.<\\/span><br \\/>\"}', NULL, 'basic', NULL, '2023-07-29 09:48:45', '2023-07-29 09:48:45'),
(63, 'testimonial.element', '{\"has_image\":\"1\",\"author\":\"Selena\",\"designation\":\"Manager of BuzzCapital, Hyip\",\"quote\":\"Lorem ipsum dolor sit amet consectetur adipisicing elit. Cumque, nesciunt inventore. Laboriosam, aperiam amet rerum nostrum dolore asperiores eos perspiciatis officiis possimus ipsa, molestiae, quod minus modi tempora velit enim!\",\"image\":\"64cf9827e1ff01691326503.jpg\"}', NULL, 'basic', NULL, '2023-07-29 10:35:34', '2023-08-06 11:25:03'),
(64, 'testimonial.element', '{\"has_image\":\"1\",\"author\":\"Niyon Spark\",\"designation\":\"CEO, Neo Port Ice\",\"quote\":\"Lorem ipsum dolor sit amet consectetur adipisicing elit. Cumque, nesciunt inventore. Laboriosam, aperiam amet rerum nostrum dolore asperiores eos perspiciatis officiis possimus ipsa, molestiae, quod minus modi tempora velit enim!\",\"image\":\"64cf97f4a27ce1691326452.jpg\"}', NULL, 'basic', NULL, '2023-07-29 11:03:03', '2023-08-06 11:24:12'),
(65, 'testimonial.element', '{\"has_image\":\"1\",\"author\":\"David Joe\",\"designation\":\"CUSTOMER\",\"quote\":\"Lorem ipsum dolor sit amet consectetur adipisicing elit. Cumque, nesciunt inventore. Laboriosam, aperiam amet rerum nostrum dolore asperiores eos perspiciatis officiis possimus ipsa, molestiae, quod minus modi tempora velit enim!\",\"image\":\"64cf97ed826751691326445.jpg\"}', NULL, 'basic', NULL, '2023-07-29 11:03:31', '2023-08-06 11:24:05'),
(66, 'testimonial.element', '{\"has_image\":\"1\",\"author\":\"Shafodil Spraow\",\"designation\":\"CEO, OF TORTOYLAB\",\"quote\":\"Lorem ipsum dolor sit amet consectetur adipisicing elit. Cumque, nesciunt inventore. Laboriosam, aperiam amet rerum nostrum dolore asperiores eos perspiciatis officiis possimus ipsa, molestiae, quod minus modi tempora velit enim!\",\"image\":\"64cf97e54174c1691326437.jpg\"}', NULL, 'basic', NULL, '2023-07-29 11:04:30', '2023-08-06 11:23:57'),
(67, 'blog.element', '{\"has_image\":[\"1\"],\"title\":\"Unlock Your Dream Home: Explore Our Home Loan Options.\",\"description_nic\":\"<p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">I wouldn\\u2019t say it\\u2019s harder to get a mortgage if you\\u2019re self-employed, but it\\u2019s less straightforward than for an employed person. You\\u2019ll find that lenders will ask you for more documentation. They\\u2019re potentially going to look at your income a little bit more closely, compared with an employed person who will just have to provide payslips and maybe some bank statements.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">One of the outcomes of Covid last year was that it took a while for mortgage providers to start lending to more self-employed clients. There were minimum deposit restrictions in place. Some lenders still have these \\u2013 they will ask for a 25% deposit if you\\u2019re self-employed.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">But most are going back to the norm now.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">Lenders vary in terms of how much they will lend self-employed people and the type of self-employed clients they will accept. That just means you need to do a bit more research before you apply to a lender.<\\/p><h3 style=\\\"font-weight:700;line-height:1.2;font-size:1.5em;color:rgb(30,72,190);font-family:stratos, sans-serif;\\\">What if I only have one year\\u2019s accounts?<\\/h3><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">You should still be able to get a mortgage with just one year\\u2019s accounts. The majority of lenders usually want two years\\u2019 history as self-employed, but some will accept a year\\u2019s self-employed income.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">There are even lenders who in some scenarios would accept the self-employed from day one. For example if someone who had one year\\u2019s accounts as an accountant decided to be a self-employed builder, they might struggle because there\\u2019s no work experience history. But an employed accountant moving to become a self-employed accountant is more likely to be accepted with a year\\u2019s accounts.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">Imagine a doctor that has worked for the NHS for five years is buying into a local, established practice. Many lenders would consider that from day one because the practice already has history. So it is definitely possible to get a mortgage with one year\\u2019s accounts, and sometimes less.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">As brokers we\\u2019re here to help. We\\u2019ve got a really good understanding of which lenders are most suitable for every type of client.<\\/p><h3 style=\\\"font-weight:700;line-height:1.2;font-size:1.5em;color:rgb(30,72,190);font-family:stratos, sans-serif;\\\">Are self-cert mortgages still available?<\\/h3><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">Thankfully not, but we have seen some companies try to set up self-certs abroad. I would avoid those companies like the plague.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">Self-cert mortgages are not a good idea \\u2013 back in 2008 a major factor in the credit crunch for the mortgage world was self-cert, and so they\\u2019re not available anymore.<\\/p><h3 style=\\\"font-weight:700;line-height:1.2;font-size:1.5em;color:rgb(30,72,190);font-family:stratos, sans-serif;\\\">Can you get a joint mortgage if one person is self-employed?<\\/h3><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">It does help to have someone else on the mortgage who\\u2019s employed, particularly when it comes to credit scoring. It will also increase the amount you can borrow, because the lender will base the loan on your combined incomes.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">One thing to do before you apply for a mortgage if you\\u2019re self-employed is to have a look at your credit score and register on the electoral roll. Some lenders might score you more harshly when you\\u2019re self-employed as the risk to them is higher.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">With mortgages for the self-employed a broker really comes into their own \\u2013 we can look at all the different options.<\\/p><h3 style=\\\"font-weight:700;line-height:1.2;font-size:1.5em;color:rgb(30,72,190);font-family:stratos, sans-serif;\\\">What\\u2019s the difference in mortgages for a sole trader and a limited company director?<\\/h3><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">A lot of this is to do with how the lenders treat you \\u2013 firstly in terms of how they calculate your income. If you\\u2019re a sole trader or running a partnership, the lender will usually take two years worth of your tax returns. They\\u2019ve got various names: tax calculations or SA302s. The lender will usually work off your net profit, which is your income after your expenses. They will usually take either an average of your last two years\\u2019 net profits or use your latest year if there\\u2019s a steady increase.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">The main difference for a limited company is that even though you\\u2019re self-employed, you\\u2019re actually employed by your limited company and receive your income via salary and dividends. The majority of lenders will use your two years\\u2019 salary and dividend figures from your tax calculations.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">But there are also lenders that will disregard your salary and dividends and go for your net profits instead \\u2013 that will often allow you to borrow a lot more. So, as a limited company the way lenders can view you will vary considerably.<\\/p><h3 style=\\\"font-weight:700;line-height:1.2;font-size:1.5em;color:rgb(30,72,190);font-family:stratos, sans-serif;\\\">How much can a self-employed person borrow on a mortgage?<\\/h3><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">Where affordability can vary is whether the lender uses the average of your last two years or your latest year. The average income might work out at, say, \\u00a325,000 but if your latest year is \\u00a340,000 that could be quite a big difference.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">But it\\u2019s with limited companies that we see the biggest difference in affordability. A limited company director may have taken a salary and dividend of say \\u00a340,000 \\u2013 some lenders will take that as the income. But perhaps their net profit is \\u00a3100,000 a year. Other lenders will take that as your income \\u2013 which means a massive difference in how much you can borrow. NatWest or Halifax will use salary and dividends while Coventry and HSBC, for example, will use the \\u00a3100,000.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\">If you want a rough idea of how much you could borrow, the very general rule is about 4.5 times your income, up to a maximum of about 4.75 and, in some very rare instances, up to 5 times.<\\/p><p style=\\\"color:rgb(19,26,56);font-size:16px;line-height:1.4;max-width:75ch;font-family:stratos, sans-serif;\\\"><br \\/><\\/p><p style=\\\"line-height:1.4;max-width:75ch;\\\"><font color=\\\"#131a38\\\" face=\\\"stratos, sans-serif\\\"><span style=\\\"font-size:16px;\\\"><\\/span><\\/font><br \\/><\\/p>\",\"image\":\"64cf8654346461691321940.jpg\"}', NULL, 'basic', 'unlock-your-dream-home:-explore-our-home-loan-options', '2023-07-29 11:29:41', '2023-08-06 10:09:00'),
(68, 'blog.element', '{\"has_image\":[\"1\"],\"title\":\"Rev Up Your Journey: A Guide to Car Loans\",\"description_nic\":\"<div>I wouldn\\u2019t say it\\u2019s harder to get a mortgage if you\\u2019re self-employed, but it\\u2019s less straightforward than for an employed person. You\\u2019ll find that lenders will ask you for more documentation. They\\u2019re potentially going to look at your income a little bit more closely, compared with an employed person who will just have to provide payslips and maybe some bank statements.<\\/div><div><br \\/><\\/div><div>One of the outcomes of Covid last year was that it took a while for mortgage providers to start lending to more self-employed clients. There were minimum deposit restrictions in place. Some lenders still have these \\u2013 they will ask for a 25% deposit if you\\u2019re self-employed.<\\/div><div><br \\/><\\/div><div>But most are going back to the norm now.<\\/div><div><br \\/><\\/div><div>Lenders vary in terms of how much they will lend self-employed people and the type of self-employed clients they will accept. That just means you need to do a bit more research before you apply to a lender.<\\/div><div><br \\/><\\/div><div>What if I only have one year\\u2019s accounts?<\\/div><div>You should still be able to get a mortgage with just one year\\u2019s accounts. The majority of lenders usually want two years\\u2019 history as self-employed, but some will accept a year\\u2019s self-employed income.<\\/div><div><br \\/><\\/div><div>There are even lenders who in some scenarios would accept the self-employed from day one. For example if someone who had one year\\u2019s accounts as an accountant decided to be a self-employed builder, they might struggle because there\\u2019s no work experience history. But an employed accountant moving to become a self-employed accountant is more likely to be accepted with a year\\u2019s accounts.<\\/div><div><br \\/><\\/div><div>Imagine a doctor that has worked for the NHS for five years is buying into a local, established practice. Many lenders would consider that from day one because the practice already has history. So it is definitely possible to get a mortgage with one year\\u2019s accounts, and sometimes less.<\\/div><div><br \\/><\\/div><div>As brokers we\\u2019re here to help. We\\u2019ve got a really good understanding of which lenders are most suitable for every type of client.<\\/div><div><br \\/><\\/div><div>Are self-cert mortgages still available?<\\/div><div>Thankfully not, but we have seen some companies try to set up self-certs abroad. I would avoid those companies like the plague.<\\/div><div><br \\/><\\/div><div>Self-cert mortgages are not a good idea \\u2013 back in 2008 a major factor in the credit crunch for the mortgage world was self-cert, and so they\\u2019re not available anymore.<\\/div><div><br \\/><\\/div><div>Can you get a joint mortgage if one person is self-employed?<\\/div><div>It does help to have someone else on the mortgage who\\u2019s employed, particularly when it comes to credit scoring. It will also increase the amount you can borrow, because the lender will base the loan on your combined incomes.<\\/div><div><br \\/><\\/div><div>One thing to do before you apply for a mortgage if you\\u2019re self-employed is to have a look at your credit score and register on the electoral roll. Some lenders might score you more harshly when you\\u2019re self-employed as the risk to them is higher.<\\/div><div><br \\/><\\/div><div>With mortgages for the self-employed a broker really comes into their own \\u2013 we can look at all the different options.<\\/div><div><br \\/><\\/div><div>What\\u2019s the difference in mortgages for a sole trader and a limited company director?<\\/div><div>A lot of this is to do with how the lenders treat you \\u2013 firstly in terms of how they calculate your income. If you\\u2019re a sole trader or running a partnership, the lender will usually take two years worth of your tax returns. They\\u2019ve got various names: tax calculations or SA302s. The lender will usually work off your net profit, which is your income after your expenses. They will usually take either an average of your last two years\\u2019 net profits or use your latest year if there\\u2019s a steady increase.<\\/div><div><br \\/><\\/div><div>The main difference for a limited company is that even though you\\u2019re self-employed, you\\u2019re actually employed by your limited company and receive your income via salary and dividends. The majority of lenders will use your two years\\u2019 salary and dividend figures from your tax calculations.<\\/div><div><br \\/><\\/div><div>But there are also lenders that will disregard your salary and dividends and go for your net profits instead \\u2013 that will often allow you to borrow a lot more. So, as a limited company the way lenders can view you will vary considerably.<\\/div><div><br \\/><\\/div><div>How much can a self-employed person borrow on a mortgage?<\\/div><div>Where affordability can vary is whether the lender uses the average of your last two years or your latest year. The average income might work out at, say, \\u00a325,000 but if your latest year is \\u00a340,000 that could be quite a big difference.<\\/div><div><br \\/><\\/div><div>But it\\u2019s with limited companies that we see the biggest difference in affordability. A limited company director may have taken a salary and dividend of say \\u00a340,000 \\u2013 some lenders will take that as the income. But perhaps their net profit is \\u00a3100,000 a year. Other lenders will take that as your income \\u2013 which means a massive difference in how much you can borrow. NatWest or Halifax will use salary and dividends while Coventry and HSBC, for example, will use the \\u00a3100,000.<\\/div><div><br \\/><\\/div><div>If you want a rough idea of how much you could borrow, the very general rule is about 4.5 times your income, up to a maximum of about 4.75 and, in some very rare instances, up to 5 times.<\\/div>\",\"image\":\"64cf86417bede1691321921.jpg\"}', NULL, 'basic', 'rev-up-your-journey:-a-guide-to-car-loans', '2023-07-29 11:32:36', '2023-08-06 10:08:41'),
(69, 'footer.content', '{\"title\":\"About Viserloan\",\"content\":\"Maecenas tempus tellus egondime honcus sequam seitmet dipiscing sem eque sedipsuNam quam\",\"subscription_heading\":\"Newsletter\",\"subscription_subheading\":\"Stay Informed and Never Miss a Beat: Subscribe to Our Loan Updates\"}', NULL, 'basic', NULL, '2023-07-30 03:02:30', '2023-08-03 11:39:21'),
(70, 'footer_cta.content', '{\"heading\":\"Like To Become A Hero\",\"subheading\":\"We are an international company working globally having clients from different parts of the world.\",\"button_name\":\"Get Loan\",\"button_link\":\"\\/loan\"}', NULL, 'basic', NULL, '2023-07-30 03:06:02', '2023-07-30 03:06:42'),
(71, 'social_icon.element', '{\"title\":\"Twitter\",\"social_icon\":\"<i class=\\\"fab fa-twitter\\\"><\\/i>\",\"url\":\"https:\\/\\/www.twitter.com\\/\"}', NULL, 'basic', NULL, '2023-07-30 03:11:59', '2023-07-30 03:11:59'),
(72, 'social_icon.element', '{\"title\":\"Instragram\",\"social_icon\":\"<i class=\\\"fab fa-instagram\\\"><\\/i>\",\"url\":\"https:\\/\\/www.instragram.com\\/\"}', NULL, 'basic', NULL, '2023-07-30 03:12:46', '2023-07-30 03:12:46'),
(73, 'social_icon.element', '{\"title\":\"Youtube\",\"social_icon\":\"<i class=\\\"fab fa-youtube\\\"><\\/i>\",\"url\":\"https:\\/\\/www.youtube.com\\/\"}', NULL, 'basic', NULL, '2023-07-30 03:13:07', '2023-07-30 03:13:08'),
(74, 'social_icon.element', '{\"title\":\"Linkedin\",\"social_icon\":\"<i class=\\\"fab fa-linkedin\\\"><\\/i>\",\"url\":\"www.linkedin.com\"}', NULL, 'basic', NULL, '2023-07-30 03:13:57', '2023-07-30 03:13:57'),
(75, 'subscribe.content', '{\"heading\":\"Newsletter\",\"subheading\":\"Stay Informed and Never Miss a Beat: Subscribe to Our Loan Updates\"}', NULL, 'basic', NULL, '2023-07-30 03:17:07', '2023-07-30 03:17:45'),
(76, 'breadcrumb.content', '{\"has_image\":\"1\",\"image\":\"64cf7341aca501691317057.jpg\"}', NULL, 'basic', NULL, '2023-07-30 04:40:47', '2023-08-06 08:47:37'),
(77, 'contact.content', '{\"heading\":\"Send Your Messages\",\"phone\":\"+4401245785\",\"email\":\"example@test.com\",\"location\":\"38\\/D North street London, UK\",\"map\":\"https:\\/\\/maps.google.com\\/maps?width=100%25&height=600&hl=en&q=1%20Grafton%20Street,%20Dublin,%20Ireland+(My%20Business%20Name)&t=&z=14&ie=UTF8&iwloc=B&output=embed\"}', NULL, 'basic', NULL, '2023-07-30 08:48:33', '2023-08-02 06:09:27'),
(78, 'contact.element', '{\"has_image\":[\"1\"],\"title\":\"Office Address\",\"content\":\"28 Benin, south of Niger #5 San  Francisco, United States of America\",\"image\":\"64c64732d5d7b1690715954.png\"}', NULL, 'basic', NULL, '2023-07-30 08:49:14', '2023-07-30 08:49:14'),
(79, 'contact.element', '{\"has_image\":[\"1\"],\"title\":\"Email Address\",\"content\":\"hello@demosite.com  \\/   ticket@cyarb.com\",\"image\":\"64c6474a24e941690715978.png\"}', NULL, 'basic', NULL, '2023-07-30 08:49:38', '2023-07-30 09:01:46'),
(80, 'contact.element', '{\"has_image\":[\"1\"],\"title\":\"Phone Number\",\"content\":\"+123 - 456 - 7890 - 124\",\"image\":\"64c6475e5cec71690715998.png\"}', NULL, 'basic', NULL, '2023-07-30 08:49:58', '2023-07-30 09:02:16'),
(81, 'login.content', '{\"has_image\":\"1\",\"heading\":\"Welcome back to Rapidlab\",\"image\":\"64cb533feb6261691046719.jpg\"}', NULL, 'basic', NULL, '2023-07-30 10:36:28', '2023-08-06 03:03:25'),
(82, 'register.content', '{\"has_image\":\"1\",\"heading\":\"Welcome to Viserloan\",\"image\":\"64cb5355b83161691046741.jpg\"}', NULL, 'basic', NULL, '2023-07-30 11:03:06', '2023-08-03 04:42:22'),
(83, 'counter.element', '{\"title\":\"Total Loan\",\"counter_digit\":\"$8M+\"}', NULL, 'basic', NULL, '2023-07-31 05:42:08', '2023-07-31 05:49:15'),
(84, 'counter.element', '{\"title\":\"Winning Awards\",\"counter_digit\":\"195\"}', NULL, 'basic', NULL, '2023-07-31 05:42:27', '2023-07-31 05:43:24'),
(85, 'counter.element', '{\"title\":\"Happy Client\",\"counter_digit\":\"1K+\"}', NULL, 'basic', NULL, '2023-07-31 05:42:55', '2023-07-31 05:46:47'),
(86, 'counter.element', '{\"title\":\"Country Wide\",\"counter_digit\":\"110+\"}', NULL, 'basic', NULL, '2023-07-31 05:43:49', '2023-07-31 05:43:49'),
(87, 'plan.content', '{\"subheading\":\"Featured Plans\",\"heading\":\"Secure your dreams with our empowering featured loans\"}', NULL, 'basic', NULL, '2023-08-02 08:50:23', '2023-08-03 05:35:49'),
(88, 'application_fee_info.content', '{\"heading\":\"Your balance is not sufficient for application of\",\"subheading\":\"Please deposit at least the plan application charge amount\"}', NULL, 'basic', NULL, '2023-08-02 09:52:00', '2023-08-02 09:52:00'),
(89, 'faq.element', '{\"question\":\"How much is the loan application fee for various loan options?\",\"answer\":\"Lorem ipsum dolor sit amet consectetur adipisicing elit. Fuga, temporibus quidem? Praesentium magnam commodi assumenda illo incidunt placeat tenetur eius necessitatibus animi reiciendis tempore rerum ad neque, voluptates ipsa delectus harum quia fugiat a quae aspernatur quaerat. Molestias voluptatum optio unde laudantium culpa in.\"}', NULL, 'basic', NULL, '2023-08-02 12:04:56', '2023-08-02 12:04:56');
INSERT INTO `frontends` (`id`, `data_keys`, `data_values`, `seo_content`, `tempname`, `slug`, `created_at`, `updated_at`) VALUES
(90, 'blog.element', '{\"has_image\":[\"1\"],\"title\":\"Dealing with Delinquent Business Loans: Protecting Your Finances and Credit\",\"description_nic\":\"<p style=\\\"border:0px;font-size:17px;margin-right:0px;margin-bottom:1.6em;margin-left:0px;padding:0px;vertical-align:baseline;line-height:1.5rem;color:rgb(68,68,68);font-family:\'Open Sans\', sans-serif;\\\">Closing on a business loan often brings a sense of relief and excitement as you secure the funds to grow your business. However, it\\u2019s not uncommon to find yourself overwhelmed after a few months or years, realizing that you\\u2019ve taken on more than you can handle. With over\\u00a0<a href=\\\"https:\\/\\/www.usatoday.com\\/story\\/money\\/personalfinance\\/2014\\/07\\/29\\/america-debt-loads\\/13152651\\/\\\" style=\\\"border:0px;font-size:17px;font-style:inherit;font-weight:inherit;margin:0px;padding:0px;vertical-align:baseline;background-color:transparent;color:rgb(51,102,255);\\\">one-third of Americans struggling with delinquent debt<\\/a>\\u00a0and the risk of loan defaults, taking immediate action is crucial when you fall behind on loan payments. This article provides essential information on delinquent loans, defaults, and practical strategies to protect yourself and minimize the associated damage.<\\/p><h2 style=\\\"border:0px;font-size:22px;font-weight:700;margin-bottom:20px;padding:0px;vertical-align:baseline;clear:both;color:rgb(0,0,0);line-height:1.15;font-family:\'Open Sans\', sans-serif;\\\">Understanding Delinquent Loans<\\/h2><p style=\\\"border:0px;font-size:17px;margin-right:0px;margin-bottom:1.6em;margin-left:0px;padding:0px;vertical-align:baseline;line-height:1.5rem;color:rgb(68,68,68);font-family:\'Open Sans\', sans-serif;\\\">A loan becomes delinquent when you miss a payment, even in just one day. If you miss payments or cannot make them for an extended period (typically 90 to 120 days), the lender may classify the loan as default and initiate collection procedures. Both delinquent loans and defaults have negative implications for your credit. It\\u2019s important to note that the timing of your delinquency rarely matters. For example, if your payment is due on February 1 and the lender doesn\\u2019t receive it that day, the loan becomes delinquent on February 2.<\\/p><h2 style=\\\"border:0px;font-size:22px;font-weight:700;margin-bottom:20px;padding:0px;vertical-align:baseline;clear:both;color:rgb(0,0,0);line-height:1.15;font-family:\'Open Sans\', sans-serif;\\\">Consequences of Delinquent Loans<\\/h2><p style=\\\"border:0px;font-size:17px;margin-right:0px;margin-bottom:1.6em;margin-left:0px;padding:0px;vertical-align:baseline;line-height:1.5rem;color:rgb(68,68,68);font-family:\'Open Sans\', sans-serif;\\\">The\\u00a0<a href=\\\"https:\\/\\/www.forafinancial.com\\/blog\\/working-capital\\/what-are-delinquent-loans\\/\\\" style=\\\"border:0px;font-size:17px;font-style:inherit;font-weight:inherit;margin:0px;padding:0px;vertical-align:baseline;background-color:transparent;color:rgb(51,102,255);\\\">consequences of a delinquent loan<\\/a>\\u00a0depend on your lender\\u2019s policies and the terms outlined in the loan agreement. However, there are three typical outcomes:<\\/p><ol style=\\\"border:0px;font-size:17px;margin-bottom:1.5em;margin-left:3em;vertical-align:baseline;list-style-type:decimal;color:rgb(68,68,68);font-family:\'Open Sans\', sans-serif;\\\"><li style=\\\"border:0px;font-size:17px;font-style:inherit;font-weight:inherit;margin-top:0px;margin-right:0px;margin-left:0px;padding:0px;vertical-align:baseline;\\\"><span style=\\\"border:0px;font-style:inherit;font-weight:700;margin-top:0px;margin-right:0px;margin-left:0px;padding:0px;vertical-align:baseline;\\\">Penalty Rates & Late Fees<\\/span>: Loan agreements often permit lenders to charge late fees after a few days grace period. Some agreements also permit the lender to increase the interest rate on overdue amounts, known as a \\u201cpenalty rate\\u201d or \\u201cdefault rate.\\u201d Late fee structures vary among lenders, so it\\u2019s essential to understand their specific policies to avoid surprises.<\\/li><li style=\\\"border:0px;font-size:17px;font-style:inherit;font-weight:inherit;margin-top:0px;margin-right:0px;margin-left:0px;padding:0px;vertical-align:baseline;\\\"><span style=\\\"border:0px;font-style:inherit;font-weight:700;margin-top:0px;margin-right:0px;margin-left:0px;padding:0px;vertical-align:baseline;\\\">Negative Impact on Credit Score<\\/span>: Once you are 30 days late on payments, lenders can report the late payment to credit bureaus. Beyond this period, a late payment can decrease your credit score by nearly 100 points. In addition, poor credit score makes qualifying for future business loans more challenging. Late payments can remain on your credit report for up to seven years, even if you pay the lender after the item is reported.<\\/li><\\/ol><p style=\\\"border:0px;font-size:17px;margin-right:0px;margin-bottom:1.6em;margin-left:0px;padding:0px;vertical-align:baseline;line-height:1.5rem;color:rgb(68,68,68);font-family:\'Open Sans\', sans-serif;\\\">It\\u2019s worth noting that this 30-day rule does not apply to business credit reports, as lenders can report late payments to commercial credit bureaus even if you are just one day late.<\\/p><h2 style=\\\"border:0px;font-size:22px;font-weight:700;margin-bottom:20px;padding:0px;vertical-align:baseline;clear:both;color:rgb(0,0,0);line-height:1.15;font-family:\'Open Sans\', sans-serif;\\\">Increased Contact from Lenders<\\/h2><p style=\\\"border:0px;font-size:17px;margin-right:0px;margin-bottom:1.6em;margin-left:0px;padding:0px;vertical-align:baseline;line-height:1.5rem;color:rgb(68,68,68);font-family:\'Open Sans\', sans-serif;\\\">When you have a delinquent loan, expect frequent calls and emails from your lender urging you to make payments. Lenders prioritize collection efforts while the deadline is fresh in your mind. As delinquency continues, it becomes more challenging for lenders to collect the debt.<\\/p><h2 style=\\\"border:0px;font-size:22px;font-weight:700;margin-bottom:20px;padding:0px;vertical-align:baseline;clear:both;color:rgb(0,0,0);line-height:1.15;font-family:\'Open Sans\', sans-serif;\\\">Delinquent Loans vs. Defaulted Loans<\\/h2><p style=\\\"border:0px;font-size:17px;margin-right:0px;margin-bottom:1.6em;margin-left:0px;padding:0px;vertical-align:baseline;line-height:1.5rem;color:rgb(68,68,68);font-family:\'Open Sans\', sans-serif;\\\">A loan transitions from delinquency to default when you have an outstanding balance for an extended period specified in the loan agreement. Typically, lenders wait 90 to 120 days before considering a loan as default.<\\/p><h2 style=\\\"border:0px;font-size:22px;font-weight:700;margin-bottom:20px;padding:0px;vertical-align:baseline;clear:both;color:rgb(0,0,0);line-height:1.15;font-family:\'Open Sans\', sans-serif;\\\">How to Identify Defaulted Loans<\\/h2><p style=\\\"border:0px;font-size:17px;margin-right:0px;margin-bottom:1.6em;margin-left:0px;padding:0px;vertical-align:baseline;line-height:1.5rem;color:rgb(68,68,68);font-family:\'Open Sans\', sans-serif;\\\">When a loan goes into default, the lender will send you a written notice stating that you have breached the loan agreement and must immediately repay the entire loan balance. The lender might also sell or transfer the debt to a collection agency, escalating collection efforts to recover the outstanding balance. If the lender believes they won\\u2019t recover the money, they can charge off the loan, removing it from their books. However, you remain responsible for paying the debt.<\\/p><h2 style=\\\"border:0px;font-size:22px;font-weight:700;margin-bottom:20px;padding:0px;vertical-align:baseline;clear:both;color:rgb(0,0,0);line-height:1.15;font-family:\'Open Sans\', sans-serif;\\\">Actions After Default<\\/h2><p style=\\\"border:0px;font-size:17px;margin-right:0px;margin-left:0px;padding:0px;vertical-align:baseline;line-height:1.5rem;color:rgb(68,68,68);font-family:\'Open Sans\', sans-serif;\\\">The lender\\u2019s subsequent actions depend on whether the loan is secured or unsecured. Secured loans have collateral or personal guarantees backing them, while unsecured loans do not.<\\/p>\",\"image\":\"64cf8585400961691321733.jpg\"}', NULL, 'basic', 'dealing-with-delinquent-business-loans:-protecting-your-finances-and-credit', '2023-08-03 06:18:38', '2023-08-06 10:05:33'),
(91, 'blog.element', '{\"has_image\":[\"1\"],\"title\":\"Agri-loan, key to farming success\",\"description_nic\":\"<span style=\\\"color:rgb(16,24,40);font-family:\'Noto Serif JP\', serif;font-size:18px;\\\">Starting today\'s article with statistics provided by the Bangladesh Bank.<\\/span><br \\/><div><p style=\\\"margin-right:0px;margin-bottom:1.55556em;margin-left:0px;padding:0px;font-size:18px;line-height:inherit;color:rgb(16,24,40);font-family:\'Noto Serif JP\', serif;\\\">According to IFPRI, 19 percent of farmers take loans from relatives. 15 percent from the landowner, 11.4 percent come from moneylenders and 3.6 percent from various associations and cooperatives. Farmers get the largest share of the loan from the Krishi Bank, which is about 15 percent. Large, medium and small farmers together get 36 percent of the total loan while marginal farmers get about 5 percent. The total percentage of loan all the farmers get is 36 percent. Sharecroppers, the farmers who cultivate other people\'s land on lease, do not get this loan. As a result, they have to rely on loans from other sources, including NGOs.<\\/p><p style=\\\"margin-right:0px;margin-bottom:1.55556em;margin-left:0px;padding:0px;font-size:18px;line-height:inherit;color:rgb(16,24,40);font-family:\'Noto Serif JP\', serif;\\\">Small NGOs and associations began to form in the districts and upazilas from the 80s to the early 90s of the last century. Along with other developmental activities, these institutions started a micro-credit programme. Institutions thrive mainly on interest earned from loans. But there is no such change in the farmer I have witnessed. The farmer falls into a debt trap and sometimes carry the burden of prolonged loans that they take from NGOs and local moneylenders. Farmer Rafiqul Islam from Natore, at one of the open-air discussion among farmers and policymakers, popularly known as \'Krishi Budget Krishoker Budget\' (Farmers\' Voices in Budget, aired on Channel i), said he has never seen any political person become poor while doing politics, but the farmers are not well off doing their profession, which is farming. \\\"We don\'t have capital, no one thinks about our market, no one talks about us,\\\" Rafiqul angrily said. Such anger doesn\'t only come from Rafiqul, but almost every farmer bears the same agony. Most importantly, the moneylenders expanded their business by capitalizing on the poor state of the farmers and they never want them to get out of this vicious circle of borrowing money from the locally-rich and powerful people.<\\/p><\\/div>\",\"image\":\"64cf8555be2da1691321685.jpg\"}', NULL, 'basic', 'agri-loan-key-to-farming-success', '2023-08-03 06:21:44', '2023-08-06 10:04:45'),
(92, 'blog.element', '{\"has_image\":[\"1\"],\"title\":\"Helped by Loan : Finding the Perfect Summer Internship\",\"description_nic\":\"<p style=\\\"margin-top:1.25em;margin-bottom:1rem;color:rgb(68,68,68);font-family:\'Nimbus Sans\', Arial, sans-serif;font-size:18px;\\\">After graduating from college, many jobs want experience in your work field. Having a summer internship is a great way gain experience. Here are some ways to find the perfect internship for you:<\\/p><p style=\\\"margin-top:1.25em;margin-bottom:1rem;color:rgb(68,68,68);font-family:\'Nimbus Sans\', Arial, sans-serif;font-size:18px;\\\"><span style=\\\"font-weight:700;\\\">Check out online resources<\\/span><\\/p><p style=\\\"margin-top:1.25em;margin-bottom:1rem;color:rgb(68,68,68);font-family:\'Nimbus Sans\', Arial, sans-serif;font-size:18px;\\\">There are many online resources to help you look for internships. CyHire is a great tool to find employers who are recruiting Iowa State students. You can search for positions by your college or major, upload your resume, view career fair information and more. You can even automate your CyHire search to have new opportunities emailed to you. LinkedIn is another great tool to connect with people and to find internships in different cities and states. Through LinkedIn you can also connect with alumni of Iowa State University.<\\/p><p style=\\\"margin-top:1.25em;margin-bottom:1rem;color:rgb(68,68,68);font-family:\'Nimbus Sans\', Arial, sans-serif;font-size:18px;\\\"><span style=\\\"font-weight:700;\\\">Attend career fairs<\\/span><\\/p><p style=\\\"margin-top:1.25em;margin-bottom:1rem;color:rgb(68,68,68);font-family:\'Nimbus Sans\', Arial, sans-serif;font-size:18px;\\\">Career fairs are a great way to network and to create connections with people in the work force. Through networking you are able to share your resume and network for yourself with others. Connections are important for finding job opportunities.<\\/p><p style=\\\"margin-top:1.25em;margin-bottom:1rem;color:rgb(68,68,68);font-family:\'Nimbus Sans\', Arial, sans-serif;font-size:18px;\\\"><span style=\\\"font-weight:700;\\\">Contact the employer directly<\\/span><\\/p><p style=\\\"margin-top:1.25em;margin-bottom:1rem;color:rgb(68,68,68);font-family:\'Nimbus Sans\', Arial, sans-serif;font-size:18px;\\\">You don\\u2019t see an internship at a business or organization you want to work for? Contact the business or the employers. This shows them that you are motivated and willing to go the extra mile.<\\/p><p style=\\\"margin-top:1.25em;margin-bottom:1rem;color:rgb(68,68,68);font-family:\'Nimbus Sans\', Arial, sans-serif;font-size:18px;\\\"><span style=\\\"font-weight:700;\\\">Find something that works for you<\\/span><\\/p><p style=\\\"margin-top:1.25em;margin-bottom:1rem;color:rgb(68,68,68);font-family:\'Nimbus Sans\', Arial, sans-serif;font-size:18px;\\\">Looking for an internship that intrigues you and will help you improve your skills and connections is important when making a final decision. While there are both paid and un-paid internships, there may be scholarships you can apply for if you decide to take an un-paid position, so don\\u2019t discount an opportunity if it will ultimately help you gain valuable experience.<\\/p>\",\"image\":\"64cf85457b75c1691321669.jpg\"}', NULL, 'basic', 'helped-by-loan-:-finding-the-perfect-summer-internship', '2023-08-03 06:24:00', '2023-08-06 10:04:29'),
(93, 'cta.content', '{\"heading\":\"Like To Become A Hero\",\"subheading\":\"We are an international company working globally having clients from different parts of the world.\",\"button_name\":\"Take Loan\",\"button_link\":\"loan\"}', NULL, 'basic', NULL, '2023-08-03 11:37:40', '2023-08-06 10:14:48'),
(94, 'policy_pages.element', '{\"title\":\"Rapid Policy\",\"details\":\"<h4>Loan Application and Approval Policy:<\\/h4>\\r\\n\\r\\n<p>\\r\\n    Clearly outline the eligibility criteria for loan applications, including credit score, income requirements, and\\r\\n    any specific documentation needed. Describe the process of application submission, review, and approval. Specify the\\r\\n    maximum loan amounts, interest rates, and repayment terms. Ensure transparency by explaining how loan decisions are\\r\\n    made and the expected timeline for approval or denial.\\r\\n<\\/p><br \\/>\\r\\n\\r\\n<h4>Interest Rate and Fee Policy:<\\/h4>\\r\\n\\r\\n<p>\\r\\n    Detail the methodology for determining interest rates based on factors like creditworthiness, loan type, and\\r\\n    market conditions. Clarify any additional fees or charges, such as origination fees or prepayment penalties.\\r\\n    Provide examples and scenarios to help borrowers understand the total cost of borrowing. Emphasize transparency\\r\\n    and compliance with usury laws or consumer protection regulations \\r\\n<\\/p><br \\/>\\r\\n\\r\\n<h4>Privacy and Data Security Policy:<\\/h4>\\r\\n<p>\\r\\n    Address how customer data will be collected, stored, processed, and protected in compliance with relevant data\\r\\n    protection laws (e.g., GDPR or CCPA). Describe measures taken to ensure data security, such as encryption and\\r\\n    regular security audits. Outline how customer data will be used, shared (if applicable), and how customers can\\r\\n    access or modify their information.\\r\\n<\\/p><br \\/>\\r\\n\\r\\n<h4>Repayment and Default Policy:<\\/h4>\\r\\n\\r\\n<p>\\r\\n    Clearly explain the repayment options available to borrowers, including automatic deductions, manual payments,\\r\\n    and payment schedules. Outline the consequences of missed or late payments, including potential late fees and\\r\\n    impact on credit scores. Describe the steps taken in case of loan default, such as collections processes,\\r\\n    potential legal actions, and implications for future borrowing.\\r\\n<\\/p><br \\/>\\r\\n\\r\\n<h4>Customer Service and Complaint Resolution Policy:<\\/h4>\\r\\n\\r\\n<p>\\r\\n    Define the channels and methods through which customers can seek assistance, ask questions, or make complaints.\\r\\n    Establish a dedicated customer support team and provide contact information. Describe the process for addressing\\r\\n    and resolving customer complaints, including timelines for response and resolution. Highlight your commitment to\\r\\n    excellent customer service.\\r\\n<\\/p>\"}', NULL, 'basic', 'rapid-policy', '2023-08-05 10:09:48', '2024-05-22 08:09:01'),
(95, 'kyc.content', '{\"required\":\"Please submit the required KYC information to verify yourself. Otherwise, you couldn\'t make any withdrawal requests to the system.\",\"pending\":\"Your submitted KYC information is pending for admin approval. Please wait till that.\",\"reject\":\"Your KYC document has been rejected. Please resubmit the document for further review.\"}', NULL, 'basic', '', '2024-05-21 02:52:28', '2024-05-21 02:52:28'),
(96, 'register_disable.content', '{\"has_image\":\"1\",\"heading\":\"Registration Currently Disabled\",\"subheading\":\"Registration unavailable: Please check back later for updates. Thank you for your patience.\",\"button_name\":\"Go Home\",\"button_url\":\"#\",\"image\":\"664c629a651d61716282010.png\"}', NULL, 'basic', '', '2024-05-21 03:00:10', '2024-05-22 04:24:49');

-- --------------------------------------------------------

--
-- Table structure for table `gateways`
--

CREATE TABLE `gateways` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `code` int(10) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `alias` varchar(40) NOT NULL DEFAULT 'NULL',
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `gateway_parameters` text DEFAULT NULL,
  `supported_currencies` text DEFAULT NULL,
  `crypto` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: fiat currency, 1: crypto currency',
  `extra` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gateways`
--

INSERT INTO `gateways` (`id`, `form_id`, `code`, `name`, `alias`, `image`, `status`, `gateway_parameters`, `supported_currencies`, `crypto`, `extra`, `description`, `created_at`, `updated_at`) VALUES
(1, 0, 101, 'Paypal', 'Paypal', '663a38d7b455d1715091671.png', 1, '{\"paypal_email\":{\"title\":\"PayPal Email\",\"global\":true,\"value\":\"sb-owud61543012@business.example.com\"}}', '{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2021-05-20 18:04:38'),
(2, 0, 102, 'Perfect Money', 'PerfectMoney', '663a3920e30a31715091744.png', 1, '{\"passphrase\":{\"title\":\"ALTERNATE PASSPHRASE\",\"global\":true,\"value\":\"hR26aw02Q1eEeUPSIfuwNypXX\"},\"wallet_id\":{\"title\":\"PM Wallet\",\"global\":false,\"value\":\"\"}}', '{\"USD\":\"$\",\"EUR\":\"\\u20ac\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2021-05-20 19:35:33'),
(3, 0, 103, 'Stripe Hosted', 'Stripe', '663a39861cb9d1715091846.png', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2021-05-20 18:48:36'),
(4, 0, 104, 'Skrill', 'Skrill', '663a39494c4a91715091785.png', 1, '{\"pay_to_email\":{\"title\":\"Skrill Email\",\"global\":true,\"value\":\"merchant@skrill.com\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"---\"}}', '{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JOD\":\"JOD\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"KWD\":\"KWD\",\"MAD\":\"MAD\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"OMR\":\"OMR\",\"PLN\":\"PLN\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"SAR\":\"SAR\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TND\":\"TND\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\",\"COP\":\"COP\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2021-05-20 19:30:16'),
(5, 0, 105, 'PayTM', 'Paytm', '663a390f601191715091727.png', 1, '{\"MID\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"DIY12386817555501617\"},\"merchant_key\":{\"title\":\"Merchant Key\",\"global\":true,\"value\":\"bKMfNxPPf_QdZppa\"},\"WEBSITE\":{\"title\":\"Paytm Website\",\"global\":true,\"value\":\"DIYtestingweb\"},\"INDUSTRY_TYPE_ID\":{\"title\":\"Industry Type\",\"global\":true,\"value\":\"Retail\"},\"CHANNEL_ID\":{\"title\":\"CHANNEL ID\",\"global\":true,\"value\":\"WEB\"},\"transaction_url\":{\"title\":\"Transaction URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/oltp-web\\/processTransaction\"},\"transaction_status_url\":{\"title\":\"Transaction STATUS URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/paytmchecksum\\/paytmCallback.jsp\"}}', '{\"AUD\":\"AUD\",\"ARS\":\"ARS\",\"BDT\":\"BDT\",\"BRL\":\"BRL\",\"BGN\":\"BGN\",\"CAD\":\"CAD\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"HRK\":\"HRK\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EGP\":\"EGP\",\"EUR\":\"EUR\",\"GEL\":\"GEL\",\"GHS\":\"GHS\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"KES\":\"KES\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"MAD\":\"MAD\",\"NPR\":\"NPR\",\"NZD\":\"NZD\",\"NGN\":\"NGN\",\"NOK\":\"NOK\",\"PKR\":\"PKR\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"ZAR\":\"ZAR\",\"KRW\":\"KRW\",\"LKR\":\"LKR\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"UGX\":\"UGX\",\"UAH\":\"UAH\",\"AED\":\"AED\",\"GBP\":\"GBP\",\"USD\":\"USD\",\"VND\":\"VND\",\"XOF\":\"XOF\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2021-05-20 21:00:44'),
(6, 0, 106, 'Payeer', 'Payeer', '663a38c9e2e931715091657.png', 1, '{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"866989763\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"7575\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"RUB\":\"RUB\"}', 0, '{\"status\":{\"title\": \"Status URL\",\"value\":\"ipn.Payeer\"}}', NULL, '2019-09-14 07:14:22', '2022-08-28 04:11:14'),
(7, 0, 107, 'PayStack', 'Paystack', '663a38fc814e91715091708.png', 1, '{\"public_key\":{\"title\":\"Public key\",\"global\":true,\"value\":\"pk_test_cd330608eb47970889bca397ced55c1dd5ad3783\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"sk_test_8a0b1f199362d7acc9c390bff72c4e81f74e2ac3\"}}', '{\"USD\":\"USD\",\"NGN\":\"NGN\"}', 0, '{\"callback\":{\"title\": \"Callback URL\",\"value\":\"ipn.Paystack\"},\"webhook\":{\"title\": \"Webhook URL\",\"value\":\"ipn.Paystack\"}}\r\n', NULL, '2019-09-14 07:14:22', '2021-05-20 19:49:51'),
(9, 0, 109, 'Flutterwave', 'Flutterwave', '663a36c2c34d61715091138.png', 1, '{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"----------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"-----------------------\"},\"encryption_key\":{\"title\":\"Encryption Key\",\"global\":true,\"value\":\"------------------\"}}', '{\"BIF\":\"BIF\",\"CAD\":\"CAD\",\"CDF\":\"CDF\",\"CVE\":\"CVE\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"GHS\":\"GHS\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"KES\":\"KES\",\"LRD\":\"LRD\",\"MWK\":\"MWK\",\"MZN\":\"MZN\",\"NGN\":\"NGN\",\"RWF\":\"RWF\",\"SLL\":\"SLL\",\"STD\":\"STD\",\"TZS\":\"TZS\",\"UGX\":\"UGX\",\"USD\":\"USD\",\"XAF\":\"XAF\",\"XOF\":\"XOF\",\"ZMK\":\"ZMK\",\"ZMW\":\"ZMW\",\"ZWD\":\"ZWD\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2021-06-05 05:37:45'),
(10, 0, 110, 'RazorPay', 'Razorpay', '663a393a527831715091770.png', 1, '{\"key_id\":{\"title\":\"Key Id\",\"global\":true,\"value\":\"rzp_test_kiOtejPbRZU90E\"},\"key_secret\":{\"title\":\"Key Secret \",\"global\":true,\"value\":\"osRDebzEqbsE1kbyQJ4y0re7\"}}', '{\"INR\":\"INR\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2021-05-20 20:51:32'),
(11, 0, 111, 'Stripe Storefront', 'StripeJs', '663a3995417171715091861.png', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2021-05-20 18:53:10'),
(12, 0, 112, 'Instamojo', 'Instamojo', '663a384d54a111715091533.png', 1, '{\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_2241633c3bc44a3de84a3b33969\"},\"auth_token\":{\"title\":\"Auth Token\",\"global\":true,\"value\":\"test_279f083f7bebefd35217feef22d\"},\"salt\":{\"title\":\"Salt\",\"global\":true,\"value\":\"19d38908eeff4f58b2ddda2c6d86ca25\"}}', '{\"INR\":\"INR\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2021-05-20 20:56:20'),
(13, 0, 501, 'Blockchain', 'Blockchain', '663a35efd0c311715090927.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"55529946-05ca-48ff-8710-f279d86b1cc5\"},\"xpub_code\":{\"title\":\"XPUB CODE\",\"global\":true,\"value\":\"xpub6CKQ3xxWyBoFAF83izZCSFUorptEU9AF8TezhtWeMU5oefjX3sFSBw62Lr9iHXPkXmDQJJiHZeTRtD9Vzt8grAYRhvbz4nEvBu3QKELVzFK\"}}', '{\"BTC\":\"BTC\"}', 1, NULL, NULL, '2019-09-14 07:14:22', '2022-03-21 01:41:56'),
(15, 0, 503, 'CoinPayments', 'Coinpayments', '663a36a8d8e1d1715091112.png', 1, '{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"---------------------\"},\"private_key\":{\"title\":\"Private Key\",\"global\":true,\"value\":\"---------------------\"},\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"---------------------\"}}', '{\"BTC\":\"Bitcoin\",\"BTC.LN\":\"Bitcoin (Lightning Network)\",\"LTC\":\"Litecoin\",\"CPS\":\"CPS Coin\",\"VLX\":\"Velas\",\"APL\":\"Apollo\",\"AYA\":\"Aryacoin\",\"BAD\":\"Badcoin\",\"BCD\":\"Bitcoin Diamond\",\"BCH\":\"Bitcoin Cash\",\"BCN\":\"Bytecoin\",\"BEAM\":\"BEAM\",\"BITB\":\"Bean Cash\",\"BLK\":\"BlackCoin\",\"BSV\":\"Bitcoin SV\",\"BTAD\":\"Bitcoin Adult\",\"BTG\":\"Bitcoin Gold\",\"BTT\":\"BitTorrent\",\"CLOAK\":\"CloakCoin\",\"CLUB\":\"ClubCoin\",\"CRW\":\"Crown\",\"CRYP\":\"CrypticCoin\",\"CRYT\":\"CryTrExCoin\",\"CURE\":\"CureCoin\",\"DASH\":\"DASH\",\"DCR\":\"Decred\",\"DEV\":\"DeviantCoin\",\"DGB\":\"DigiByte\",\"DOGE\":\"Dogecoin\",\"EBST\":\"eBoost\",\"EOS\":\"EOS\",\"ETC\":\"Ether Classic\",\"ETH\":\"Ethereum\",\"ETN\":\"Electroneum\",\"EUNO\":\"EUNO\",\"EXP\":\"EXP\",\"Expanse\":\"Expanse\",\"FLASH\":\"FLASH\",\"GAME\":\"GameCredits\",\"GLC\":\"Goldcoin\",\"GRS\":\"Groestlcoin\",\"KMD\":\"Komodo\",\"LOKI\":\"LOKI\",\"LSK\":\"LSK\",\"MAID\":\"MaidSafeCoin\",\"MUE\":\"MonetaryUnit\",\"NAV\":\"NAV Coin\",\"NEO\":\"NEO\",\"NMC\":\"Namecoin\",\"NVST\":\"NVO Token\",\"NXT\":\"NXT\",\"OMNI\":\"OMNI\",\"PINK\":\"PinkCoin\",\"PIVX\":\"PIVX\",\"POT\":\"PotCoin\",\"PPC\":\"Peercoin\",\"PROC\":\"ProCurrency\",\"PURA\":\"PURA\",\"QTUM\":\"QTUM\",\"RES\":\"Resistance\",\"RVN\":\"Ravencoin\",\"RVR\":\"RevolutionVR\",\"SBD\":\"Steem Dollars\",\"SMART\":\"SmartCash\",\"SOXAX\":\"SOXAX\",\"STEEM\":\"STEEM\",\"STRAT\":\"STRAT\",\"SYS\":\"Syscoin\",\"TPAY\":\"TokenPay\",\"TRIGGERS\":\"Triggers\",\"TRX\":\" TRON\",\"UBQ\":\"Ubiq\",\"UNIT\":\"UniversalCurrency\",\"USDT\":\"Tether USD (Omni Layer)\",\"USDT.BEP20\":\"Tether USD (BSC Chain)\",\"USDT.ERC20\":\"Tether USD (ERC20)\",\"USDT.TRC20\":\"Tether USD (Tron/TRC20)\",\"VTC\":\"Vertcoin\",\"WAVES\":\"Waves\",\"XCP\":\"Counterparty\",\"XEM\":\"NEM\",\"XMR\":\"Monero\",\"XSN\":\"Stakenet\",\"XSR\":\"SucreCoin\",\"XVG\":\"VERGE\",\"XZC\":\"ZCoin\",\"ZEC\":\"ZCash\",\"ZEN\":\"Horizen\"}', 1, NULL, NULL, '2019-09-14 07:14:22', '2023-04-07 21:17:18'),
(16, 0, 504, 'CoinPayments Fiat', 'CoinpaymentsFiat', '663a36b7b841a1715091127.png', 1, '{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"6515561\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2021-05-20 20:07:44'),
(17, 0, 505, 'Coingate', 'Coingate', '663a368e753381715091086.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"6354mwVCEw5kHzRJ6thbGo-N\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2022-03-30 03:24:57'),
(18, 0, 506, 'Coinbase Commerce', 'CoinbaseCommerce', '663a367e46ae51715091070.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"c47cd7df-d8e8-424b-a20a\"},\"secret\":{\"title\":\"Webhook Shared Secret\",\"global\":true,\"value\":\"55871878-2c32-4f64-ab66\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"JPY\":\"JPY\",\"GBP\":\"GBP\",\"AUD\":\"AUD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CNY\":\"CNY\",\"SEK\":\"SEK\",\"NZD\":\"NZD\",\"MXN\":\"MXN\",\"SGD\":\"SGD\",\"HKD\":\"HKD\",\"NOK\":\"NOK\",\"KRW\":\"KRW\",\"TRY\":\"TRY\",\"RUB\":\"RUB\",\"INR\":\"INR\",\"BRL\":\"BRL\",\"ZAR\":\"ZAR\",\"AED\":\"AED\",\"AFN\":\"AFN\",\"ALL\":\"ALL\",\"AMD\":\"AMD\",\"ANG\":\"ANG\",\"AOA\":\"AOA\",\"ARS\":\"ARS\",\"AWG\":\"AWG\",\"AZN\":\"AZN\",\"BAM\":\"BAM\",\"BBD\":\"BBD\",\"BDT\":\"BDT\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"BIF\":\"BIF\",\"BMD\":\"BMD\",\"BND\":\"BND\",\"BOB\":\"BOB\",\"BSD\":\"BSD\",\"BTN\":\"BTN\",\"BWP\":\"BWP\",\"BYN\":\"BYN\",\"BZD\":\"BZD\",\"CDF\":\"CDF\",\"CLF\":\"CLF\",\"CLP\":\"CLP\",\"COP\":\"COP\",\"CRC\":\"CRC\",\"CUC\":\"CUC\",\"CUP\":\"CUP\",\"CVE\":\"CVE\",\"CZK\":\"CZK\",\"DJF\":\"DJF\",\"DKK\":\"DKK\",\"DOP\":\"DOP\",\"DZD\":\"DZD\",\"EGP\":\"EGP\",\"ERN\":\"ERN\",\"ETB\":\"ETB\",\"FJD\":\"FJD\",\"FKP\":\"FKP\",\"GEL\":\"GEL\",\"GGP\":\"GGP\",\"GHS\":\"GHS\",\"GIP\":\"GIP\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"GTQ\":\"GTQ\",\"GYD\":\"GYD\",\"HNL\":\"HNL\",\"HRK\":\"HRK\",\"HTG\":\"HTG\",\"HUF\":\"HUF\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"IMP\":\"IMP\",\"IQD\":\"IQD\",\"IRR\":\"IRR\",\"ISK\":\"ISK\",\"JEP\":\"JEP\",\"JMD\":\"JMD\",\"JOD\":\"JOD\",\"KES\":\"KES\",\"KGS\":\"KGS\",\"KHR\":\"KHR\",\"KMF\":\"KMF\",\"KPW\":\"KPW\",\"KWD\":\"KWD\",\"KYD\":\"KYD\",\"KZT\":\"KZT\",\"LAK\":\"LAK\",\"LBP\":\"LBP\",\"LKR\":\"LKR\",\"LRD\":\"LRD\",\"LSL\":\"LSL\",\"LYD\":\"LYD\",\"MAD\":\"MAD\",\"MDL\":\"MDL\",\"MGA\":\"MGA\",\"MKD\":\"MKD\",\"MMK\":\"MMK\",\"MNT\":\"MNT\",\"MOP\":\"MOP\",\"MRO\":\"MRO\",\"MUR\":\"MUR\",\"MVR\":\"MVR\",\"MWK\":\"MWK\",\"MYR\":\"MYR\",\"MZN\":\"MZN\",\"NAD\":\"NAD\",\"NGN\":\"NGN\",\"NIO\":\"NIO\",\"NPR\":\"NPR\",\"OMR\":\"OMR\",\"PAB\":\"PAB\",\"PEN\":\"PEN\",\"PGK\":\"PGK\",\"PHP\":\"PHP\",\"PKR\":\"PKR\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"RWF\":\"RWF\",\"SAR\":\"SAR\",\"SBD\":\"SBD\",\"SCR\":\"SCR\",\"SDG\":\"SDG\",\"SHP\":\"SHP\",\"SLL\":\"SLL\",\"SOS\":\"SOS\",\"SRD\":\"SRD\",\"SSP\":\"SSP\",\"STD\":\"STD\",\"SVC\":\"SVC\",\"SYP\":\"SYP\",\"SZL\":\"SZL\",\"THB\":\"THB\",\"TJS\":\"TJS\",\"TMT\":\"TMT\",\"TND\":\"TND\",\"TOP\":\"TOP\",\"TTD\":\"TTD\",\"TWD\":\"TWD\",\"TZS\":\"TZS\",\"UAH\":\"UAH\",\"UGX\":\"UGX\",\"UYU\":\"UYU\",\"UZS\":\"UZS\",\"VEF\":\"VEF\",\"VND\":\"VND\",\"VUV\":\"VUV\",\"WST\":\"WST\",\"XAF\":\"XAF\",\"XAG\":\"XAG\",\"XAU\":\"XAU\",\"XCD\":\"XCD\",\"XDR\":\"XDR\",\"XOF\":\"XOF\",\"XPD\":\"XPD\",\"XPF\":\"XPF\",\"XPT\":\"XPT\",\"YER\":\"YER\",\"ZMW\":\"ZMW\",\"ZWL\":\"ZWL\"}\r\n\r\n', 0, '{\"endpoint\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.CoinbaseCommerce\"}}', NULL, '2019-09-14 07:14:22', '2021-05-20 20:02:47'),
(24, 0, 113, 'Paypal Express', 'PaypalSdk', '663a38ed101a61715091693.png', 1, '{\"clientId\":{\"title\":\"Paypal Client ID\",\"global\":true,\"value\":\"Ae0-tixtSV7DvLwIh3Bmu7JvHrjh5EfGdXr_cEklKAVjjezRZ747BxKILiBdzlKKyp-W8W_T7CKH1Ken\"},\"clientSecret\":{\"title\":\"Client Secret\",\"global\":true,\"value\":\"EOhbvHZgFNO21soQJT1L9Q00M3rK6PIEsdiTgXRBt2gtGtxwRer5JvKnVUGNU5oE63fFnjnYY7hq3HBA\"}}', '{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2021-05-20 17:01:08'),
(25, 0, 114, 'Stripe Checkout', 'StripeV3', '663a39afb519f1715091887.png', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"},\"end_point\":{\"title\":\"End Point Secret\",\"global\":true,\"value\":\"whsec_lUmit1gtxwKTveLnSe88xCSDdnPOt8g5\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, '{\"webhook\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.StripeV3\"}}', NULL, '2019-09-14 07:14:22', '2021-05-20 18:58:38'),
(27, 0, 115, 'Mollie', 'Mollie', '663a387ec69371715091582.png', 1, '{\"mollie_email\":{\"title\":\"Mollie Email \",\"global\":true,\"value\":\"vi@gmail.com\"},\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_cucfwKTWfft9s337qsVfn5CC4vNkrn\"}}', '{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}', 0, NULL, NULL, '2019-09-14 07:14:22', '2021-05-20 20:44:45'),
(30, 0, 116, 'Cashmaal', 'Cashmaal', '663a361b16bd11715090971.png', 1, '{\"web_id\":{\"title\":\"Web Id\",\"global\":true,\"value\":\"3748\"},\"ipn_key\":{\"title\":\"IPN Key\",\"global\":true,\"value\":\"546254628759524554647987\"}}', '{\"PKR\":\"PKR\",\"USD\":\"USD\"}', 0, '{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.Cashmaal\"}}', NULL, NULL, '2021-06-22 02:05:04'),
(36, 0, 119, 'Mercado Pago', 'MercadoPago', '663a386c714a91715091564.png', 1, '{\"access_token\":{\"title\":\"Access Token\",\"global\":true,\"value\":\"APP_USR-7924565816849832-082312-21941521997fab717db925cf1ea2c190-1071840315\"}}', '{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}', 0, NULL, NULL, NULL, '2022-09-14 01:41:14'),
(37, 0, 120, 'Authorize.net', 'Authorize', '663a35b9ca5991715090873.png', 1, '{\"login_id\":{\"title\":\"Login ID\",\"global\":true,\"value\":\"59e4P9DBcZv\"},\"transaction_key\":{\"title\":\"Transaction Key\",\"global\":true,\"value\":\"47x47TJyLw2E7DbR\"}}', '{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}', 0, NULL, NULL, NULL, '2024-05-13 00:11:32'),
(46, 0, 121, 'NMI', 'NMI', '663a3897754cf1715091607.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"2F822Rw39fx762MaV7Yy86jXGTC7sCDy\"}}', '{\"AED\":\"AED\",\"ARS\":\"ARS\",\"AUD\":\"AUD\",\"BOB\":\"BOB\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"RUB\":\"RUB\",\"SEC\":\"SEC\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}', 0, NULL, NULL, NULL, '2022-08-28 04:32:31'),
(50, 0, 507, 'BTCPay', 'BTCPay', '663a35cd25a8d1715090893.png', 1, '{\"store_id\":{\"title\":\"Store Id\",\"global\":true,\"value\":\"HsqFVTXSeUFJu7caoYZc3CTnP8g5LErVdHhEXPVTheHf\"},\"api_key\":{\"title\":\"Api Key\",\"global\":true,\"value\":\"4436bd706f99efae69305e7c4eff4780de1335ce\"},\"server_name\":{\"title\":\"Server Name\",\"global\":true,\"value\":\"https:\\/\\/testnet.demo.btcpayserver.org\"},\"secret_code\":{\"title\":\"Secret Code\",\"global\":true,\"value\":\"SUCdqPn9CDkY7RmJHfpQVHP2Lf2\"}}', '{\"BTC\":\"Bitcoin\",\"LTC\":\"Litecoin\"}', 1, '{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.BTCPay\"}}', NULL, NULL, '2024-05-13 00:12:00'),
(51, 0, 508, 'Now payments hosted', 'NowPaymentsHosted', '663a38b8d57a81715091640.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"--------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"------------\"}}', '{\"BTG\":\"BTG\",\"ETH\":\"ETH\",\"XMR\":\"XMR\",\"ZEC\":\"ZEC\",\"XVG\":\"XVG\",\"ADA\":\"ADA\",\"LTC\":\"LTC\",\"BCH\":\"BCH\",\"QTUM\":\"QTUM\",\"DASH\":\"DASH\",\"XLM\":\"XLM\",\"XRP\":\"XRP\",\"XEM\":\"XEM\",\"DGB\":\"DGB\",\"LSK\":\"LSK\",\"DOGE\":\"DOGE\",\"TRX\":\"TRX\",\"KMD\":\"KMD\",\"REP\":\"REP\",\"BAT\":\"BAT\",\"ARK\":\"ARK\",\"WAVES\":\"WAVES\",\"BNB\":\"BNB\",\"XZC\":\"XZC\",\"NANO\":\"NANO\",\"TUSD\":\"TUSD\",\"VET\":\"VET\",\"ZEN\":\"ZEN\",\"GRS\":\"GRS\",\"FUN\":\"FUN\",\"NEO\":\"NEO\",\"GAS\":\"GAS\",\"PAX\":\"PAX\",\"USDC\":\"USDC\",\"ONT\":\"ONT\",\"XTZ\":\"XTZ\",\"LINK\":\"LINK\",\"RVN\":\"RVN\",\"BNBMAINNET\":\"BNBMAINNET\",\"ZIL\":\"ZIL\",\"BCD\":\"BCD\",\"USDT\":\"USDT\",\"USDTERC20\":\"USDTERC20\",\"CRO\":\"CRO\",\"DAI\":\"DAI\",\"HT\":\"HT\",\"WABI\":\"WABI\",\"BUSD\":\"BUSD\",\"ALGO\":\"ALGO\",\"USDTTRC20\":\"USDTTRC20\",\"GT\":\"GT\",\"STPT\":\"STPT\",\"AVA\":\"AVA\",\"SXP\":\"SXP\",\"UNI\":\"UNI\",\"OKB\":\"OKB\",\"BTC\":\"BTC\"}', 1, '', NULL, NULL, '2023-02-13 23:08:23'),
(52, 0, 509, 'Now payments checkout', 'NowPaymentsCheckout', '663a38a59d2541715091621.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"---------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"-----------\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\"}', 1, '', NULL, NULL, '2023-02-13 23:08:04'),
(53, 0, 122, '2Checkout', 'TwoCheckout', '663a39b8e64b91715091896.png', 1, '{\"merchant_code\":{\"title\":\"Merchant Code\",\"global\":true,\"value\":\"253248016872\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"eQM)ID@&vG84u!O*g[p+\"}}', '{\"AFN\": \"AFN\",\"ALL\": \"ALL\",\"DZD\": \"DZD\",\"ARS\": \"ARS\",\"AUD\": \"AUD\",\"AZN\": \"AZN\",\"BSD\": \"BSD\",\"BDT\": \"BDT\",\"BBD\": \"BBD\",\"BZD\": \"BZD\",\"BMD\": \"BMD\",\"BOB\": \"BOB\",\"BWP\": \"BWP\",\"BRL\": \"BRL\",\"GBP\": \"GBP\",\"BND\": \"BND\",\"BGN\": \"BGN\",\"CAD\": \"CAD\",\"CLP\": \"CLP\",\"CNY\": \"CNY\",\"COP\": \"COP\",\"CRC\": \"CRC\",\"HRK\": \"HRK\",\"CZK\": \"CZK\",\"DKK\": \"DKK\",\"DOP\": \"DOP\",\"XCD\": \"XCD\",\"EGP\": \"EGP\",\"EUR\": \"EUR\",\"FJD\": \"FJD\",\"GTQ\": \"GTQ\",\"HKD\": \"HKD\",\"HNL\": \"HNL\",\"HUF\": \"HUF\",\"INR\": \"INR\",\"IDR\": \"IDR\",\"ILS\": \"ILS\",\"JMD\": \"JMD\",\"JPY\": \"JPY\",\"KZT\": \"KZT\",\"KES\": \"KES\",\"LAK\": \"LAK\",\"MMK\": \"MMK\",\"LBP\": \"LBP\",\"LRD\": \"LRD\",\"MOP\": \"MOP\",\"MYR\": \"MYR\",\"MVR\": \"MVR\",\"MRO\": \"MRO\",\"MUR\": \"MUR\",\"MXN\": \"MXN\",\"MAD\": \"MAD\",\"NPR\": \"NPR\",\"TWD\": \"TWD\",\"NZD\": \"NZD\",\"NIO\": \"NIO\",\"NOK\": \"NOK\",\"PKR\": \"PKR\",\"PGK\": \"PGK\",\"PEN\": \"PEN\",\"PHP\": \"PHP\",\"PLN\": \"PLN\",\"QAR\": \"QAR\",\"RON\": \"RON\",\"RUB\": \"RUB\",\"WST\": \"WST\",\"SAR\": \"SAR\",\"SCR\": \"SCR\",\"SGD\": \"SGD\",\"SBD\": \"SBD\",\"ZAR\": \"ZAR\",\"KRW\": \"KRW\",\"LKR\": \"LKR\",\"SEK\": \"SEK\",\"CHF\": \"CHF\",\"SYP\": \"SYP\",\"THB\": \"THB\",\"TOP\": \"TOP\",\"TTD\": \"TTD\",\"TRY\": \"TRY\",\"UAH\": \"UAH\",\"AED\": \"AED\",\"USD\": \"USD\",\"VUV\": \"VUV\",\"VND\": \"VND\",\"XOF\": \"XOF\",\"YER\": \"YER\"}', 0, '{\"approved_url\":{\"title\": \"Approved URL\",\"value\":\"ipn.TwoCheckout\"}}', NULL, NULL, '2023-04-29 03:21:58'),
(54, 0, 123, 'Checkout', 'Checkout', '663a3628733351715090984.png', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"------\"},\"public_key\":{\"title\":\"PUBLIC KEY\",\"global\":true,\"value\":\"------\"},\"processing_channel_id\":{\"title\":\"PROCESSING CHANNEL\",\"global\":true,\"value\":\"------\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"AUD\":\"AUD\",\"CAN\":\"CAN\",\"CHF\":\"CHF\",\"SGD\":\"SGD\",\"JPY\":\"JPY\",\"NZD\":\"NZD\"}', 0, NULL, NULL, NULL, '2023-05-06 01:43:01'),
(56, 0, 510, 'Binance', 'Binance', '663a35db4fd621715090907.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"tsu3tjiq0oqfbtmlbevoeraxhfbp3brejnm9txhjxcp4to29ujvakvfl1ibsn3ja\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"jzngq4t04ltw8d4iqpi7admfl8tvnpehxnmi34id1zvfaenbwwvsvw7llw3zdko8\"},\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"231129033\"}}', '{\"BTC\":\"Bitcoin\",\"USD\":\"USD\",\"BNB\":\"BNB\"}', 1, '{\"cron\":{\"title\": \"Cron Job URL\",\"value\":\"ipn.Binance\"}}', NULL, NULL, '2023-02-13 23:08:04'),
(57, 0, 124, 'SslCommerz', 'SslCommerz', '663a397a70c571715091834.png', 1, '{\"store_id\": {\"title\": \"Store ID\",\"global\": true,\"value\": \"---------\"},\"store_password\": {\"title\": \"Store Password\",\"global\": true,\"value\": \"----------\"}}', '{\"BDT\":\"BDT\",\"USD\":\"USD\",\"EUR\":\"EUR\",\"SGD\":\"SGD\",\"INR\":\"INR\",\"MYR\":\"MYR\"}', 0, NULL, NULL, NULL, '2023-05-06 01:43:01'),
(58, 0, 125, 'Aamarpay', 'Aamarpay', '663a34d5d1dfc1715090645.png', 1, '{\"store_id\":{\"title\":\"Store ID\",\"global\":true,\"value\":\"---------\"},\"signature_key\":{\"title\":\"Signature Key\",\"global\":true,\"value\":\"----------\"}}', '{\"BDT\":\"BDT\"}', 0, NULL, NULL, NULL, '2024-05-13 00:10:02'),
(59, 6, 1000, 'Bank Transfer', 'bank_transfer', '664849335dd041716013363.png', 1, '[]', '[]', 0, NULL, '<div style=\"border-left: 3px solid #b5b0b0;\r\n    padding: 12px;\r\n    font-style: italic;\r\n    margin: 30px 0px;\r\n    background: #f9f9f9;\r\n    border-radius: 3px;\"><p style=\"\r\n    margin-bottom: 10px;\r\n    font-weight: bold;\r\n    font-size: 17px;\r\n\">Send the amount to the below bank information</p><p style=\"\r\n    margin-bottom: 0;\r\n\">\r\n</p><p style=\"\r\nmargin-bottom: 0;\r\n\"><span style=\"font-weight:500\">Bank Name:</span> Bank of America</p>\r\n<p style=\"\r\nmargin-bottom: 0;\r\n\"><span style=\"font-weight:500\">Branch:</span> New York</p>\r\n<p style=\"\r\nmargin-bottom: 0;\r\n\"><span style=\"font-weight:500\">Routing:</span> 1234</p>\r\n<p style=\"\r\n    margin-bottom: 0;\r\n\"><span style=\"font-weight:500\">Account Number:</span> 997855xxxxxxxx6985</p></div>', '2024-05-17 18:22:44', '2024-05-17 18:22:44');

-- --------------------------------------------------------

--
-- Table structure for table `gateway_currencies`
--

CREATE TABLE `gateway_currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `currency` varchar(40) DEFAULT NULL,
  `symbol` varchar(40) DEFAULT NULL,
  `method_code` int(10) DEFAULT NULL,
  `gateway_alias` varchar(40) DEFAULT NULL,
  `min_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `max_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `percent_charge` decimal(5,2) NOT NULL DEFAULT 0.00,
  `fixed_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `gateway_parameter` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_settings`
--

CREATE TABLE `general_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `site_name` varchar(40) DEFAULT NULL,
  `cur_text` varchar(40) DEFAULT NULL COMMENT 'currency text',
  `cur_sym` varchar(40) DEFAULT NULL COMMENT 'currency symbol',
  `email_from` varchar(40) DEFAULT NULL,
  `email_from_name` varchar(255) DEFAULT NULL,
  `email_template` text DEFAULT NULL,
  `sms_template` varchar(255) DEFAULT NULL,
  `sms_from` varchar(255) DEFAULT NULL,
  `push_title` varchar(255) DEFAULT NULL,
  `push_template` varchar(255) DEFAULT NULL,
  `base_color` varchar(40) DEFAULT NULL,
  `mail_config` text DEFAULT NULL COMMENT 'email configuration',
  `sms_config` text DEFAULT NULL,
  `firebase_config` text DEFAULT NULL,
  `push_configuration` text DEFAULT NULL,
  `global_shortcodes` text DEFAULT NULL,
  `kv` tinyint(1) NOT NULL DEFAULT 0,
  `ev` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'email verification, 0 - dont check, 1 - check',
  `en` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'email notification, 0 - dont send, 1 - send',
  `sv` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'mobile verication, 0 - dont check, 1 - check',
  `sn` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'sms notification, 0 - dont send, 1 - send',
  `pn` tinyint(1) NOT NULL DEFAULT 0,
  `force_ssl` tinyint(1) NOT NULL DEFAULT 0,
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  `secure_password` tinyint(1) NOT NULL DEFAULT 0,
  `agree` tinyint(1) NOT NULL DEFAULT 0,
  `multi_language` tinyint(1) NOT NULL DEFAULT 1,
  `registration` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Off	, 1: On',
  `active_template` varchar(40) DEFAULT NULL,
  `system_customized` tinyint(1) NOT NULL DEFAULT 0,
  `paginate_number` int(11) NOT NULL DEFAULT 0,
  `currency_format` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>Both\r\n2=>Text Only\r\n3=>Symbol Only',
  `last_cron` datetime DEFAULT NULL,
  `available_version` varchar(40) DEFAULT NULL,
  `socialite_credentials` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `general_settings`
--

INSERT INTO `general_settings` (`id`, `site_name`, `cur_text`, `cur_sym`, `email_from`, `email_from_name`, `email_template`, `sms_template`, `sms_from`, `push_title`, `push_template`, `base_color`, `mail_config`, `sms_config`, `firebase_config`, `push_configuration`, `global_shortcodes`, `kv`, `ev`, `en`, `sv`, `sn`, `pn`, `force_ssl`, `maintenance_mode`, `secure_password`, `agree`, `multi_language`, `registration`, `active_template`, `system_customized`, `paginate_number`, `currency_format`, `last_cron`, `available_version`, `socialite_credentials`, `created_at`, `updated_at`) VALUES
(1, 'Rapidlab', 'USD', '$', 'info@viserlab.com', '{{site_name}}', '<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.ibb.co/rw2fTRM/logo-dark.png\" width=\"220\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello {{fullname}} ({{username}})</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\">{{message}}</td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          © 2024 <a href=\"#\">{{site_name}}</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>', 'hi {{fullname}} ({{username}}), {{message}}', 'ViserAdmin', '{{site_name}}', 'hi {{fullname}} ({{username}}), {{message}}', '024059', '{\"name\":\"php\"}', '{\"name\":\"nexmo\",\"clickatell\":{\"api_key\":\"----------------\"},\"infobip\":{\"username\":\"------------8888888\",\"password\":\"-----------------\"},\"message_bird\":{\"api_key\":\"-------------------\"},\"nexmo\":{\"api_key\":\"----------------------\",\"api_secret\":\"----------------------\"},\"sms_broadcast\":{\"username\":\"----------------------\",\"password\":\"-----------------------------\"},\"twilio\":{\"account_sid\":\"-----------------------\",\"auth_token\":\"---------------------------\",\"from\":\"----------------------\"},\"text_magic\":{\"username\":\"-----------------------\",\"apiv2_key\":\"-------------------------------\"},\"custom\":{\"method\":\"get\",\"url\":\"https:\\/\\/hostname\\/demo-api-v1\",\"headers\":{\"name\":[\"api_key\"],\"value\":[\"test_api 555\"]},\"body\":{\"name\":[\"from_number\"],\"value\":[\"5657545757\"]}}}', NULL, NULL, '{\n    \"site_name\":\"Name of your site\",\n    \"site_currency\":\"Currency of your site\",\n    \"currency_symbol\":\"Symbol of currency\"\n}', 0, 1, 1, 0, 1, 0, 0, 0, 0, 1, 1, 1, 'basic', 0, 20, 1, '2024-05-23 09:21:48', '0', '{\"google\":{\"client_id\":\"-------------\",\"client_secret\":\"----------------\",\"status\":1},\"facebook\":{\"client_id\":\"-------------\",\"client_secret\":\"--------------\",\"status\":1},\"linkedin\":{\"client_id\":\"-----------\",\"client_secret\":\"-------------\",\"status\":1}}', NULL, '2024-05-23 03:31:19');

-- --------------------------------------------------------

--
-- Table structure for table `installments`
--

CREATE TABLE `installments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` int(10) UNSIGNED NOT NULL,
  `delay_charge` decimal(28,8) UNSIGNED NOT NULL DEFAULT 0.00000000,
  `installment_date` date DEFAULT NULL,
  `given_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `code` varchar(40) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: not default language, 1: default language',
  `image` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `code`, `is_default`, `image`, `created_at`, `updated_at`) VALUES
(1, 'English', 'en', 1, '664dfcd2eeb2f1716387026.png', '2020-07-06 03:47:55', '2024-05-22 08:10:27'),
(11, 'Spanish', 'es', 0, '664dfce35f4911716387043.png', '2024-05-22 08:10:43', '2024-05-22 08:10:43'),
(12, 'Franch', 'fr', 0, '664dfd03612e71716387075.png', '2024-05-22 08:11:15', '2024-05-22 08:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_number` varchar(40) DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `per_installment` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `installment_interval` int(11) NOT NULL DEFAULT 0 COMMENT 'Days',
  `delay_value` int(11) NOT NULL DEFAULT 1,
  `charge_per_installment` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `delay_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `given_installment` int(11) NOT NULL DEFAULT 0,
  `total_installment` int(11) NOT NULL DEFAULT 0,
  `application_form` text DEFAULT NULL,
  `admin_feedback` text DEFAULT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 = Pending, 1 = Running, 2 = Paid, 3 = Rejected',
  `due_notification_sent` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_plans`
--

CREATE TABLE `loan_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(10) NOT NULL DEFAULT 0,
  `form_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(40) NOT NULL,
  `title` varchar(40) DEFAULT NULL,
  `minimum_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `maximum_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `per_installment` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT '%',
  `installment_interval` int(11) NOT NULL DEFAULT 0 COMMENT 'In Day',
  `total_installment` int(11) NOT NULL DEFAULT 0,
  `application_fixed_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000 COMMENT 'Fixed Application charge ',
  `application_percent_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000 COMMENT 'Percent Application charge ',
  `instruction` text DEFAULT NULL,
  `delay_value` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `fixed_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `percent_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1->Featured , 0-> Non-Featured',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_logs`
--

CREATE TABLE `notification_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sender` varchar(40) DEFAULT NULL,
  `sent_from` varchar(40) DEFAULT NULL,
  `sent_to` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `notification_type` varchar(40) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `user_read` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_templates`
--

CREATE TABLE `notification_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `act` varchar(40) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `push_title` varchar(255) DEFAULT NULL,
  `email_body` text DEFAULT NULL,
  `sms_body` text DEFAULT NULL,
  `push_body` text DEFAULT NULL,
  `shortcodes` text DEFAULT NULL,
  `email_status` tinyint(1) NOT NULL DEFAULT 1,
  `email_sent_from_name` varchar(40) DEFAULT NULL,
  `email_sent_from_address` varchar(40) DEFAULT NULL,
  `sms_status` tinyint(1) NOT NULL DEFAULT 1,
  `sms_sent_from` varchar(40) DEFAULT NULL,
  `push_status` tinyint(1) NOT NULL DEFAULT 0,
  `push_notification_body` text DEFAULT NULL,
  `push_notification_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_templates`
--

INSERT INTO `notification_templates` (`id`, `act`, `name`, `subject`, `push_title`, `email_body`, `sms_body`, `push_body`, `shortcodes`, `email_status`, `email_sent_from_name`, `email_sent_from_address`, `sms_status`, `sms_sent_from`, `push_status`, `push_notification_body`, `push_notification_status`, `created_at`, `updated_at`) VALUES
(1, 'BAL_ADD', 'Balance - Added', 'Your Account has been Credited', NULL, '<div><div style=\"font-family: Montserrat, sans-serif;\">{{amount}} {{site_currency}} has been added to your account .</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><span style=\"color: rgb(33, 37, 41); font-family: Montserrat, sans-serif;\">Your Current Balance is :&nbsp;</span><font style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">{{post_balance}}&nbsp; {{site_currency}}&nbsp;</span></font><br></div><div><font style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></font></div><div>Admin note:&nbsp;<span style=\"color: rgb(33, 37, 41); font-size: 12px; font-weight: 600; white-space: nowrap; text-align: var(--bs-body-text-align);\">{{remark}}</span></div>', '{{amount}} {{site_currency}} credited in your account. Your Current Balance {{post_balance}} {{site_currency}} . Transaction: #{{trx}}. Admin note is \"{{remark}}\"', NULL, '{\"trx\":\"Transaction number for the action\",\"amount\":\"Amount inserted by the admin\",\"remark\":\"Remark inserted by the admin\",\"post_balance\":\"Balance of the user after this transaction\"}', 1, NULL, NULL, 0, NULL, 0, '{{amount}} {{site_currency}} has been added to your account', 1, '2021-11-03 12:00:00', '2022-04-03 02:18:28'),
(2, 'BAL_SUB', 'Balance - Subtracted', 'Your Account has been Debited', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">{{amount}} {{site_currency}} has been subtracted from your account .</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><span style=\"color: rgb(33, 37, 41); font-family: Montserrat, sans-serif;\">Your Current Balance is :&nbsp;</span><font style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">{{post_balance}}&nbsp; {{site_currency}}</span></font><br><div><font style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></font></div><div>Admin Note: {{remark}}</div>', '{{amount}} {{site_currency}} debited from your account. Your Current Balance {{post_balance}} {{site_currency}} . Transaction: #{{trx}}. Admin Note is {{remark}}', NULL, '{\"trx\":\"Transaction number for the action\",\"amount\":\"Amount inserted by the admin\",\"remark\":\"Remark inserted by the admin\",\"post_balance\":\"Balance of the user after this transaction\"}', 1, NULL, NULL, 1, NULL, 0, '{{amount}} {{site_currency}} has been subtracted from your account', 1, '2021-11-03 12:00:00', '2022-04-03 02:24:11'),
(3, 'DEPOSIT_COMPLETE', 'Deposit - Automated - Successful', 'Deposit Completed Successfully', NULL, '<div>Your deposit of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>has been completed Successfully.<span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge:&nbsp;<font color=\"#000000\">{{charge}} {{site_currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Received : {{method_amount}} {{method_currency}}<br></div><div>Paid via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><font size=\"5\"><span style=\"font-weight: bolder;\"><br></span></font></div><div><font size=\"5\">Your current Balance is&nbsp;<span style=\"font-weight: bolder;\">{{post_balance}} {{site_currency}}</span></font></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>', '{{amount}} {{site_currency}} Deposit successfully by {{method_name}}', NULL, '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after this transaction\"}', 1, NULL, NULL, 1, NULL, 0, '{{amount}} {{site_currency}} deposited successfully via  {{method_name}}', 1, '2021-11-03 12:00:00', '2022-04-03 02:25:43'),
(4, 'DEPOSIT_APPROVE', 'Deposit - Manual - Approved', 'Your Deposit is Approved', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>is Approved .<span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Amount : {{amount}} {{site_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Received : {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Paid via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"5\"><span style=\"font-weight: bolder;\"><br></span></font></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"5\">Your current Balance is&nbsp;<span style=\"font-weight: bolder;\">{{post_balance}} {{site_currency}}</span></font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div>', 'Admin Approve Your {{amount}} {{site_currency}} payment request by {{method_name}} transaction : {{trx}}', NULL, '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after this transaction\"}', 1, NULL, NULL, 1, NULL, 0, '{{amount}} {{site_currency}} deposit request via  {{method_name}} approved successfully', 1, '2021-11-03 12:00:00', '2022-04-03 02:26:07'),
(5, 'DEPOSIT_REJECT', 'Deposit - Manual - Rejected', 'Your Deposit Request is Rejected', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}} has been rejected</span>.<span style=\"font-weight: bolder;\"><br></span></div><div><br></div><div><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Received : {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Paid via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge: {{charge}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number was : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">if you have any queries, feel free to contact us.<br></div><br style=\"font-family: Montserrat, sans-serif;\"><div style=\"font-family: Montserrat, sans-serif;\"><br><br></div><span style=\"color: rgb(33, 37, 41); font-family: Montserrat, sans-serif;\">{{rejection_message}}</span><br>', 'Admin Rejected Your {{amount}} {{site_currency}} payment request by {{method_name}}\r\n\r\n{{rejection_message}}', NULL, '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"rejection_message\":\"Rejection message by the admin\"}', 1, NULL, NULL, 1, NULL, 0, 'Your deposit request of {{amount}} {{site_currency}} via {{method_name}} is rejected', 1, '2021-11-03 12:00:00', '2022-04-05 03:45:27'),
(6, 'DEPOSIT_REQUEST', 'Deposit - Manual - Requested', 'Deposit Request Submitted Successfully', NULL, '<div>Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>submitted successfully<span style=\"font-weight: bolder;\">&nbsp;.<br></span></div><div><span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Payable : {{method_amount}} {{method_currency}}<br></div><div>Pay via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>', '{{amount}} {{site_currency}} Deposit requested by {{method_name}}. Charge: {{charge}} . Trx: {{trx}}', NULL, '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\"}', 1, NULL, NULL, 1, NULL, 0, 'Your deposit request of {{amount}} {{site_currency}} via {{method_name}} is submitted successfully', 1, '2021-11-03 12:00:00', '2022-04-03 02:29:19'),
(7, 'PASS_RESET_CODE', 'Password - Reset - Code', 'Password Reset', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">We have received a request to reset the password for your account on&nbsp;<span style=\"font-weight: bolder;\">{{time}} .<br></span></div><div style=\"font-family: Montserrat, sans-serif;\">Requested From IP:&nbsp;<span style=\"font-weight: bolder;\">{{ip}}</span>&nbsp;using&nbsp;<span style=\"font-weight: bolder;\">{{browser}}</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{operating_system}}&nbsp;</span>.</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><br style=\"font-family: Montserrat, sans-serif;\"><div style=\"font-family: Montserrat, sans-serif;\"><div>Your account recovery code is:&nbsp;&nbsp;&nbsp;<font size=\"6\"><span style=\"font-weight: bolder;\">{{code}}</span></font></div><div><br></div></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"4\" color=\"#CC0000\">If you do not wish to reset your password, please disregard this message.&nbsp;</font><br></div><div><font size=\"4\" color=\"#CC0000\"><br></font></div>', 'Your account recovery code is: {{code}}', NULL, '{\"code\":\"Verification code for password reset\",\"ip\":\"IP address of the user\",\"browser\":\"Browser of the user\",\"operating_system\":\"Operating system of the user\",\"time\":\"Time of the request\"}', 1, NULL, NULL, 0, NULL, 0, 'Your account recovery code is: {{code}}', 1, '2021-11-03 12:00:00', '2022-03-20 20:47:05'),
(8, 'PASS_RESET_DONE', 'Password - Reset - Confirmation', 'You have reset your password', NULL, '<p style=\"font-family: Montserrat, sans-serif;\">You have successfully reset your password.</p><p style=\"font-family: Montserrat, sans-serif;\">You changed from&nbsp; IP:&nbsp;<span style=\"font-weight: bolder;\">{{ip}}</span>&nbsp;using&nbsp;<span style=\"font-weight: bolder;\">{{browser}}</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{operating_system}}&nbsp;</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{time}}</span></p><p style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></p><p style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><font color=\"#ff0000\">If you did not change that, please contact us as soon as possible.</font></span></p>', 'Your password has been changed successfully', NULL, '{\"ip\":\"IP address of the user\",\"browser\":\"Browser of the user\",\"operating_system\":\"Operating system of the user\",\"time\":\"Time of the request\"}', 1, NULL, NULL, 1, NULL, 0, 'Your password has been changed successfully', 1, '2021-11-03 12:00:00', '2022-04-05 03:46:35'),
(9, 'ADMIN_SUPPORT_REPLY', 'Support - Reply', 'Reply Support Ticket', NULL, '<div><p><span data-mce-style=\"font-size: 11pt;\" style=\"font-size: 11pt;\"><span style=\"font-weight: bolder;\">A member from our support team has replied to the following ticket:</span></span></p><p><span style=\"font-weight: bolder;\"><span data-mce-style=\"font-size: 11pt;\" style=\"font-size: 11pt;\"><span style=\"font-weight: bolder;\"><br></span></span></span></p><p><span style=\"font-weight: bolder;\">[Ticket#{{ticket_id}}] {{ticket_subject}}<br><br>Click here to reply:&nbsp; {{link}}</span></p><p>----------------------------------------------</p><p>Here is the reply :<br></p><p>{{reply}}<br></p></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>', 'Your Ticket#{{ticket_id}} :  {{ticket_subject}} has been replied.', NULL, '{\"ticket_id\":\"ID of the support ticket\",\"ticket_subject\":\"Subject  of the support ticket\",\"reply\":\"Reply made by the admin\",\"link\":\"URL to view the support ticket\"}', 1, NULL, NULL, 1, NULL, 0, 'Your Ticket#{{ticket_id}} :  {{ticket_subject}} has been replied.', 1, '2021-11-03 12:00:00', '2022-03-20 20:47:51'),
(10, 'EVER_CODE', 'Verification - Email', 'Please verify your email address', NULL, '<br><div><div style=\"font-family: Montserrat, sans-serif;\">Thanks For joining us.<br></div><div style=\"font-family: Montserrat, sans-serif;\">Please use the below code to verify your email address.<br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Your email verification code is:<font size=\"6\"><span style=\"font-weight: bolder;\">&nbsp;{{code}}</span></font></div></div>', '---', NULL, '{\"code\":\"Email verification code\"}', 1, NULL, NULL, 0, NULL, 0, NULL, 1, '2021-11-03 12:00:00', '2022-04-03 02:32:07'),
(11, 'SVER_CODE', 'Verification - SMS', 'Verify Your Mobile Number', NULL, '---', 'Your phone verification code is: {{code}}', NULL, '{\"code\":\"SMS Verification Code\"}', 0, NULL, NULL, 1, NULL, 0, 'Your mobile verification code is: {{code}}', 1, '2021-11-03 12:00:00', '2022-03-20 19:24:37'),
(12, 'WITHDRAW_APPROVE', 'Withdraw - Approved', 'Withdraw Request has been Processed and your money is sent', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">Your withdraw request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp; via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>has been Processed Successfully.<span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">Details of your withdraw:<br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Amount : {{amount}} {{site_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">You will get: {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">-----</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"4\">Details of Processed Payment :</font></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"4\"><span style=\"font-weight: bolder;\">{{admin_details}}</span></font></div>', 'Admin Approve Your {{amount}} {{site_currency}} withdraw request by {{method_name}}. Transaction {{trx}}', NULL, '{\"trx\":\"Transaction number for the withdraw\",\"amount\":\"Amount requested by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the withdraw method\",\"method_currency\":\"Currency of the withdraw method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"admin_details\":\"Details provided by the admin\"}', 1, NULL, NULL, 1, NULL, 0, 'Your withdraw request of {{amount}} {{site_currency}} via {{method_name}} has been approved', 1, '2021-11-03 12:00:00', '2022-03-20 20:50:16'),
(13, 'WITHDRAW_REJECT', 'Withdraw - Rejected', 'Withdraw Request has been Rejected and your money is refunded to your account', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">Your withdraw request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp; via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>has been Rejected.<span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">Details of your withdraw:<br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Amount : {{amount}} {{site_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">You should get: {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">----</div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"3\"><br></font></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"3\">{{amount}} {{currency}} has been&nbsp;<span style=\"font-weight: bolder;\">refunded&nbsp;</span>to your account and your current Balance is&nbsp;<span style=\"font-weight: bolder;\">{{post_balance}}</span><span style=\"font-weight: bolder;\">&nbsp;{{site_currency}}</span></font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">-----</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"4\">Details of Rejection :</font></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"4\"><span style=\"font-weight: bolder;\">{{admin_details}}</span></font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br><br><br><br><br></div><div></div><div></div>', 'Admin Rejected Your {{amount}} {{site_currency}} withdraw request. Your Main Balance {{post_balance}}  {{method_name}} , Transaction {{trx}}', NULL, '{\"trx\":\"Transaction number for the withdraw\",\"amount\":\"Amount requested by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the withdraw method\",\"method_currency\":\"Currency of the withdraw method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after fter this action\",\"admin_details\":\"Rejection message by the admin\"}', 1, NULL, NULL, 1, NULL, 0, 'Your withdraw request of {{amount}} {{site_currency}} via {{method_name}} has been rejected', 1, '2021-11-03 12:00:00', '2022-03-20 20:57:46'),
(14, 'WITHDRAW_REQUEST', 'Withdraw - Requested', 'Withdraw Request Submitted Successfully', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">Your withdraw request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp; via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>has been submitted Successfully.<span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">Details of your withdraw:<br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Amount : {{amount}} {{site_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">You will get: {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"5\">Your current Balance is&nbsp;<span style=\"font-weight: bolder;\">{{post_balance}} {{site_currency}}</span></font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br><br><br></div>', '{{amount}} {{site_currency}} withdraw requested by {{method_name}}. You will get {{method_amount}} {{method_currency}} Trx: {{trx}}', NULL, '{\"trx\":\"Transaction number for the withdraw\",\"amount\":\"Amount requested by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the withdraw method\",\"method_currency\":\"Currency of the withdraw method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after fter this transaction\"}', 1, NULL, NULL, 1, NULL, 0, 'Your withdraw request of {{amount}} {{site_currency}} via {{method_name}} submitted successfully', 1, '2021-11-03 12:00:00', '2022-03-21 04:39:03'),
(15, 'DEFAULT', 'Default Template', '{{subject}}', NULL, '{{message}}', '{{message}}', NULL, '{\"subject\":\"Subject\",\"message\":\"Message\"}', 1, NULL, NULL, 1, NULL, 0, '{{message}}', 1, '2019-09-14 13:14:22', '2021-11-04 09:38:55'),
(16, 'KYC_APPROVE', 'KYC Approved', 'KYC has been approved', NULL, NULL, NULL, NULL, '[]', 1, NULL, NULL, 1, NULL, 0, 'Your kyc verification completed successfully', 1, NULL, NULL),
(17, 'KYC_REJECT', 'KYC Rejected', 'KYC has been rejected', NULL, NULL, NULL, NULL, '{\"reason\":\"Rejection Reason\"}', 1, NULL, NULL, 1, NULL, 0, 'Your kyc verification has been rejected', 1, NULL, NULL),
(18, 'LOAN_APPROVE', 'Loan Approved Successfully', 'Loan Approved Successfully', NULL, '<div>Your request for a loan is approved successfully<br><br>&nbsp;Your {{plan_name}} </div>', 'Your {{plan_name}}', NULL, '{\n     \"plan_name\": \"Plan name\",\n     \"loan_number\": \"Loan number\",\n     \"amount\": \"Loan amount\",\n     \"per_installment\": \"Installment amount\",\n     \"payable_amount\": \"Payable amount for user\",\n     \"installment_interval\": \"How many days in an installment\",\n     \"delay_value\": \"How many days of delay the charge will be applied\",\n     \"charge_per_installment\": \"Installment delay charge for each day\",\n     \"delay_charge\": \"Total installment delay charge\",\n     \"given_installment\": \"How many installments are given by the user\",\n     \"total_installment\": \"How many installments for this plan\",\n     \"next_installment_date\": \"Next installment date\"\n}', 1, NULL, NULL, 1, NULL, 0, 'Your loan application approved successfully', 1, NULL, NULL),
(19, 'LOAN_REJECT', 'Loan Request Rejected', 'Loan Request Rejected', NULL, '<div> Your application for a loan is rejected by the authority <br>{{plan_name}} </div>', 'Your {{plan_name}}', NULL, '{\r\n     \"plan_name\": \"Plan name\",\r\n     \"loan_number\": \"Loan number\",\r\n     \"amount\": \"Loan amount\",\r\n     \"per_installment\": \"Installment amount\",\r\n     \"payable_amount\": \"Payable amount for user\",\r\n     \"installment_interval\": \"How many days in an installment\",\r\n     \"delay_value\": \"How many days of delay the charge will be applied\",\r\n     \"charge_per_installment\": \"Installment delay charge for each day\",\r\n     \"delay_charge\": \"Total installment delay charge\",\r\n     \"given_installment\": \"How many installments are given by the user\",\r\n     \"total_installment\": \"How many installments for this plan\",\r\n     \"reason_of_rejection\": \"Reason of rejection\"\r\n}', 1, NULL, NULL, 1, NULL, 0, 'Your loan application has been rejected', 1, NULL, NULL),
(20, 'LOAN_INSTALLMENT_DUE', 'Loan Installment Due', 'Loan installment due', NULL, '<div>Please recharge your balance for installment&nbsp;<br><br>{{plan_name}} </div>', 'Your {{plan_name}}', NULL, '{\r\n     \"loan_number\": \"Loan number\",\r\n     \"amount\": \"Loan amount\",\r\n     \"per_installment\": \"Installment amount\",\r\n     \"payable_amount\": \"Payable amount for user\",\r\n     \"installment_interval\": \"How many days in an installment\",\r\n     \"delay_value\": \"How many days of delay the charge will be applied\",\r\n     \"charge_per_installment\": \"Installment delay charge for each day\",\r\n     \"delay_charge\": \"Total installment delay charge\",\r\n     \"given_installment\": \"How many installments are given by the user\",\r\n     \"total_installment\": \"How many installments for this plan\",\r\n     \"installment_date\": \"Installment date\"\r\n}', 1, NULL, NULL, 1, NULL, 0, 'Loan installment due', 1, NULL, NULL),
(21, 'LOAN_PAID', 'Loan Paid', 'Loan completed', NULL, '<div> You paid all installment<br>{{plan_name}} </div>', 'Your {{plan_name}}', NULL, '{\r\n     \"plan_name\": \"Plan name\",\r\n     \"loan_number\": \"Loan number\",\r\n     \"amount\": \"Loan amount\",\r\n     \"per_installment\": \"Installment amount\",\r\n     \"payable_amount\": \"Payable amount for user\",\r\n     \"installment_interval\": \"How many days in an installment\",\r\n     \"delay_value\": \"How many days of delay the charge will be applied\",\r\n     \"charge_per_installment\": \"Installment delay charge for each day\",\r\n     \"delay_charge\": \"Total installment delay charge\",\r\n     \"given_installment\": \"How many installments are given by the user\",\r\n     \"total_installment\": \"How many installments for this plan\"\r\n}', 1, NULL, NULL, 1, NULL, 0, 'Loan installment paid', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `slug` varchar(40) DEFAULT NULL,
  `tempname` varchar(40) DEFAULT NULL COMMENT 'template name',
  `secs` text DEFAULT NULL,
  `seo_content` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `name`, `slug`, `tempname`, `secs`, `seo_content`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'HOME', '/', 'templates.basic.', '[\"client\",\"about\",\"feature\",\"plan\",\"counter\",\"faq\",\"testimonial\",\"blog\"]', NULL, 1, '2020-07-11 06:23:58', '2023-08-02 08:50:52'),
(4, 'Blog', 'blog', 'templates.basic.', NULL, NULL, 1, '2020-10-22 01:14:43', '2020-10-22 01:14:43'),
(5, 'Contact', 'contact', 'templates.basic.', NULL, NULL, 1, '2020-10-22 01:14:53', '2020-10-22 01:14:53'),
(19, 'About', 'about', 'templates.basic.', '[\"about\"]', NULL, 0, '2020-10-22 01:14:53', '2023-07-30 07:32:28');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(40) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_attachments`
--

CREATE TABLE `support_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `support_message_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_messages`
--

CREATE TABLE `support_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `support_ticket_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `message` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) DEFAULT 0,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `ticket` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Open, 1: Answered, 2: Replied, 3: Closed',
  `priority` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Low, 2 = medium, 3 = heigh',
  `last_reply` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `post_balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `trx_type` varchar(40) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `remark` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `update_logs`
--

CREATE TABLE `update_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(40) DEFAULT NULL,
  `update_log` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firstname` varchar(40) DEFAULT NULL,
  `lastname` varchar(40) DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `email` varchar(40) NOT NULL,
  `dial_code` varchar(40) DEFAULT NULL,
  `country_code` varchar(40) DEFAULT NULL,
  `mobile` varchar(40) DEFAULT NULL,
  `ref_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `password` varchar(255) NOT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0: banned, 1: active',
  `kyc_data` text DEFAULT NULL,
  `kyc_rejection_reason` varchar(255) DEFAULT NULL,
  `kv` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: KYC Unverified, 2: KYC pending, 1: KYC verified',
  `ev` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: email unverified, 1: email verified',
  `sv` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: mobile unverified, 1: mobile verified',
  `profile_complete` tinyint(1) NOT NULL DEFAULT 0,
  `ver_code` varchar(40) DEFAULT NULL COMMENT 'stores verification code',
  `ver_code_send_at` datetime DEFAULT NULL COMMENT 'verification send time',
  `ts` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: 2fa off, 1: 2fa on',
  `tv` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0: 2fa unverified, 1: 2fa verified',
  `tsc` varchar(255) DEFAULT NULL,
  `ban_reason` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `login_by` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_logins`
--

CREATE TABLE `user_logins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_ip` varchar(40) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `country` varchar(40) DEFAULT NULL,
  `country_code` varchar(40) DEFAULT NULL,
  `longitude` varchar(40) DEFAULT NULL,
  `latitude` varchar(40) DEFAULT NULL,
  `browser` varchar(40) DEFAULT NULL,
  `os` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `remark` varchar(40) DEFAULT NULL,
  `click_value` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `method_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `currency` varchar(40) DEFAULT NULL,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `trx` varchar(40) DEFAULT NULL,
  `final_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `after_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `withdraw_information` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>success, 2=>pending, 3=>cancel,  ',
  `admin_feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_methods`
--

CREATE TABLE `withdraw_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(40) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `min_limit` decimal(28,8) DEFAULT 0.00000000,
  `max_limit` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `fixed_charge` decimal(28,8) DEFAULT 0.00000000,
  `rate` decimal(28,8) DEFAULT 0.00000000,
  `percent_charge` decimal(5,2) DEFAULT NULL,
  `currency` varchar(40) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`,`username`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_jobs`
--
ALTER TABLE `cron_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_job_logs`
--
ALTER TABLE `cron_job_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_schedules`
--
ALTER TABLE `cron_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device_tokens`
--
ALTER TABLE `device_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `extensions`
--
ALTER TABLE `extensions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontends`
--
ALTER TABLE `frontends`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gateways`
--
ALTER TABLE `gateways`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `gateway_currencies`
--
ALTER TABLE `gateway_currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `general_settings`
--
ALTER TABLE `general_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `installments`
--
ALTER TABLE `installments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loan_number` (`loan_number`);

--
-- Indexes for table `loan_plans`
--
ALTER TABLE `loan_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_templates`
--
ALTER TABLE `notification_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_attachments`
--
ALTER TABLE `support_attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `update_logs`
--
ALTER TABLE `update_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`,`email`);

--
-- Indexes for table `user_logins`
--
ALTER TABLE `user_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdraw_methods`
--
ALTER TABLE `withdraw_methods`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cron_jobs`
--
ALTER TABLE `cron_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cron_job_logs`
--
ALTER TABLE `cron_job_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cron_schedules`
--
ALTER TABLE `cron_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_tokens`
--
ALTER TABLE `device_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `extensions`
--
ALTER TABLE `extensions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `frontends`
--
ALTER TABLE `frontends`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `gateways`
--
ALTER TABLE `gateways`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `gateway_currencies`
--
ALTER TABLE `gateway_currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_settings`
--
ALTER TABLE `general_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `installments`
--
ALTER TABLE `installments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_plans`
--
ALTER TABLE `loan_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_logs`
--
ALTER TABLE `notification_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_templates`
--
ALTER TABLE `notification_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_attachments`
--
ALTER TABLE `support_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `update_logs`
--
ALTER TABLE `update_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_logins`
--
ALTER TABLE `user_logins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdraw_methods`
--
ALTER TABLE `withdraw_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
