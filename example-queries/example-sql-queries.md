# Przykładowe zapytania SQL

Zapytania do przeglądania i analizy danych w bazie `turnieje_tenisa`.

---

## Turnieje i edycje

```sql
-- Lista wszystkich turniejów z krajem i liczbą edycji
SELECT t.Nazwa, t.Miasto, k.Nazwa AS Kraj, t.Nawierzchnia,
       COUNT(e.ID_Edycji) AS Liczba_edycji
FROM TURNIEJ t
JOIN KRAJ k ON k.Kod_ISO = t.Kod_ISO
LEFT JOIN EDYCJA_TURNIEJU e ON e.ID_Turnieju = t.ID_Turnieju
GROUP BY t.ID_Turnieju
ORDER BY t.Nazwa;

-- Edycje jednego turnieju z liczbą zawodników i meczów
SELECT e.Rok, e.Data_rozpoczecia, e.Data_zakonczenia,
       COUNT(DISTINCT u.ID_Udzialu) AS Zawodnicy,
       COUNT(DISTINCT m.ID_Meczu)   AS Mecze
FROM EDYCJA_TURNIEJU e
LEFT JOIN UDZIAL u ON u.ID_Edycji = e.ID_Edycji
LEFT JOIN MECZ m   ON m.ID_Edycji = e.ID_Edycji
WHERE e.ID_Turnieju = 1
GROUP BY e.ID_Edycji
ORDER BY e.Rok DESC;
```

---

## Zawodnicy

```sql
-- Lista zawodników z krajem i wiekiem
SELECT z.Nr_Licencji_ITF, z.Imie, z.Nazwisko, z.Plec,
       k.Nazwa AS Kraj,
       TIMESTAMPDIFF(YEAR, z.Data_urodzenia, CURDATE()) AS Wiek
FROM ZAWODNIK z
JOIN KRAJ k ON k.Kod_ISO = z.Kod_ISO
ORDER BY z.Nazwisko, z.Imie;

-- Zawodnicy z największą liczbą startów w turniejach
SELECT z.Imie, z.Nazwisko, k.Nazwa AS Kraj,
       COUNT(u.ID_Udzialu) AS Starty
FROM ZAWODNIK z
JOIN KRAJ k ON k.Kod_ISO = z.Kod_ISO
JOIN UDZIAL u ON u.Nr_Licencji_ITF = z.Nr_Licencji_ITF
GROUP BY z.Nr_Licencji_ITF
ORDER BY Starty DESC
LIMIT 10;
```

---

## Mecze i wyniki

```sql
-- Mecze danej edycji z zawodnikami i wynikiem
SELECT m.Runda, m.Data, m.Kort,
       GROUP_CONCAT(DISTINCT CASE WHEN mu.Strona = 'A'
           THEN CONCAT(z.Imie, ' ', z.Nazwisko) END SEPARATOR ' / ') AS Strona_A,
       GROUP_CONCAT(DISTINCT CASE WHEN mu.Strona = 'B'
           THEN CONCAT(z.Imie, ' ', z.Nazwisko) END SEPARATOR ' / ') AS Strona_B,
       m.`Zwycięska_strona`
FROM MECZ m
JOIN MECZ_UDZIAL mu ON mu.ID_Meczu = m.ID_Meczu
JOIN UDZIAL u       ON u.ID_Udzialu = mu.ID_Udzialu
JOIN ZAWODNIK z     ON z.Nr_Licencji_ITF = u.Nr_Licencji_ITF
WHERE m.ID_Edycji = 1
GROUP BY m.ID_Meczu
ORDER BY m.Data, m.Runda;

-- Wyniki setów dla konkretnego meczu
SELECT sm.Numer_setu, sm.Wynik_A, sm.Wynik_B,
       IF(sm.Tie_break, 'Tak', 'Nie') AS Tie_break
FROM SET_MECZU sm
WHERE sm.ID_Meczu = 1
ORDER BY sm.Numer_setu;
```

---

## Statystyki

```sql
-- Liczba wygranych meczów per zawodnik w danej edycji
SELECT z.Imie, z.Nazwisko,
       COUNT(*) AS Wygrane
FROM MECZ m
JOIN MECZ_UDZIAL mu ON mu.ID_Meczu = m.ID_Meczu
                    AND mu.Strona = m.`Zwycięska_strona`
JOIN UDZIAL u       ON u.ID_Udzialu = mu.ID_Udzialu
JOIN ZAWODNIK z     ON z.Nr_Licencji_ITF = u.Nr_Licencji_ITF
WHERE m.ID_Edycji = 1
GROUP BY z.Nr_Licencji_ITF
ORDER BY Wygrane DESC;

-- Najdłuższe mecze (wg czasu trwania)
SELECT t.Nazwa AS Turniej, e.Rok, m.Runda,
       m.Czas_trwania_min,
       GROUP_CONCAT(DISTINCT CONCAT(z.Imie, ' ', z.Nazwisko)
           ORDER BY mu.Strona SEPARATOR ' vs ') AS Zawodnicy
FROM MECZ m
JOIN EDYCJA_TURNIEJU e ON e.ID_Edycji = m.ID_Edycji
JOIN TURNIEJ t         ON t.ID_Turnieju = e.ID_Turnieju
JOIN MECZ_UDZIAL mu    ON mu.ID_Meczu = m.ID_Meczu
JOIN UDZIAL u          ON u.ID_Udzialu = mu.ID_Udzialu
JOIN ZAWODNIK z        ON z.Nr_Licencji_ITF = u.Nr_Licencji_ITF
WHERE m.Czas_trwania_min IS NOT NULL
GROUP BY m.ID_Meczu
ORDER BY m.Czas_trwania_min DESC
LIMIT 10;

-- Liczba meczów per rodzaj (singiel/debel/mikst)
SELECT rm.Nazwa AS Rodzaj, COUNT(*) AS Liczba_meczow
FROM MECZ m
JOIN RODZAJ_MECZU rm ON rm.ID_Rodzaju = m.ID_Rodzaju
GROUP BY rm.ID_Rodzaju
ORDER BY Liczba_meczow DESC;
```

---

## Pomocnicze

```sql
-- Sprawdź dostępne edycje z zakresem dat
SELECT ID_Edycji, ID_Turnieju, Rok, Data_rozpoczecia, Data_zakonczenia
FROM EDYCJA_TURNIEJU
ORDER BY Rok DESC, ID_Turnieju;

-- Sprawdź rodzaje meczu z liczbą zawodników
SELECT ID_Rodzaju, Nazwa, Sety_do_wygrania, Zawodnikow_na_strone
FROM RODZAJ_MECZU;

-- Sprawdź udziały zawodnika we wszystkich edycjach
SELECT t.Nazwa AS Turniej, e.Rok, u.Status,
       u.Rozstawienie, u.Ranking_w_momencie_rejestracji
FROM UDZIAL u
JOIN EDYCJA_TURNIEJU e ON e.ID_Edycji = u.ID_Edycji
JOIN TURNIEJ t         ON t.ID_Turnieju = e.ID_Turnieju
WHERE u.Nr_Licencji_ITF = 'ITF000001'
ORDER BY e.Rok DESC;
```
