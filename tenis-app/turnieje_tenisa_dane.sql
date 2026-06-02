-- =============================================================================
-- DANE PRZYKŁADOWE: Turnieje Tenisowe
-- Silnik: MariaDB 10.4 / InnoDB  |  Kodowanie: utf8mb4
--
-- Zawiera:
--   2 turnieje: Wimbledon (1 edycja) i Roland Garros (2 edycje)
--   13 zawodników (część bierze udział w kilku turniejach)
--   21 meczów ćwierćfinałowych, półfinałowych i finałowych
--   75 setów ze szczegółowymi wynikami
--
-- UWAGA: Skrypt CZYŚCI istniejące rekordy z tabel TURNIEJ, EDYCJA_TURNIEJU,
--        ZAWODNIK, UDZIAL, MECZ, MECZ_UDZIAL, SET_MECZU, GEM.
--        Tabele słownikowe (KRAJ, PLEC, STATUS, RODZAJ_MECZU) są zachowane.
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Czyszczenie danych (odwrotna kolejność kluczy obcych)
DELETE FROM GEM;
DELETE FROM SET_MECZU;
DELETE FROM MECZ_UDZIAL;
DELETE FROM MECZ;
DELETE FROM UDZIAL;
DELETE FROM ZAWODNIK;
DELETE FROM EDYCJA_TURNIEJU;
DELETE FROM TURNIEJ;

-- Reset liczników auto_increment
ALTER TABLE TURNIEJ         AUTO_INCREMENT = 1;
ALTER TABLE EDYCJA_TURNIEJU AUTO_INCREMENT = 1;
ALTER TABLE UDZIAL          AUTO_INCREMENT = 1;
ALTER TABLE MECZ            AUTO_INCREMENT = 1;
ALTER TABLE SET_MECZU       AUTO_INCREMENT = 1;
ALTER TABLE GEM             AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- TURNIEJE
-- =============================================================================
INSERT INTO TURNIEJ (ID_Turnieju, Kod_ISO, Nazwa, Miasto, Nawierzchnia) VALUES
    (1, 'GBR', 'Wimbledon',     'Londyn', 'Trawa'),
    (2, 'FRA', 'Roland Garros', 'Paryż',  'Ziemna');

-- =============================================================================
-- EDYCJE TURNIEJÓW
--   Wimbledon:     1 edycja  (2023)
--   Roland Garros: 2 edycje  (2022 i 2023)
-- =============================================================================
INSERT INTO EDYCJA_TURNIEJU (ID_Edycji, ID_Turnieju, Rok, Data_rozpoczecia, Data_zakonczenia) VALUES
    (1, 1, 2023, '2023-07-03', '2023-07-16'),   -- Wimbledon 2023
    (2, 2, 2022, '2022-05-22', '2022-06-05'),   -- Roland Garros 2022
    (3, 2, 2023, '2023-05-28', '2023-06-11');   -- Roland Garros 2023

-- =============================================================================
-- ZAWODNICY
--   ITF-M-001 Reyes     – dominuje, zwycięzca Wimbledonu i RG 2022
--   ITF-M-007 Dubois    – triumfator Roland Garros 2023 (wielka niespodzianka)
--   ITF-M-002 Petrescu  – finał Wimbledonu, regularnie w półfinałach
--   Reyes, Dubois, Petrescu, Fletcher, Müller, Ferretti, Kowalski
--     biorą udział w kilku turniejach
-- =============================================================================
INSERT INTO ZAWODNIK (Nr_Licencji_ITF, Plec, Kod_ISO, Imie, Nazwisko, Data_urodzenia) VALUES
    ('ITF-M-001', 'Mężczyzna', 'ESP', 'Carlos',    'Reyes',     '1996-06-15'),
    ('ITF-M-002', 'Mężczyzna', 'ROU', 'Andrei',    'Petrescu',  '1994-03-22'),
    ('ITF-M-003', 'Mężczyzna', 'GBR', 'James',     'Fletcher',  '1997-09-10'),
    ('ITF-M-004', 'Mężczyzna', 'POL', 'Tomasz',    'Kowalski',  '1998-04-05'),
    ('ITF-M-005', 'Mężczyzna', 'GER', 'Sebastian', 'Müller',    '1993-11-28'),
    ('ITF-M-006', 'Mężczyzna', 'ITA', 'Luca',      'Ferretti',  '1995-07-14'),
    ('ITF-M-007', 'Mężczyzna', 'FRA', 'Nicolas',   'Dubois',    '1997-01-30'),
    ('ITF-M-008', 'Mężczyzna', 'RUS', 'Alexei',    'Volkov',    '1992-08-17'),
    ('ITF-M-009', 'Mężczyzna', 'ARG', 'Miguel',    'Herrera',   '1996-02-11'),
    ('ITF-M-010', 'Mężczyzna', 'ARG', 'Diego',     'Alonso',    '1999-05-20'),
    ('ITF-M-011', 'Mężczyzna', 'FRA', 'Pierre',    'Lefebvre',  '1994-10-03'),
    ('ITF-M-012', 'Mężczyzna', 'BUL', 'Stefan',    'Popov',     '1995-12-07'),
    ('ITF-M-013', 'Mężczyzna', 'ITA', 'Marco',     'Rizzi',     '2000-03-16');

-- =============================================================================
-- UDZIAŁY – zgłoszenia zawodników do edycji turniejów
--   Rozstawienie: miejsce w drabince (seed), wymagane > 0
-- =============================================================================

-- Wimbledon 2023 (ID_Edycji = 1)
INSERT INTO UDZIAL (ID_Udzialu, Nr_Licencji_ITF, ID_Edycji, Status,            Rozstawienie, Ranking_w_momencie_rejestracji, Data_rejestracji) VALUES
    ( 1, 'ITF-M-001', 1, 'Główna drabinka', 1,  3, '2023-03-15'),  -- Reyes
    ( 2, 'ITF-M-003', 1, 'Główna drabinka', 2,  8, '2023-03-15'),  -- Fletcher
    ( 3, 'ITF-M-005', 1, 'Główna drabinka', 3, 12, '2023-03-15'),  -- Müller
    ( 4, 'ITF-M-002', 1, 'Główna drabinka', 4, 15, '2023-03-15'),  -- Petrescu
    ( 5, 'ITF-M-004', 1, 'Główna drabinka', 5, 22, '2023-03-15'),  -- Kowalski
    ( 6, 'ITF-M-007', 1, 'Główna drabinka', 6, 28, '2023-03-15'),  -- Dubois
    ( 7, 'ITF-M-006', 1, 'Główna drabinka', 7, 31, '2023-03-15'),  -- Ferretti
    ( 8, 'ITF-M-008', 1, 'Główna drabinka', 8, 35, '2023-03-15');  -- Volkov

-- Roland Garros 2022 (ID_Edycji = 2)
INSERT INTO UDZIAL (ID_Udzialu, Nr_Licencji_ITF, ID_Edycji, Status,            Rozstawienie, Ranking_w_momencie_rejestracji, Data_rejestracji) VALUES
    ( 9, 'ITF-M-001', 2, 'Główna drabinka', 1,  3, '2022-04-01'),  -- Reyes
    (10, 'ITF-M-007', 2, 'Główna drabinka', 2, 10, '2022-04-01'),  -- Dubois
    (11, 'ITF-M-006', 2, 'Główna drabinka', 3, 18, '2022-04-01'),  -- Ferretti
    (12, 'ITF-M-009', 2, 'Główna drabinka', 4, 20, '2022-04-01'),  -- Herrera
    (13, 'ITF-M-010', 2, 'Główna drabinka', 5, 25, '2022-04-01'),  -- Alonso
    (14, 'ITF-M-011', 2, 'Główna drabinka', 6, 30, '2022-04-01'),  -- Lefebvre
    (15, 'ITF-M-002', 2, 'Główna drabinka', 7, 16, '2022-04-01'),  -- Petrescu
    (16, 'ITF-M-012', 2, 'Dzika karta',     8, 40, '2022-04-01');  -- Popov (dzika karta)

-- Roland Garros 2023 (ID_Edycji = 3)
INSERT INTO UDZIAL (ID_Udzialu, Nr_Licencji_ITF, ID_Edycji, Status,            Rozstawienie, Ranking_w_momencie_rejestracji, Data_rejestracji) VALUES
    (17, 'ITF-M-001', 3, 'Główna drabinka', 1,  2, '2023-04-01'),  -- Reyes
    (18, 'ITF-M-007', 3, 'Główna drabinka', 2,  9, '2023-04-01'),  -- Dubois
    (19, 'ITF-M-002', 3, 'Główna drabinka', 3, 14, '2023-04-01'),  -- Petrescu
    (20, 'ITF-M-003', 3, 'Główna drabinka', 4,  7, '2023-04-01'),  -- Fletcher
    (21, 'ITF-M-006', 3, 'Główna drabinka', 5, 17, '2023-04-01'),  -- Ferretti
    (22, 'ITF-M-005', 3, 'Główna drabinka', 6, 11, '2023-04-01'),  -- Müller
    (23, 'ITF-M-004', 3, 'Główna drabinka', 7, 21, '2023-04-01'),  -- Kowalski
    (24, 'ITF-M-013', 3, 'Dzika karta',     8, 45, '2023-04-01');  -- Rizzi (dzika karta)

-- =============================================================================
-- MECZE – 21 meczów, wszystkie to Singiel BO5 (ID_Rodzaju = 2)
--   Strona A = wyżej rozstawiony / inicjator
--   `Zwycięska_strona` = 'A' lub 'B'
-- =============================================================================

-- ── Wimbledon 2023 ───────────────────────────────────────────────────────────
INSERT INTO MECZ (ID_Meczu, ID_Edycji, ID_Rodzaju, Runda,          Data,         Kort,             Czas_trwania_min, `Zwycięska_strona`) VALUES
    ( 1, 1, 2, 'Ćwierćfinał', '2023-07-11', 'Centre Court',   115, 'A'),  -- Reyes     vs Volkov    → Reyes
    ( 2, 1, 2, 'Ćwierćfinał', '2023-07-11', 'Court 1',         85, 'A'),  -- Fletcher  vs Ferretti  → Fletcher
    ( 3, 1, 2, 'Ćwierćfinał', '2023-07-11', 'Court 2',        150, 'A'),  -- Müller    vs Dubois    → Müller
    ( 4, 1, 2, 'Ćwierćfinał', '2023-07-11', 'Court 3',         90, 'A'),  -- Petrescu  vs Kowalski  → Petrescu
    ( 5, 1, 2, 'Półfinał',    '2023-07-14', 'Centre Court',   145, 'A'),  -- Reyes     vs Fletcher  → Reyes
    ( 6, 1, 2, 'Półfinał',    '2023-07-14', 'Court 1',        210, 'B'),  -- Müller    vs Petrescu  → Petrescu (5 setów!)
    ( 7, 1, 2, 'Finał',       '2023-07-16', 'Centre Court',   105, 'A'); -- Reyes     vs Petrescu  → Reyes 🏆

-- ── Roland Garros 2022 ───────────────────────────────────────────────────────
INSERT INTO MECZ (ID_Meczu, ID_Edycji, ID_Rodzaju, Runda,          Data,         Kort,                          Czas_trwania_min, `Zwycięska_strona`) VALUES
    ( 8, 2, 2, 'Ćwierćfinał', '2022-06-01', 'Court Philippe-Chatrier',   72, 'A'),  -- Reyes    vs Popov    → Reyes
    ( 9, 2, 2, 'Ćwierćfinał', '2022-06-01', 'Court Suzanne-Lenglen',    155, 'A'),  -- Dubois   vs Petrescu → Dubois
    (10, 2, 2, 'Ćwierćfinał', '2022-06-01', 'Court Simonne-Mathieu',     88, 'A'),  -- Ferretti vs Lefebvre → Ferretti
    (11, 2, 2, 'Ćwierćfinał', '2022-06-01', 'Court Philippe-Chatrier',  140, 'A'),  -- Herrera  vs Alonso   → Herrera
    (12, 2, 2, 'Półfinał',    '2022-06-03', 'Court Philippe-Chatrier',   95, 'A'),  -- Reyes    vs Dubois   → Reyes
    (13, 2, 2, 'Półfinał',    '2022-06-03', 'Court Suzanne-Lenglen',    138, 'B'),  -- Ferretti vs Herrera  → Herrera
    (14, 2, 2, 'Finał',       '2022-06-05', 'Court Philippe-Chatrier',  143, 'A'); -- Reyes    vs Herrera  → Reyes 🏆

-- ── Roland Garros 2023 ───────────────────────────────────────────────────────
INSERT INTO MECZ (ID_Meczu, ID_Edycji, ID_Rodzaju, Runda,          Data,         Kort,                          Czas_trwania_min, `Zwycięska_strona`) VALUES
    (15, 3, 2, 'Ćwierćfinał', '2023-06-06', 'Court Philippe-Chatrier',   68, 'A'),  -- Reyes    vs Rizzi    → Reyes
    (16, 3, 2, 'Ćwierćfinał', '2023-06-06', 'Court Suzanne-Lenglen',     92, 'A'),  -- Dubois   vs Müller   → Dubois
    (17, 3, 2, 'Ćwierćfinał', '2023-06-06', 'Court Simonne-Mathieu',     88, 'A'),  -- Petrescu vs Kowalski → Petrescu
    (18, 3, 2, 'Ćwierćfinał', '2023-06-06', 'Court Suzanne-Lenglen',    155, 'A'),  -- Fletcher vs Ferretti → Fletcher
    (19, 3, 2, 'Półfinał',    '2023-06-08', 'Court Philippe-Chatrier',  218, 'B'),  -- Reyes    vs Dubois   → Dubois (NIESPODZIANKA! 5 setów)
    (20, 3, 2, 'Półfinał',    '2023-06-08', 'Court Suzanne-Lenglen',     84, 'A'),  -- Petrescu vs Fletcher → Petrescu
    (21, 3, 2, 'Finał',       '2023-06-11', 'Court Philippe-Chatrier',  152, 'A'); -- Dubois   vs Petrescu → Dubois 🏆

-- =============================================================================
-- MECZ_UDZIAL – przypisanie zawodników do stron meczu
-- =============================================================================

-- Wimbledon 2023
INSERT INTO MECZ_UDZIAL (ID_Meczu, ID_Udzialu, Strona) VALUES
    ( 1,  1, 'A'), ( 1,  8, 'B'),   -- Reyes [1]    vs Volkov [8]
    ( 2,  2, 'A'), ( 2,  7, 'B'),   -- Fletcher [2] vs Ferretti [7]
    ( 3,  3, 'A'), ( 3,  6, 'B'),   -- Müller [3]   vs Dubois [6]
    ( 4,  4, 'A'), ( 4,  5, 'B'),   -- Petrescu [4] vs Kowalski [5]
    ( 5,  1, 'A'), ( 5,  2, 'B'),   -- Reyes [1]    vs Fletcher [2]
    ( 6,  3, 'A'), ( 6,  4, 'B'),   -- Müller [3]   vs Petrescu [4]
    ( 7,  1, 'A'), ( 7,  4, 'B');   -- Reyes [1]    vs Petrescu [4]  ← FINAŁ

-- Roland Garros 2022
INSERT INTO MECZ_UDZIAL (ID_Meczu, ID_Udzialu, Strona) VALUES
    ( 8,  9, 'A'), ( 8, 16, 'B'),   -- Reyes [1]    vs Popov [8]
    ( 9, 10, 'A'), ( 9, 15, 'B'),   -- Dubois [2]   vs Petrescu [7]
    (10, 11, 'A'), (10, 14, 'B'),   -- Ferretti [3] vs Lefebvre [6]
    (11, 12, 'A'), (11, 13, 'B'),   -- Herrera [4]  vs Alonso [5]
    (12,  9, 'A'), (12, 10, 'B'),   -- Reyes [1]    vs Dubois [2]
    (13, 11, 'A'), (13, 12, 'B'),   -- Ferretti [3] vs Herrera [4]
    (14,  9, 'A'), (14, 12, 'B');   -- Reyes [1]    vs Herrera [4]   ← FINAŁ

-- Roland Garros 2023
INSERT INTO MECZ_UDZIAL (ID_Meczu, ID_Udzialu, Strona) VALUES
    (15, 17, 'A'), (15, 24, 'B'),   -- Reyes [1]    vs Rizzi [8]
    (16, 18, 'A'), (16, 22, 'B'),   -- Dubois [2]   vs Müller [6]
    (17, 19, 'A'), (17, 23, 'B'),   -- Petrescu [3] vs Kowalski [7]
    (18, 20, 'A'), (18, 21, 'B'),   -- Fletcher [4] vs Ferretti [5]
    (19, 17, 'A'), (19, 18, 'B'),   -- Reyes [1]    vs Dubois [2]
    (20, 19, 'A'), (20, 20, 'B'),   -- Petrescu [3] vs Fletcher [4]
    (21, 18, 'A'), (21, 19, 'B');   -- Dubois [2]   vs Petrescu [3]  ← FINAŁ

-- =============================================================================
-- SETY MECZÓW – wyniki gemowe każdego seta
--   Wynik_A / Wynik_B = liczba wygranych gemów w secie
--   Tie_break = 1 jeśli set rozstrzygnął tie-break
-- =============================================================================

-- ── Wimbledon 2023 ───────────────────────────────────────────────────────────

-- MECZ 1: Reyes bt Volkov  6-3  7-6(4)  6-2
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    ( 1, 1, 1, 6, 3, 0),
    ( 2, 1, 2, 7, 6, 1),
    ( 3, 1, 3, 6, 2, 0);

-- MECZ 2: Fletcher bt Ferretti  6-4  6-1  6-3
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    ( 4, 2, 1, 6, 4, 0),
    ( 5, 2, 2, 6, 1, 0),
    ( 6, 2, 3, 6, 3, 0);

-- MECZ 3: Müller bt Dubois  7-5  6-4  6-7(5)  7-6(3)
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    ( 7, 3, 1, 7, 5, 0),
    ( 8, 3, 2, 6, 4, 0),
    ( 9, 3, 3, 6, 7, 1),  -- Dubois wygrywa ten set
    (10, 3, 4, 7, 6, 1);

-- MECZ 4: Petrescu bt Kowalski  6-2  6-4  6-3
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (11, 4, 1, 6, 2, 0),
    (12, 4, 2, 6, 4, 0),
    (13, 4, 3, 6, 3, 0);

-- MECZ 5: Reyes bt Fletcher  6-4  6-7(2)  7-5  6-3
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (14, 5, 1, 6, 4, 0),
    (15, 5, 2, 6, 7, 1),  -- Fletcher wygrywa ten set
    (16, 5, 3, 7, 5, 0),
    (17, 5, 4, 6, 3, 0);

-- MECZ 6: Petrescu bt Müller  6-4  6-7(5)  4-6  7-5  7-5   (5 setów!)
--   Uwaga: strona A = Müller, B = Petrescu; B wygrywa
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (18, 6, 1, 4, 6, 0),   -- Petrescu wygrywa
    (19, 6, 2, 7, 6, 1),   -- Müller wygrywa (tie-break)
    (20, 6, 3, 6, 4, 0),   -- Müller wygrywa
    (21, 6, 4, 5, 7, 0),   -- Petrescu wygrywa
    (22, 6, 5, 5, 7, 0);   -- Petrescu wygrywa (decydujący 5. set!)

-- MECZ 7 (FINAŁ Wimbledon 2023): Reyes bt Petrescu  7-6(4)  6-3  6-4
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (23, 7, 1, 7, 6, 1),
    (24, 7, 2, 6, 3, 0),
    (25, 7, 3, 6, 4, 0);

-- ── Roland Garros 2022 ───────────────────────────────────────────────────────

-- MECZ 8: Reyes bt Popov  6-1  6-3  6-2
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (26, 8, 1, 6, 1, 0),
    (27, 8, 2, 6, 3, 0),
    (28, 8, 3, 6, 2, 0);

-- MECZ 9: Dubois bt Petrescu  6-4  6-3  6-7(5)  7-6(3)
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (29, 9, 1, 6, 4, 0),
    (30, 9, 2, 6, 3, 0),
    (31, 9, 3, 6, 7, 1),   -- Petrescu wygrywa ten set
    (32, 9, 4, 7, 6, 1);

-- MECZ 10: Ferretti bt Lefebvre  7-5  6-4  6-2
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (33, 10, 1, 7, 5, 0),
    (34, 10, 2, 6, 4, 0),
    (35, 10, 3, 6, 2, 0);

-- MECZ 11: Herrera bt Alonso  4-6  6-3  6-4  7-5
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (36, 11, 1, 4, 6, 0),   -- Alonso wygrywa ten set
    (37, 11, 2, 6, 3, 0),
    (38, 11, 3, 6, 4, 0),
    (39, 11, 4, 7, 5, 0);

-- MECZ 12: Reyes bt Dubois  6-2  6-4  7-6(3)
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (40, 12, 1, 6, 2, 0),
    (41, 12, 2, 6, 4, 0),
    (42, 12, 3, 7, 6, 1);

-- MECZ 13: Herrera bt Ferretti  3-6  6-4  6-3  6-2
--   Uwaga: strona A = Ferretti, B = Herrera; B wygrywa
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (43, 13, 1, 6, 3, 0),   -- Ferretti wygrywa ten set
    (44, 13, 2, 4, 6, 0),   -- Herrera wygrywa
    (45, 13, 3, 3, 6, 0),   -- Herrera wygrywa
    (46, 13, 4, 2, 6, 0);   -- Herrera wygrywa

-- MECZ 14 (FINAŁ Roland Garros 2022): Reyes bt Herrera  6-4  7-5  4-6  6-3
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (47, 14, 1, 6, 4, 0),
    (48, 14, 2, 7, 5, 0),
    (49, 14, 3, 4, 6, 0),   -- Herrera wygrywa ten set
    (50, 14, 4, 6, 3, 0);

-- ── Roland Garros 2023 ───────────────────────────────────────────────────────

-- MECZ 15: Reyes bt Rizzi  6-3  6-2  6-1
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (51, 15, 1, 6, 3, 0),
    (52, 15, 2, 6, 2, 0),
    (53, 15, 3, 6, 1, 0);

-- MECZ 16: Dubois bt Müller  6-3  7-5  6-4
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (54, 16, 1, 6, 3, 0),
    (55, 16, 2, 7, 5, 0),
    (56, 16, 3, 6, 4, 0);

-- MECZ 17: Petrescu bt Kowalski  7-5  6-3  6-4
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (57, 17, 1, 7, 5, 0),
    (58, 17, 2, 6, 3, 0),
    (59, 17, 3, 6, 4, 0);

-- MECZ 18: Fletcher bt Ferretti  6-4  3-6  7-6(5)  6-3
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (60, 18, 1, 6, 4, 0),
    (61, 18, 2, 3, 6, 0),   -- Ferretti wygrywa ten set
    (62, 18, 3, 7, 6, 1),
    (63, 18, 4, 6, 3, 0);

-- MECZ 19: Dubois bt Reyes  6-3  4-6  3-6  6-4  7-5   (WIELKA NIESPODZIANKA! 5 setów!)
--   Uwaga: strona A = Reyes, B = Dubois; B wygrywa
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (64, 19, 1, 3, 6, 0),   -- Dubois wygrywa
    (65, 19, 2, 6, 4, 0),   -- Reyes wygrywa
    (66, 19, 3, 6, 3, 0),   -- Reyes wygrywa (Reyes prowadzi 2-1!)
    (67, 19, 4, 4, 6, 0),   -- Dubois wygrywa (wyrównuje 2-2)
    (68, 19, 5, 5, 7, 0);   -- Dubois wygrywa decydujący set!

-- MECZ 20: Petrescu bt Fletcher  6-4  6-2  7-5
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (69, 20, 1, 6, 4, 0),
    (70, 20, 2, 6, 2, 0),
    (71, 20, 3, 7, 5, 0);

-- MECZ 21 (FINAŁ Roland Garros 2023): Dubois bt Petrescu  6-3  3-6  7-6(4)  6-4
INSERT INTO SET_MECZU (ID_Setu, ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
    (72, 21, 1, 6, 3, 0),
    (73, 21, 2, 3, 6, 0),   -- Petrescu wygrywa ten set
    (74, 21, 3, 7, 6, 1),
    (75, 21, 4, 6, 4, 0);

-- =============================================================================
-- EOF
-- Podsumowanie:
--   Turnieje:        2
--   Edycje:          3
--   Zawodnicy:      13  (z czego 7 gra w więcej niż 1 turnieju)
--   Udziały:        24
--   Mecze:          21
--   Powiązania:     42  (MECZ_UDZIAL)
--   Sety:           75
-- =============================================================================
