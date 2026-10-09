# Działanie

## Stan

Wersja 0.1.0 nie rejestruje żadnych haków: `configure()` na `plugins_loaded` jest pusta. Strona działa tak
samo z wtyczką i bez niej.

## Zakres

Zakres (SRP): wyjście i zasoby rdzenia WordPressa, których strona nie potrzebuje, a które kosztują bajty,
żądania albo pracę serwera. Podział między wtyczki DSS: sekcja 5
[`.github/CONTRIBUTING.md`](../.github/CONTRIBUTING.md#5-zakres-i-podział-między-wtyczki).

## Plan

Tematy, które mają tu trafić. To plan, a nie opis działania: nic z tej tabeli nie jest jeszcze napisane ani
sprawdzone. Każdy temat przed napisaniem kodu sprawdzamy w kodzie rdzenia WordPressa (nazwy i priorytety
haków), a po napisaniu na prawdziwym WordPressie.

| Temat | Co |
|---|---|
| **emoji** | skrypt wykrywający, style i filtry zamieniające emoji na obrazki |
| **oEmbed** | linki discovery w `<head>` i skrypt `wp-embed` |
| **linki w `<head>`** | RSD, `wlwmanifest`, shortlink, link do REST API |
| **RSS** | kanały i linki do nich |
| **jQuery Migrate** | na froncie, gdy motyw i wtyczki go nie potrzebują |
| **Heartbeat** | poza ekranem edycji |

## Czego wtyczka nie robi

| Temat | Gdzie |
|---|---|
| Gutenberg i bloki rdzenia | `dss-no-blocks` |
| zbędne funkcje i zasoby WooCommerce | `dss-lean-woocommerce` (repo `dss-disable-woo-bloatware`) |
| meta `generator`, XML-RPC, dane o autorach (pomagają atakującemu) | `dss-wp-security` |
| dane SEO | `dss-wp-seo` |
| obrazy i ich warianty | `dss-media-suite` |
