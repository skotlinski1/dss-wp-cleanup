<?php
/**
 * Plugin Name: DSS — Cleanup
 * Description: Usuwa zbędne wyjście rdzenia WordPressa (emoji, oEmbed, RSS, linki w head). MU-plugin.
 * Version: 0.1.0
 * License: GPL-2.0-or-later
 *
 * Własny MU-plugin: zakłada aktualne stabilne WordPress (7.0+) oraz PHP 8.3+.
 * Opis, instalacja i plan: README.md i docs/ w repozytorium.
 *
 * Zakres (SRP): wyjście i zasoby rdzenia WordPressa, których strona nie potrzebuje, a które kosztują
 * bajty, żądania albo pracę serwera (emoji, oEmbed, RSS, zbędne linki w <head>, jQuery Migrate,
 * Heartbeat). Pytanie kontrolne: „dlaczego usuwamy?”. Bo zbędne albo ciężkie: tutaj. Bo pomaga
 * atakującemu: dss-wp-security. Bloki rdzenia wyłącza dss-no-blocks, a zbędne funkcje WooCommerce
 * dss-lean-woocommerce.
 */

declare(strict_types=1);

namespace DSS\Cleanup;

if (!defined('ABSPATH')) {
	exit;
}

// Wyłącznik awaryjny: define('DSS_WP_CLEANUP_DISABLED', true); w wp-config.php, przed wp-settings.php.
// Nic nie rejestruje i niczego nie zmienia w bazie. Służy do szybkiego wyłączenia i do porównań A/B.
if (defined('DSS_WP_CLEANUP_DISABLED') && DSS_WP_CLEANUP_DISABLED) {
	return;
}

// Po wczytaniu wszystkich wtyczek: haki rejestruje configure().
add_action('plugins_loaded', __NAMESPACE__ . '\\configure');

/** Rejestruje haki wtyczki. Na razie żadnych: wtyczka niczego nie zmienia. */
function configure(): void
{
}
