# Turnieje Tenisa

Aplikacja webowa do zarządzania turniejami tenisowymi — edycje roczne, drabinki, wyniki meczów, profile zawodników.

## Stack technologiczny

| Warstwa | Technologia |
|---|---|
| Backend | Laravel 13 (PHP 8.3) |
| Widoki | Blade + Tailwind CSS (CDN) |
| Baza danych | MariaDB 10.4 / InnoDB |
| Lokalne środowisko | XAMPP + phpMyAdmin |

## Struktura repozytorium

```
sql/
  turnieje_tenisa_ddl.sql           # schemat bazy danych + dane słownikowe
  turnieje_tenisa_triggery.sql      # triggery walidacyjne
  turnieje_tenisa_dokumentacja.md   # opis wyborów implementacyjnych DDL

tenis-app/
  turnieje-tenisa/                  # aplikacja Laravel
    app/Http/Controllers/           # kontrolery: Turniej, Edycja, Zawodnik, Mecz
    app/Models/                     # modele Eloquent (11 klas)
    resources/views/                # szablony Blade
    routes/web.php                  # routing
    database/seeders/               # KrajSeeder
  turnieje_tenisa_ddl.sql           # kopia DDL przy aplikacji
  turnieje_tenisa_dane.sql          # dane demo do testów

example-queries/
  example-trigger-violations.md    # zapytania testujące triggery (oczekiwane błędy)
  example-sql-queries.md           # przykładowe zapytania SELECT
```

## Wymagania

| Narzędzie | Minimalna wersja |
|---|---|
| PHP | 8.3 |
| Composer | 2.x |
| Node.js | 18.x |
| MariaDB / MySQL | 10.4 / 8.0 |
| XAMPP (opcjonalnie) | 8.2+ |

Sprawdź wersje przed startem:

```bash
php -v
composer -V
node -v
mysql --version
```

## Uruchomienie

### 1. Baza danych

Uruchom XAMPP (Apache + MySQL), następnie w phpMyAdmin wykonaj skrypty w kolejności:

```
1. sql/turnieje_tenisa_ddl.sql
2. sql/turnieje_tenisa_triggery.sql
3. (opcjonalnie) tenis-app/turnieje_tenisa_dane.sql  — dane demo
```

Oba pierwsze skrypty są idempotentne — zawierają `DROP IF EXISTS` i można je wykonywać wielokrotnie.

### 2. Aplikacja Laravel

W folderze `tenis-app/turnieje-tenisa/`:

```bash
cp .env.example .env
```

Edytuj `.env` — sekcja bazy danych i sterowników:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=turnieje_tenisa
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
```

Następnie:

```bash
composer install
php artisan key:generate
php artisan db:seed          # ładuje KrajSeeder
npm install
npm run build
php artisan serve
```

Aplikacja dostępna pod `http://127.0.0.1:8000`.

## Model danych

Schemat obejmuje:

- turnieje i ich edycje roczne (każdy turniej raz w roku),
- zawodników z licencją ITF,
- udziały zawodników w edycjach (status wejścia, rozstawienie, ranking w momencie rejestracji),
- mecze z podziałem na sety i gemy,
- obsługę singla, debla i miksta (formaty BO3 i BO5).

Szczegółowy opis wyborów implementacyjnych: [`sql/turnieje_tenisa_dokumentacja.md`](sql/turnieje_tenisa_dokumentacja.md)

### Słowniki

**STATUS** — sposób wejścia zawodnika do turnieju:
`Główna drabinka`, `Kwalifikacje`, `Dzika karta`, `Lucky Loser`, `Wycofał się`

**RODZAJ_MECZU** — format meczu:
`Singiel BO3`, `Singiel BO5`, `Debel BO3`, `Debel BO5`, `Mikst BO3`

**Nawierzchnia** (ENUM w tabeli TURNIEJ):
`Twarda`, `Twarda (hala)`, `Ziemna`, `Trawa`, `Dywan`

## Triggery walidacyjne

Skrypt `sql/turnieje_tenisa_triggery.sql` definiuje 9 triggerów w 6 grupach:

| Trigger | Tabela | Reguła |
|---|---|---|
| TRG_EDYCJA_INSERT/UPDATE_DATA_ROK | EDYCJA_TURNIEJU | Rok musi zgadzać się z rokiem Data_rozpoczecia |
| TRG_MECZ_INSERT/UPDATE_DATA | MECZ | Data meczu w przedziale edycji |
| TRG_MECZ_UDZIAL_INSERT_STRONA | MECZ_UDZIAL | Maksymalnie 2 strony (A i B) |
| TRG_MECZ_UDZIAL_INSERT_LICZBA | MECZ_UDZIAL | Liczba zawodników zgodna z rodzajem meczu |
| TRG_UDZIAL_INSERT/UPDATE_WIEK | UDZIAL | Data rejestracji nie wcześniej niż urodziny |
| TRG_UDZIAL_INSERT/UPDATE_EDYCJA | UDZIAL | Data rejestracji nie po zakończeniu edycji |
| TRG_ZAWODNIK_INSERT/UPDATE_URODZINY | ZAWODNIK | Data urodzenia nie w przyszłości |

Zapytania testujące triggery: [`example-queries/example-trigger-violations.md`](example-queries/example-trigger-violations.md)

## Aplikacja — funkcjonalności

Aplikacja umożliwia:

- przeglądanie listy turniejów i ich edycji rocznych,
- dodawanie nowych turniejów i edycji,
- przypisywanie zawodników (z istniejących w bazie lub nowych) do edycji,
- wpisywanie meczów z wynikami setów,
- przeglądanie profili zawodników z historią startów.

Nie zaimplementowano: edycji i usuwania istniejących rekordów, formularzy gemów, kont użytkowników.

## Podział prac

| Osoba | Zakres |
|---|---|
| Jakub Tkaczyk | Modele Eloquent, seedery |
| Adrian Stuglik | Kontrolery, routing, walidacja |
| Laura Wrońska | Widoki turniejów i edycji, layout |
| Natalia Topinek | Widoki zawodników i meczów, dane demo SQL |
