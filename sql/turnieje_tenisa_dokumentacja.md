# TURNIEJE_TENISA – Wybory implementacyjne

> Model relacyjny (PlantUML) definiuje strukturę encji i powiązania między nimi.
> Niniejszy dokument opisuje decyzje podjęte na etapie implementacji DDL – czyli
> to, co zostało dookreślone lub dodane względem modelu, wraz z uzasadnieniem
> każdego wyboru.

---

## 1. Mapowanie typów

### 1.1 Typ `string` → `VARCHAR(n)` lub `ENUM`

**Dotyczy:** wszystkich atrybutów oznaczonych `string` w modelu PlantUML.

Model używa `string` jako abstrakcyjnego oznaczenia tekstowego. W implementacji
każdy taki atrybut otrzymał konkretny typ dopasowany do przewidywanej zawartości:

| Tabela         | Kolumna        | Typ w modelu | Typ w DDL               |
| -------------- | -------------- | ------------ | ----------------------- |
| `TURNIEJ`      | `Nazwa`        | `string`     | `VARCHAR(100)`          |
| `TURNIEJ`      | `Miasto`       | `string`     | `VARCHAR(100)`          |
| `TURNIEJ`      | `Nawierzchnia` | `string`     | `ENUM(...)`             |
| `RODZAJ_MECZU` | `Nazwa`        | `string`     | `VARCHAR(50)`           |
| `KRAJ`         | `Nazwa`        | `string`     | `VARCHAR(100)`          |
| `KRAJ`         | `Kontynent`    | `string`     | `VARCHAR(50)` + `CHECK` |
| `ZAWODNIK`     | `Imie`         | `string`     | `VARCHAR(50)`           |
| `ZAWODNIK`     | `Nazwisko`     | `string`     | `VARCHAR(50)`           |
| `MECZ`         | `Kort`         | `string`     | `VARCHAR(50)`           |
| `MECZ`         | `Runda`        | `string`     | `ENUM(...)`             |
| `STATUS`       | `Status`       | `string`     | `VARCHAR(30)`           |
| `PLEC`         | `Nazwa`        | `string`     | `VARCHAR(20)`           |

Kolumny o skończonym i z góry określonym zbiorze wartości (`Nawierzchnia`, `Runda`)
otrzymały typ `ENUM`, co gwarantuje spójność danych na poziomie silnika bazy:

```sql
-- TURNIEJ
Nawierzchnia ENUM('Twarda','Twarda (hala)','Ziemna','Trawa','Dywan')

-- MECZ
Runda ENUM('Kwalifikacje R1','Kwalifikacje R2','Kwalifikacje R3',
           '1. runda','2. runda','3. runda','4. runda',
           '1/8 finału','Ćwierćfinał','Półfinał','Finał')
```

---

### 1.2 Nazwa tabeli `SET` → `SET_MECZU`

Model używa nazwy encji `SET`. W implementacji tabela nosi nazwę `SET_MECZU`,
ponieważ `SET` jest słowem kluczowym SQL zarezerwowanym w MariaDB.
Model PlantUML pozostaje bez zmian – jest narzędziem dokumentacyjnym,
a nie skryptem SQL.

---

## 2. Ograniczenia integralności

Model definiuje klucze główne i obce, ale nie opisuje szczegółowych reguł
biznesowych. Poniższe ograniczenia zostały dodane w DDL jako implementacja
zamysłu dziedzinowego.

### 2.1 Unikalność edycji turnieju

Para `(ID_Turnieju, Rok)` w tabeli `EDYCJA_TURNIEJU` otrzymała ograniczenie
unikalności – każdy turniej odbywa się co najwyżej raz w danym roku.

```sql
CONSTRAINT UQ_EDYCJA_TURNIEJU_ROK UNIQUE (ID_Turnieju, Rok)
```

---

### 2.2 Unikalność udziału zawodnika w edycji

Para `(Nr_Licencji_ITF, ID_Edycji)` w tabeli `UDZIAL` otrzymała ograniczenie
unikalności – zawodnik może być zarejestrowany w danej edycji tylko raz.

```sql
CONSTRAINT UQ_UDZIAL_ZAWODNIK_EDYCJA UNIQUE (Nr_Licencji_ITF, ID_Edycji)
```

---

### 2.3 Kolejność dat w `EDYCJA_TURNIEJU`

Data zakończenia edycji musi być nie wcześniejsza niż data rozpoczęcia.

```sql
CONSTRAINT CHK_EDYCJA_Daty CHECK (Data_zakonczenia >= Data_rozpoczecia)
```

---

### 2.4 Dopuszczalne wartości pól `char(1)`

Kolumny `MECZ.Zwycięska_strona`, `MECZ_UDZIAL.Strona` i `GEM.Serwujacy`
przechowują identyfikator strony meczu (`'A'` lub `'B'`). Ograniczenia `CHECK`
egzekwują ten wybór na poziomie bazy:

```sql
-- MECZ
CONSTRAINT CHK_MECZ_Strona
  CHECK (Zwycięska_strona IS NULL OR Zwycięska_strona IN ('A','B'))

-- MECZ_UDZIAL
CONSTRAINT CHK_MECZ_UDZIAL_Strona CHECK (Strona IN ('A','B'))

-- GEM
CONSTRAINT CHK_GEM_Serwujacy CHECK (Serwujacy IN ('A','B'))
```

`NULL` w `Zwycięska_strona` oznacza mecz w toku lub niezakończony (walkower).

---

## 3. Dodatkowe wybory implementacyjne

### 3.1 `Zwycięska_strona` – świadoma denormalizacja

Kolumna `MECZ.Zwycięska_strona` jest redundantna względem tabeli `SET_MECZU`
(wynik można wyliczyć z liczby wygranych setów). Kolumna została jednak
zachowana jako świadoma denormalizacja, uzasadniona uproszczeniem zapytań
analitycznych i przyspieszeniem generowania drabinek turniejowych.

Spójność między `Zwycięska_strona` a wynikami setów należy zapewnić na
poziomie aplikacji lub triggera (poza zakresem obecnej wersji skryptów).

---

### 3.2 `PLEC` jako tabela słownikowa

Tabela `PLEC` zawiera jedną kolumnę będącą jednocześnie kluczem głównym.
Alternatywą byłoby użycie `ENUM` bezpośrednio w `ZAWODNIK`, jednak tabela
słownikowa – zgodna z modelem – daje możliwość rozszerzenia schematu bez
`ALTER TABLE ZAWODNIK` (np. przy dodaniu atrybutów opisujących płeć).

---

### 3.3 Liczba zawodników na stronę egzekwowana triggerem

Model definiuje `RODZAJ_MECZU.Zawodnikow_na_strone`, ale nie opisuje sposobu
egzekwowania tej reguły. Ograniczenia `CHECK` nie mogą odwoływać się do innych
tabel, dlatego walidacja (mecz singlowy = 2 udziały, deblowy = 4) została
przeniesiona do triggera `TRG_MECZ_UDZIAL_INSERT_LICZBA`
(plik `turnieje_tenisa_triggery.sql`).

---

## 4. Słowniki danych

### 4.1 `STATUS` – sposób wejścia zawodnika do turnieju

Tabela `STATUS` opisuje, w jaki sposób zawodnik dostał się do drabinki. Nie opisuje wyniku jego udziału.

| Wartość           | Znaczenie                                                                                                                                                                                      |
| ----------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `Główna drabinka` | Zawodnik zakwalifikował się bezpośrednio na podstawie rankingu ITF/ATP/WTA.                                                                                                                    |
| `Kwalifikacje`    | Zawodnik przeszedł przez rundy kwalifikacyjne rozgrywane przed turniejem głównym.                                                                                                              |
| `Dzika karta`     | Zawodnik otrzymał zaproszenie od organizatorów poza normalną procedurą, niezależnie od rankingu. Przyznawana m.in. lokalnym zawodnikom, powracającym po kontuzji gwiazdом lub młodym talentom. |
| `Lucky Loser`     | Zawodnik przegrał w ostatniej rundzie kwalifikacji, ale dostał miejsce w głównej drabince po wycofaniu się innego zawodnika tuż przed turniejem.                                               |
| `Wycofał się`     | Zawodnik zarejestrowany w turnieju wycofał się (najczęściej z powodu kontuzji lub choroby).                                                                                                    |

---

### 4.2 `RODZAJ_MECZU` – format meczu

Tabela `RODZAJ_MECZU` definiuje format rozgrywki. BO3 (Best of 3) oznacza mecz do 2 wygranych setów, BO5 (Best of 5) – do 3.

| Nazwa         | Format                                             | Gdzie stosowany                                         |
| ------------- | -------------------------------------------------- | ------------------------------------------------------- |
| `Singiel BO3` | do 2 setów, 1 zawodnik na stronę                   | większość turniejów ATP/WTA, wszystkie mecze kobiet     |
| `Singiel BO5` | do 3 setów, 1 zawodnik na stronę                   | Grand Slamy i Puchar Davisa (mężczyźni)                 |
| `Debel BO3`   | do 2 setów, 2 zawodników na stronę                 | standardowy format debla                                |
| `Debel BO5`   | do 3 setów, 2 zawodników na stronę                 | historyczny format Pucharu Davisa przed reformą 2019 r. |
| `Mikst BO3`   | do 2 setów, 2 zawodników na stronę (para mieszana) | Grand Slamy, zawody drużynowe                           |

---

## 5. Ograniczenia MariaDB 10.4 i zastosowane obejścia

### 5.1 `CURDATE()` niedozwolone w `CHECK`

MariaDB 10.4 odrzuca funkcje niedeterministyczne (zwracające różną wartość w zależności od momentu wywołania) w ograniczeniach `CHECK`. Dotyczy to m.in. `CURDATE()`, `NOW()`, `RAND()`.

Constraint `CHECK (Data_urodzenia <= CURDATE())` w tabeli `ZAWODNIK` został usunięty z DDL i zastąpiony triggerami `BEFORE INSERT` / `BEFORE UPDATE`, które wykonują tę samą walidację:

```sql
IF NEW.Data_urodzenia > CURDATE() THEN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Data urodzenia nie może być w przyszłości.';
END IF;
```

### 5.2 `CONCAT()` niedozwolone bezpośrednio w `SIGNAL`

MariaDB nie akceptuje wyrażeń (np. `CONCAT()`) jako wartości `MESSAGE_TEXT` w instrukcji `SIGNAL` — wymagany jest literał lub zmienna. Rozwiązanie polega na podstawieniu wyniku wyrażenia do zmiennej lokalnej przed wywołaniem `SIGNAL`:

```sql
DECLARE v_msg VARCHAR(200);
SET v_msg = CONCAT('Przekroczono liczbę zawodników na stronę. Wymagana: ',
                   v_wymagani, ', aktualna: ', v_faktyczni, '.');
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_msg;
```

Dotyczy triggera `TRG_MECZ_UDZIAL_INSERT_LICZBA`.

---

## 6. Kolejność wykonania skryptów

```
1. turnieje_tenisa_ddl.sql       -- CREATE TABLE + dane słownikowe
2. turnieje_tenisa_triggery.sql  -- CREATE TRIGGER
```

Oba skrypty są idempotentne – zawierają `DROP IF EXISTS` przed każdą
instrukcją tworzącą obiekt i można je wykonywać wielokrotnie.

---

## 7. Środowisko docelowe

| Parametr     | Wartość                         |
| ------------ | ------------------------------- |
| DBMS         | MariaDB 10.4.32                 |
| Silnik tabel | InnoDB                          |
| Kodowanie    | `utf8mb4` / `utf8mb4_polish_ci` |
| Narzędzie    | phpMyAdmin 5.2.1 / XAMPP        |
| PHP          | 8.2.12                          |

---
