# Instalacja i wdrożenie

## Wymagania

- **WordPress:** najnowszy stabilny (7.0+). Wtyczka nie sprawdza wersji.
- **PHP 8.3+** (wymaga tego `composer.json`). Składnię sprawdza CI na PHP 8.3 i 8.5.

## Instalacja ręczna

Skopiuj `dss-wp-cleanup.php` do `wp-content/mu-plugins/`. MU-plugin nie wymaga aktywacji. W katalogu
MU-pluginów może być tylko jedna kopia pliku: druga zadeklarowałaby te same funkcje w przestrzeni nazw
`DSS\Cleanup` i zakończyła każde żądanie błędem krytycznym.

## Instalacja przez Composera

W projekcie DSS WP Manage paczka `dss/wp-cleanup` (typ `wordpress-muplugin`, wersja z tagu Git bez `v`) jest
instalowana do katalogu MU-pluginów i ładowana przez loader DSS. Instrukcja krok po kroku:
[COMPOSER.md](../COMPOSER.md).

## Wyłącznik awaryjny

W `wp-config.php` przed `wp-settings.php`:

```php
define('DSS_WP_CLEANUP_DISABLED', true);
```

Wtyczka niczego wtedy nie rejestruje i niczego nie zmienia w bazie, więc strona wraca do zachowania rdzenia.
Usunięcie linii przywraca działanie. Ta sama stała służy do porównań z wtyczką i bez niej.

## Sprawdzenie po wdrożeniu

Po wdrożeniu i po każdej większej aktualizacji WordPressa:

1. W źródle wpisu nie ma `wp-emoji`, `EditURI`, `rel='shortlink'`, `https://api.w.org/` ani `json+oembed`.
   Jeśli któreś jest, wtyczka nie działa albo strona ma w cache starą wersję (wyczyść cache i odśwież).
2. Odpięcia są na miejscu. `remove_action` na powiązaniu, którego w nowym WordPressie już nie ma, nie zgłasza
   błędu, więc brak błędów PHP niczego nie potwierdza. Sprawdź wprost:

   ```bash
   wp eval 'var_dump(has_action("wp_head", "print_emoji_detection_script"), has_action("wp_head", "rsd_link"), has_action("wp_head", "wp_oembed_add_discovery_links"));'
   ```

   Każda wartość ma być `false`. Z `--exec='define("DSS_WP_CLEANUP_DISABLED", true);'` to samo polecenie
   pokazuje priorytety z rdzenia (7, 10 i 4); inne liczby znaczą, że rdzeń zmienił priorytet i odpięcie trzeba
   poprawić.

3. Kanały RSS są wyłączone: `curl -sI https://twoja-domena.pl/feed/` daje `404`, a w źródle strony nie ma
   `application/rss+xml`.

Lista wszystkich haków z priorytetami: [DZIALANIE.md](DZIALANIE.md#haki-aktywne).
