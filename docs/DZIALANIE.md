# Działanie

Zakres (SRP): wyjście i zasoby rdzenia WordPressa, których strona nie potrzebuje, a które kosztują bajty,
żądania albo pracę serwera. Podział między wtyczki DSS: sekcja 5
[`.github/CONTRIBUTING.md`](../.github/CONTRIBUTING.md#5-zakres-i-podział-między-wtyczki).

Aktywne są tylko odpięcia rzeczy, z których strona nie korzysta. Odpięcie, które może coś odebrać (jQuery
Migrate), jest w kodzie zakomentowane, z powodem i warunkiem włączenia. Wszystkie haki rejestruje
`configure()` na `plugins_loaded`; haki panelu dopiero na `admin_init`, bo rdzeń dodaje je w
`wp-admin/includes/admin-filters.php` po `plugins_loaded`. Nazwy i priorytety sprawdzone w kodzie WordPressa
7.1.3.

## Haki aktywne

### Emoji

Przeglądarki same wyświetlają emoji, więc skrypt wykrywający, style `img.emoji` i zamiana emoji na obrazki z
`s.w.org` są zbędne.

| Hak | Funkcja rdzenia | Priorytet | Co znika |
|---|---|---|---|
| `wp_head` | `print_emoji_detection_script` | 7 | ustawienia i skrypt wykrywający emoji w stopce strony |
| `embed_head` | `print_emoji_detection_script` | 10 | to samo w widoku osadzenia (`/wpis/embed/`) |
| `admin_print_scripts` | `print_emoji_detection_script` | 10 | to samo w panelu (odpięte na `admin_init`) |
| `wp_enqueue_scripts` | `wp_enqueue_emoji_styles` | 10 | styl `wp-emoji-styles-inline-css` |
| `enqueue_embed_scripts` | `wp_enqueue_emoji_styles` | 10 | to samo w widoku osadzenia |
| `admin_enqueue_scripts` | `wp_enqueue_emoji_styles` | 10 | to samo w panelu (odpięte na `admin_init`) |
| `wp_print_styles`, `admin_print_styles` | `print_emoji_styles` | 10 | przestarzała wersja stylów, którą rdzeń woła, gdy brakuje `wp_enqueue_emoji_styles` |
| `the_content_feed`, `comment_text_rss` | `wp_staticize_emoji` | 10 | zamiana emoji na `<img>` z `s.w.org` w kanałach RSS |
| `wp_mail` | `wp_staticize_emoji_for_email` | 10 | zamiana emoji na `<img>` w e-mailach |
| `tiny_mce_plugins` (filtr) | | 10 | wtyczka `wpemoji` klasycznego edytora |

Treść z emoji wyświetla się dalej: w HTML, kanałach i e-mailach zostaje znak emoji zamiast obrazka.

### Linki w `<head>` i nagłówki `Link`

| Hak | Funkcja rdzenia | Priorytet | Co znika |
|---|---|---|---|
| `wp_head` | `rsd_link` | 10 | `<link rel="EditURI">` do `xmlrpc.php?rsd` (klienty XML-RPC) |
| `wp_head` | `wp_shortlink_wp_head` | 10 | `<link rel="shortlink">` (`?p=ID`) |
| `template_redirect` | `wp_shortlink_header` | 11 | nagłówek `Link: …; rel=shortlink` |
| `wp_head` | `rest_output_link_wp_head` | 10 | `<link rel="https://api.w.org/">` i `<link rel="alternate" type="application/json">` |
| `template_redirect` | `rest_output_link_header` | 11 | nagłówek `Link: …; rel="https://api.w.org/"` |
| `wp_head` | `wp_oembed_add_discovery_links` | 4 i 10 | dwa linki odkrywania oEmbed (JSON i XML) na wpisach i stronach |

Rdzeń podpina odkrywanie oEmbed dwa razy (priorytet 4 i 10, ten drugi dla zgodności), więc wtyczka odpina oba.
REST API działa dalej (także `/wp-json/oembed/1.0/embed`), a osadzanie treści innych serwisów (YouTube itd.) w
tej stronie nie zależy od tych linków. Inne strony WordPressa nie znajdą jednak automatycznie podglądu wpisów
tej strony przy wklejeniu linku.

### Kanały RSS i Atom

Strona nie publikuje kanałów (nikt z nich nie korzysta), a każdy adres kanału to dynamiczne zapytanie, które
boty kopiujące treść mogą odpytywać bez końca.

| Hak | Funkcja | Priorytet | Co robi |
|---|---|---|---|
| `wp_head` | `feed_links` | 2 | znikają linki do kanału głównego i kanału komentarzy |
| `wp_head` | `feed_links_extra` | 3 | znikają linki do kanałów kategorii, wpisu i komentarzy wpisu |
| `template_redirect` | `disable_feed_request` | 1 | adresy kanałów dają 404 ze stroną 404 motywu |

`disable_feed_request` działa przy każdym żądaniu kanału (`/feed/`, `/feed/atom/`, `/feed/rdf/`,
`/comments/feed/`, `/wpis/feed/`, kanały kategorii, autorów i wyszukiwania, `?feed=`). Robi pięć rzeczy: woła
`set_404()` i zeruje `is_feed` oraz `is_comment_feed` (`set_404()` zostawia `is_feed`, więc bez tego rdzeń nadal
budowałby kanał, tylko z kodem 404), ustawia kod 404 i nagłówki bez cache, przywraca `Content-Type` HTML
(rdzeń ustawia typ kanału wcześniej, w `WP::send_headers()`) i wyłącza zgadywanie adresu dla 404
(`do_redirect_guess_404_permalink`), żeby `/wpis/feed/` nie przekierowywało na wpis. Priorytet 1 jest przed
`redirect_canonical` (10).

Czytniki RSS i automatyzacje, które subskrybowały kanał, dostają 404. Mapa witryny (`wp-sitemap.xml`), REST API,
logowanie i panel działają bez zmian. Jeśli strona kiedyś ma publikować kanał, usuń te trzy odpięcia z
`configure_feeds()`.

## Opcje nieaktywne

| Opcja | Co robi | Włącz, jeśli |
|---|---|---|
| jQuery Migrate | usuwa `jquery-migrate` z zależności `jquery` na froncie (panel bez zmian) przez `remove_jquery_migrate()` na `wp_default_scripts` | konsola przeglądarki nie pokazuje ostrzeżeń `JQMIGRATE` na stronach sklepu (koszyk, kasa, produkt) i w motywie |

Funkcję `remove_jquery_migrate()` sprawdzono bez włączania haka: na WordPressie 7.1.3 zmienia zależności
`jquery` z `jquery-core, jquery-migrate` na `jquery-core`.

## Czego wtyczka nie robi

| Temat | Gdzie |
|---|---|
| Gutenberg i bloki rdzenia | `dss-no-blocks` |
| zbędne funkcje i zasoby WooCommerce | `dss-lean-woocommerce` (repo `dss-disable-woo-bloatware`) |
| meta `generator`, XML-RPC, dane o autorach (pomagają atakującemu) | `dss-wp-hardening` |
| dane SEO | `dss-wp-seo` |
| obrazy i ich warianty | `dss-media-suite` |

Do decyzji, poza kodem: Heartbeat w panelu (autozapis i blokada edycji wpisu korzystają z niego) i skrypt
`wp-embed` (rdzeń dołącza go tylko na stronach, które osadzają wpis innej strony WordPressa, więc zwykle go
nie ma).

`wlwmanifest_link` nie jest już podpięty w rdzeniu (funkcja jest w `wp-includes/deprecated.php`), więc wtyczka
go nie odpina.
