# Przykładowe zapytania testujące triggery

Każde zapytanie powinno zakończyć się błędem `SQLSTATE 45000`.
Konkretne ID dostosuj do danych w bazie (`SELECT ID_Edycji, Rok, Data_rozpoczecia, Data_zakonczenia FROM EDYCJA_TURNIEJU LIMIT 5;`).

---

## TRG_EDYCJA_DATA_ROK — rok niezgodny z datą rozpoczęcia

```sql
-- Rok=2024, ale data rozpoczęcia jest w 2025 → błąd
INSERT INTO EDYCJA_TURNIEJU (ID_Turnieju, Rok, Data_rozpoczecia, Data_zakonczenia)
VALUES (1, 2024, '2025-01-13', '2025-01-26');
```

---

## TRG_MECZ_DATA_W_EDYCJI — data meczu poza przedziałem edycji

```sql
-- Zakładając że edycja ID=1 trwa 2025-01-13 do 2025-01-26

-- Data meczu przed rozpoczęciem → błąd
INSERT INTO MECZ (ID_Edycji, ID_Rodzaju, Runda, Data, Kort)
VALUES (1, 1, 'Finał', '2025-01-01', 'Kort centralny');

-- Data meczu po zakończeniu → błąd
INSERT INTO MECZ (ID_Edycji, ID_Rodzaju, Runda, Data, Kort)
VALUES (1, 1, 'Finał', '2025-02-15', 'Kort centralny');
```

---

## TRG_MECZ_UDZIAL_STRONA — próba dodania trzeciej strony

```sql
-- Zakładając że mecz ID=1 ma już strony A i B
-- Próba dodania strony C → błąd
INSERT INTO MECZ_UDZIAL (ID_Meczu, ID_Udzialu, Strona)
VALUES (1, 3, 'C');
```

---

## TRG_MECZ_UDZIAL_LICZBA — za dużo zawodników na stronę

```sql
-- Zakładając mecz ID=1 jest singlem (Zawodnikow_na_strone=1)
-- i strona A ma już jednego zawodnika — próba dodania drugiego → błąd
INSERT INTO MECZ_UDZIAL (ID_Meczu, ID_Udzialu, Strona)
VALUES (1, 5, 'A');
```

---

## TRG_UDZIAL_WIEK — rejestracja przed urodzinami

```sql
-- Zawodnik urodzony 1995-06-15, data rejestracji wcześniejsza → błąd
INSERT INTO UDZIAL (Nr_Licencji_ITF, ID_Edycji, Status, Rozstawienie, Ranking_w_momencie_rejestracji, Data_rejestracji)
VALUES ('ITF-TEST-01', 1, 'Uczestnik', 1, 100, '1990-01-01');
```

---

## TRG_UDZIAL_EDYCJA — rejestracja po zakończeniu edycji

```sql
-- Edycja ID=1 kończy się 2025-01-26, data rejestracji późniejsza → błąd
INSERT INTO UDZIAL (Nr_Licencji_ITF, ID_Edycji, Status, Rozstawienie, Ranking_w_momencie_rejestracji, Data_rejestracji)
VALUES ('ITF-TEST-01', 1, 'Uczestnik', 1, 100, '2025-12-31');
```

---

## TRG_ZAWODNIK_URODZINY — data urodzenia w przyszłości

```sql
-- Data urodzenia jutro → błąd
INSERT INTO ZAWODNIK (Nr_Licencji_ITF, Imie, Nazwisko, Plec, Kod_ISO, Data_urodzenia)
VALUES ('ITF-TEST-99', 'Jan', 'Testowy', 'Mężczyzna', 'POL', DATE_ADD(CURDATE(), INTERVAL 1 DAY));

-- UPDATE też powinien zablokować
UPDATE ZAWODNIK
SET Data_urodzenia = DATE_ADD(CURDATE(), INTERVAL 365 DAY)
WHERE Nr_Licencji_ITF = 'ITF-TEST-99';
```
