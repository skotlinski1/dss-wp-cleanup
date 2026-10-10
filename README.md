# DSS — Cleanup

Własny MU-plugin dla WordPressa (`dss-wp-cleanup.php`, paczka Composera `dss/wp-cleanup`), który usuwa wyjście
i zasoby rdzenia WordPressa, których strona nie potrzebuje, a które kosztują bajty, żądania albo pracę
serwera. To narzędzie na własne potrzeby, bez gwarancji zgodności z innymi konfiguracjami.

Bloki rdzenia wyłącza `dss-no-blocks`, zbędne funkcje WooCommerce `dss-lean-woocommerce`, a to, co pomaga
atakującemu (meta `generator`, XML-RPC, dane o autorach), `dss-wp-hardening`.

**Dokumentacja:** [docs/](docs/README.md). Instalacja przez Composera w projekcie DSS WP Manage:
[COMPOSER.md](COMPOSER.md).

## Co robi

- Nie wypisuje skryptu wykrywającego emoji ani stylów emoji (front, panel, widok osadzenia) i nie zamienia
  emoji na obrazki w kanałach RSS i e-mailach.
- Nie wypisuje w `<head>` linków RSD, shortlink, odkrywania REST API i oEmbed ani nagłówków `Link` shortlink
  i REST API. REST API działa dalej.
- Nie publikuje kanałów RSS i Atom: adresy kanałów (`/feed/`, `/comments/feed/`, `?feed=` itd.) dają 404, a
  linki do nich znikają z `<head>`.

Każdy hak z powodem i opcja nieaktywna (jQuery Migrate):
[docs/DZIALANIE.md](docs/DZIALANIE.md).

## Wymagania

Najnowszy stabilny WordPress (7.0+), PHP 8.3+. Szczegóły: [docs/INSTALACJA.md](docs/INSTALACJA.md#wymagania).

## Instalacja

Skopiuj `dss-wp-cleanup.php` do `wp-content/mu-plugins/` (MU-plugin nie wymaga aktywacji) albo zainstaluj
Composerem w projekcie DSS WP Manage ([COMPOSER.md](COMPOSER.md)). Wyłącznik awaryjny w `wp-config.php` przed
`wp-settings.php`:

```php
define('DSS_WP_CLEANUP_DISABLED', true);
```

## Pomiary w skrócie

Twenty Twenty na WordPressie 7.1.3 z `dss-no-blocks`: strona główna 24,3 → 20,4 KB, wpis 30,9 → 26,5 KB HTML
(9 znaczników mniej), czas żądania bez mierzalnej różnicy. Szczegóły: [docs/POMIARY.md](docs/POMIARY.md).

## Licencja

GPL-2.0-or-later.
