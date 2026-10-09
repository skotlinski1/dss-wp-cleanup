# Pomiary

## Środowisko i metoda

WordPress 7.1.3, PHP 8.3, MariaDB, motyw Twenty Twenty, bez WooCommerce. Wtyczka podłączona jako MU-plugin
(`require_once` pliku z repo), porównanie z wyłącznikiem `DSS_WP_CLEANUP_DISABLED`. Żądania frontu z CLI
(`wp-blog-header.php`), rozmiar HTML z pełnego wyjścia procesu.

## Wynik

Razem z `dss-no-blocks` 3.0.1 (tak jak na stronie), bloki-widżety przeniesione do nieaktywnych:

| Strona | Bez wtyczki | Z wtyczką | Różnica |
|---|---|---|---|
| strona główna | 24 311 B | 20 377 B | −3 934 B (−16%) |
| wpis | 30 884 B | 26 452 B | −4 432 B (−14%) |

Na wpisie znika 9 znaczników `<link>`, `<script>`, `<style>` i `<meta>` (z 27 na 18): 2 linki oEmbed, styl
emoji, 2 linki REST API, RSD, shortlink i 2 skrypty emoji (ustawienia i moduł wykrywający).

Bez `dss-no-blocks` (z domyślnymi widżetami) różnica w bajtach jest taka sama: strona główna
52 746 → 48 812 B, wpis 57 858 → 53 426 B. Widok osadzenia: 20 120 → 16 357 B. Kokpit w panelu:
88 109 → 84 382 B.

Czas żądania: bez mierzalnej różnicy (mediana 15 żądań wpisu z CLI 252 i 257 ms, w granicach rozrzutu). Skrypt
wykrywający emoji pobiera dodatkowy plik tylko w przeglądarce bez obsługi emoji, więc w nowych przeglądarkach
zysk to bajty HTML i praca skryptu, a nie liczba żądań.

## Jak powtórzyć pomiar na swojej stronie

Porównaj źródło strony z wtyczką i z `define('DSS_WP_CLEANUP_DISABLED', true);` w `wp-config.php`: rozmiar
HTML i liczba znaczników w `<head>` i przed `</body>`. Wynik zależy od motywu i wtyczek strony.
