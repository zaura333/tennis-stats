# Turnieje Tenisa

Aplikacja webowa do zarządzania turniejami tenisowymi — drabinki, wyniki meczów, statystyki zawodników i rankingi.

## Stack technologiczny

- **Backend:** Laravel (PHP)
- **Frontend:** Blade + Livewire
- **Baza danych:** MariaDB 10.4
- **Lokalne środowisko:** XAMPP + phpMyAdmin

## Struktura repozytorium

```
sql/
  turnieje_tenisa_ddl.sql        # schemat bazy danych + dane słownikowe
  turnieje_tenisa_triggery.sql   # triggery walidacyjne
  turnieje_tenisa_dokumentacja.md # opis wyborów implementacyjnych
```

> Aplikacja Laravel zostanie dodana w kolejnym kroku. Skrypty SQL w folderze `sql/` stanowią podstawę bazy danych, na której będzie działać aplikacja.

## Uruchomienie bazy danych

1. Uruchom XAMPP (Apache + MySQL).
2. Otwórz phpMyAdmin i wykonaj skrypty w kolejności:
   ```
   1. sql/turnieje_tenisa_ddl.sql
   2. sql/turnieje_tenisa_triggery.sql
   ```
   Skrypty są idempotentne — można je wykonywać wielokrotnie.

## Model danych

Schemat opisuje:
- turnieje i ich edycje roczne,
- zawodników z licencją ITF,
- udziały zawodników w edycjach (z rozstawieniem i rankingiem w momencie rejestracji),
- mecze z podziałem na sety i gemy,
- obsługę singla, debla i miksta (BO3 i BO5).

Szczegółowy opis wyborów implementacyjnych: [`sql/turnieje_tenisa_dokumentacja.md`](sql/turnieje_tenisa_dokumentacja.md)
