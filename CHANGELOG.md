# Historia zmian

Format wzorowany na [Keep a Changelog](https://keepachangelog.com/pl/1.1.0/), wersjonowanie
[SemVer](https://semver.org/lang/pl/). Najnowsze wpisy na górze. Tagi bez przedrostka `v`, numer taki sam jak
`Version:` w `dss-wp-cleanup.php`. Zmiany tylko w plikach spoza paczki (dokumentacja, `.github/`) nie dostają
numeru: wspomina je najbliższe wydanie.

## 0.3.0 — 2026-10-10

- Kanały RSS i Atom wyłączone: adresy kanałów (`/feed/`, `/comments/feed/`, kanały kategorii, wpisu, autorów i
  wyszukiwania, `?feed=`) dają 404, a linki do nich znikają z `<head>`. Czytniki RSS i automatyzacje, które
  subskrybowały kanał, dostają 404. Znika też nazwa wyświetlana autora, którą kanał podawał w `<dc:creator>`.

### Aktualizacja

- W `composer.json` strony: `"dss/wp-cleanup": "^0.3"`.

## 0.2.1 — 2026-10-09

- Komentarze i dokumentacja: wtyczka od rzeczy, które pomagają atakującemu, to `dss-wp-hardening` (repo
  przemianowane). Działanie bez zmian.

## 0.2.0 — 2026-10-09

- Emoji: bez skryptu wykrywającego i stylów emoji (front, panel, widok osadzenia) i bez zamiany emoji na
  obrazki z `s.w.org` w kanałach RSS i e-mailach.
- Bez linków RSD, shortlink, odkrywania REST API i oEmbed w `<head>` i bez nagłówków `Link` shortlink i REST
  API. Wpis na Twenty Twenty z `dss-no-blocks`: 30,9 → 26,5 KB HTML.
- Opcje nieaktywne: linki do kanałów RSS i jQuery Migrate na froncie.

## 0.1.0 — 2026-10-09

- Pierwsza wersja: pusty MU-plugin `dss-wp-cleanup.php` (nagłówek, wyłącznik awaryjny
  `DSS_WP_CLEANUP_DISABLED`, pusta `configure()`), paczka Composera `dss/wp-cleanup`. Wtyczka niczego nie
  zmienia na stronie.
- Zasady pracy jak w pozostałych wtyczkach DSS: `.github/CONTRIBUTING.md` (numer wersji, zakres i podział
  między wtyczki, tagi), szablon PR, CI ze sprawdzeniem składni na PHP 8.3 i 8.5, `CLAUDE.md`, dokumentacja w
  `docs/` i `COMPOSER.md`.
