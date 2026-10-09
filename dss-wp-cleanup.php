<?php
/**
 * Plugin Name: DSS — Cleanup
 * Description: Usuwa zbędne wyjście rdzenia WordPressa (emoji, oEmbed, RSS, linki w head). MU-plugin.
 * Version: 0.2.0
 * License: GPL-2.0-or-later
 *
 * Własny MU-plugin: zakłada aktualne stabilne WordPress (7.0+) oraz PHP 8.3+.
 * Opis haków, pomiary i instalacja: README.md i docs/ w repozytorium.
 *
 * Zakres (SRP): wyjście i zasoby rdzenia WordPressa, których strona nie potrzebuje, a które kosztują
 * bajty, żądania albo pracę serwera (emoji, oEmbed, RSS, zbędne linki w <head>, jQuery Migrate,
 * Heartbeat). Pytanie kontrolne: „dlaczego usuwamy?”. Bo zbędne albo ciężkie: tutaj. Bo pomaga
 * atakującemu: dss-wp-security. Bloki rdzenia wyłącza dss-no-blocks, a zbędne funkcje WooCommerce
 * dss-lean-woocommerce.
 *
 * Zasada: aktywne są tylko odpięcia rzeczy, z których strona nie korzysta. Zakomentowane linie
 * to opcje nieaktywne: nad każdą jest komentarz blokowy z powodem i sytuacją, w której warto ją
 * włączyć; sam zakomentowany kod jest w liniach `//`, a opcje oddziela pusta linia.
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

/** Rejestruje odpięcia; każdy temat ma własną funkcję. */
function configure(): void
{
	configure_emoji();
	configure_head_links();
	configure_feeds();
	configure_scripts();
}

/**
 * Emoji: przeglądarki same wyświetlają emoji, więc skrypt wykrywający (w stopce), style `img.emoji` i
 * zamiana emoji na obrazki z s.w.org w kanałach i e-mailach są zbędne. Front, panel i widok osadzenia.
 */
function configure_emoji(): void
{
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('embed_head', 'print_emoji_detection_script');

	// Style emoji. Bez wp_enqueue_emoji_styles() rdzeń dalej woła przestarzałe print_emoji_styles(),
	// więc odpinamy oba.
	remove_action('wp_enqueue_scripts', 'wp_enqueue_emoji_styles');
	remove_action('enqueue_embed_scripts', 'wp_enqueue_emoji_styles');
	remove_action('wp_print_styles', 'print_emoji_styles');

	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

	// Haki panelu dodaje wp-admin/includes/admin-filters.php dopiero po plugins_loaded.
	add_action('admin_init', __NAMESPACE__ . '\\remove_admin_emoji');

	// Wtyczka TinyMCE w klasycznym edytorze zamienia emoji na obrazki; bez skryptu wykrywającego jest zbędna.
	add_filter('tiny_mce_plugins', __NAMESPACE__ . '\\remove_tinymce_emoji');
}

/** Emoji w panelu: skrypt wykrywający i style. */
function remove_admin_emoji(): void
{
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('admin_enqueue_scripts', 'wp_enqueue_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
}

/**
 * Usuwa wtyczkę `wpemoji` z listy wtyczek TinyMCE.
 *
 * @param mixed $plugins Lista nazw wtyczek TinyMCE.
 * @return mixed
 */
function remove_tinymce_emoji($plugins)
{
	if (!is_array($plugins)) {
		return $plugins;
	}

	return array_values(array_diff($plugins, ['wpemoji']));
}

/**
 * Linki w `<head>` i nagłówki `Link`, z których strona nie korzysta: RSD (klienty XML-RPC), shortlink
 * (`?p=ID`), odkrywanie REST API i oEmbed (osadzanie wpisów tej strony na innych stronach). REST API i
 * osadzanie treści z innych serwisów w tej stronie działają dalej.
 */
function configure_head_links(): void
{
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wp_shortlink_wp_head', 10);
	remove_action('template_redirect', 'wp_shortlink_header', 11);
	remove_action('wp_head', 'rest_output_link_wp_head', 10);
	remove_action('template_redirect', 'rest_output_link_header', 11);

	// Rdzeń podpina odkrywanie oEmbed dwa razy (priorytet 4 i 10, ten drugi dla zgodności).
	remove_action('wp_head', 'wp_oembed_add_discovery_links', 4);
	remove_action('wp_head', 'wp_oembed_add_discovery_links');
}

/** Kanały RSS: zostają, dopóki nie wiadomo, czy strona je publikuje. */
function configure_feeds(): void
{
	/* Linki do kanałów RSS w <head> (główny, komentarzy, kategorii i komentarzy wpisu). Same kanały pod
	 * /feed/ działają dalej. Włącz, jeśli strona nie publikuje kanałów RSS. */
	// remove_action('wp_head', 'feed_links', 2);
	// remove_action('wp_head', 'feed_links_extra', 3);
}

/** Skrypty rdzenia na froncie. */
function configure_scripts(): void
{
	/* jQuery Migrate na froncie: zgodność ze starym kodem jQuery. Rdzeń dołącza go do każdego `jquery`,
	 * a WooCommerce na froncie używa jQuery. Włącz, jeśli konsola przeglądarki nie pokazuje ostrzeżeń
	 * JQMIGRATE na stronach sklepu (koszyk, kasa, produkt) i w motywie. */
	// add_action('wp_default_scripts', __NAMESPACE__ . '\\remove_jquery_migrate');
}

/**
 * Usuwa `jquery-migrate` z zależności `jquery` na froncie (panel bez zmian).
 *
 * @param \WP_Scripts $scripts Rejestr skryptów.
 */
function remove_jquery_migrate(\WP_Scripts $scripts): void
{
	if (is_admin() || !isset($scripts->registered['jquery'])) {
		return;
	}

	$jquery = $scripts->registered['jquery'];
	$jquery->deps = array_values(array_diff($jquery->deps, ['jquery-migrate']));
}
