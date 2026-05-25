-- =============================================================================
-- TURNIEJE_TENISA – Skrypt danych ćwiczebnych
-- Silnik: MariaDB 10.4 / InnoDB
-- Wykonywać PO: turnieje_tenisa_ddl.sql  (tabele muszą już istnieć)
-- =============================================================================
--
-- Scenariusz danych:
--   • 4 turnieje: Wimbledon, Roland Garros, US Open, Warsaw Open
--   • 6 edycji: 2024 (wszystkie 4) + 2025 (Wimbledon, US Open)
--   • 12 zawodników: 8 mężczyzn + 4 kobiety z 7 krajów
--   • 27 udziałów w edycjach
--   • 15 meczów (singiel BO3/BO5 i debel BO3)
--   • 45 setów + szczegółowe gemy dla 3 wybranych setów
--
-- Przydatne zapytania (przykłady):
--   – Finaliści każdego turnieju
--   – Zawodnicy z > 1 tytułem Grand Slam
--   – Mecze z tie-breakiem
--   – Statystyki gemów (% przełamań serwisu)
--   – Ranking aktywności zawodników (liczba meczów)
-- =============================================================================

USE turnieje_tenisa;

SET FOREIGN_KEY_CHECKS = 0;

-- =============================================================================
-- 1. KRAJ
--    Kontynent: tylko wartości z CHECK w DDL
-- =============================================================================
INSERT INTO KRAJ (Kod_ISO, Nazwa, Kontynent) VALUES
    ('AUS', 'Australia',         'Australia i Oceania'),
    ('ESP', 'Hiszpania',         'Europa'),
    ('FRA', 'Francja',           'Europa'),
    ('GBR', 'Wielka Brytania',   'Europa'),
    ('POL', 'Polska',            'Europa'),
    ('SRB', 'Serbia',            'Europa'),
    ('USA', 'Stany Zjednoczone', 'Ameryka Północna');

-- =============================================================================
-- 2. TURNIEJ
--    ID_Turnieju (AUTO_INCREMENT): 1=Wimbledon, 2=Roland Garros,
--                                  3=US Open,   4=Warsaw Open
-- =============================================================================
INSERT INTO TURNIEJ (Kod_ISO, Nazwa, Miasto, Nawierzchnia) VALUES
    ('GBR', 'Wimbledon Championships', 'Londyn',    'Trawa'),
    ('FRA', 'Roland Garros',           'Paryż',     'Ziemna'),
    ('USA', 'US Open',                 'Nowy Jork', 'Twarda'),
    ('POL', 'Warsaw Open',             'Warszawa',  'Ziemna');

-- =============================================================================
-- 3. EDYCJA_TURNIEJU
--    Trigger: YEAR(Data_rozpoczecia) = Rok  ← obowiązuje!
--    ID_Edycji: 1=Wimbledon2024, 2=RolandGarros2024, 3=USOpen2024,
--               4=WarsawOpen2024, 5=Wimbledon2025,   6=USOpen2025
-- =============================================================================
INSERT INTO EDYCJA_TURNIEJU (ID_Turnieju, Rok, Data_rozpoczecia, Data_zakonczenia) VALUES
    (1, 2024, '2024-07-01', '2024-07-14'),   -- ID_Edycji=1 : Wimbledon 2024
    (2, 2024, '2024-05-26', '2024-06-09'),   -- ID_Edycji=2 : Roland Garros 2024
    (3, 2024, '2024-08-26', '2024-09-08'),   -- ID_Edycji=3 : US Open 2024
    (4, 2024, '2024-07-22', '2024-07-28'),   -- ID_Edycji=4 : Warsaw Open 2024
    (1, 2025, '2025-06-30', '2025-07-13'),   -- ID_Edycji=5 : Wimbledon 2025
    (3, 2025, '2025-08-25', '2025-09-07');   -- ID_Edycji=6 : US Open 2025

-- =============================================================================
-- 4. ZAWODNIK
--    8 mężczyzn (ITF-M0001..0008) + 4 kobiety (ITF-F0001..0004)
--    Trigger: Data_urodzenia <= CURDATE()
-- =============================================================================
INSERT INTO ZAWODNIK (Nr_Licencji_ITF, Plec, Kod_ISO, Imie, Nazwisko, Data_urodzenia) VALUES
    ('ITF-M0001', 'Mężczyzna', 'SRB', 'Stefan',      'Branković',  '1988-11-08'),
    ('ITF-M0002', 'Mężczyzna', 'ESP', 'Carlos',      'Mendoza',    '1997-06-20'),
    ('ITF-M0003', 'Mężczyzna', 'POL', 'Aleksander',  'Kowalski',   '1995-03-15'),
    ('ITF-M0004', 'Mężczyzna', 'GBR', 'James',       'Harrison',   '1993-04-22'),
    ('ITF-M0005', 'Mężczyzna', 'FRA', 'Pierre',      'Dupont',     '1999-01-30'),
    ('ITF-M0006', 'Mężczyzna', 'USA', 'Michael',     'Turner',     '1991-07-12'),
    ('ITF-M0007', 'Mężczyzna', 'AUS', 'Liam',        'Fletcher',   '2001-09-05'),
    ('ITF-M0008', 'Mężczyzna', 'POL', 'Piotr',       'Wiśniewski', '1996-12-18'),
    ('ITF-F0001', 'Kobieta',   'POL', 'Iga',         'Świątek',    '2001-05-31'),
    ('ITF-F0002', 'Kobieta',   'ESP', 'Sofia',       'García',     '2000-08-23'),
    ('ITF-F0003', 'Kobieta',   'GBR', 'Serena',      'Williams',   '1981-09-26'),
    ('ITF-F0004', 'Kobieta',   'USA', 'Jessica',     'Adams',      '1995-11-30');

-- =============================================================================
-- 5. UDZIAL
--    • Unikalny na (Nr_Licencji_ITF, ID_Edycji)
--    • Trigger: Data_rejestracji BETWEEN Data_urodzenia AND Data_zakonczenia_edycji
--    • Rozstawienie > 0, Ranking > 0
-- =============================================================================

-- ---- Wimbledon 2024 (ID_Edycji=1) – singiel mężczyzn i kobiet ---------------
--  ID_Udzialu=1..7
INSERT INTO UDZIAL
    (Nr_Licencji_ITF, ID_Edycji, Status,           Rozstawienie, Ranking_w_momencie_rejestracji, Data_rejestracji)
VALUES
    ('ITF-M0001', 1, 'Główna drabinka',  1,  3, '2024-06-01'),  -- Branković
    ('ITF-M0002', 1, 'Główna drabinka',  2,  8, '2024-06-01'),  -- Mendoza
    ('ITF-M0003', 1, 'Główna drabinka',  4, 15, '2024-06-05'),  -- Kowalski
    ('ITF-M0004', 1, 'Główna drabinka',  3, 12, '2024-06-05'),  -- Harrison
    ('ITF-F0001', 1, 'Główna drabinka',  1,  5, '2024-06-01'),  -- Nowak
    ('ITF-F0002', 1, 'Główna drabinka',  2,  9, '2024-06-01'),  -- García
    ('ITF-F0003', 1, 'Dzika karta',      3, 11, '2024-06-03');  -- Williams (wildcard gospodarzy)

-- ---- Roland Garros 2024 (ID_Edycji=2) – singiel mężczyzn -------------------
--  ID_Udzialu=8..12
INSERT INTO UDZIAL
    (Nr_Licencji_ITF, ID_Edycji, Status,           Rozstawienie, Ranking_w_momencie_rejestracji, Data_rejestracji)
VALUES
    ('ITF-M0001', 2, 'Główna drabinka',  1,  3, '2024-04-20'),  -- Branković
    ('ITF-M0002', 2, 'Główna drabinka',  2,  8, '2024-04-20'),  -- Mendoza
    ('ITF-M0005', 2, 'Główna drabinka',  3, 13, '2024-04-22'),  -- Dupont
    ('ITF-M0006', 2, 'Główna drabinka',  4, 16, '2024-04-22'),  -- Turner
    ('ITF-M0003', 2, 'Główna drabinka',  5, 20, '2024-04-25');  -- Kowalski (odpadł w 1. rundzie, brak meczu)

-- ---- US Open 2024 (ID_Edycji=3) – debel mężczyzn ---------------------------
--  ID_Udzialu=13..16
INSERT INTO UDZIAL
    (Nr_Licencji_ITF, ID_Edycji, Status,           Rozstawienie, Ranking_w_momencie_rejestracji, Data_rejestracji)
VALUES
    ('ITF-M0006', 3, 'Główna drabinka',  1,  5, '2024-07-01'),  -- Turner
    ('ITF-M0003', 3, 'Główna drabinka',  2, 15, '2024-07-01'),  -- Kowalski
    ('ITF-M0002', 3, 'Główna drabinka',  3,  8, '2024-07-01'),  -- Mendoza
    ('ITF-M0007', 3, 'Lucky Loser',      4, 22, '2024-07-01');  -- Fletcher

-- ---- Warsaw Open 2024 (ID_Edycji=4) – singiel mężczyzn ---------------------
--  ID_Udzialu=17..20
INSERT INTO UDZIAL
    (Nr_Licencji_ITF, ID_Edycji, Status,           Rozstawienie, Ranking_w_momencie_rejestracji, Data_rejestracji)
VALUES
    ('ITF-M0003', 4, 'Główna drabinka',  1, 15, '2024-07-01'),  -- Kowalski
    ('ITF-M0008', 4, 'Główna drabinka',  2, 30, '2024-07-01'),  -- Wiśniewski
    ('ITF-M0005', 4, 'Główna drabinka',  3, 13, '2024-07-01'),  -- Dupont
    ('ITF-M0007', 4, 'Lucky Loser',      4, 22, '2024-07-01');  -- Fletcher

-- ---- Wimbledon 2025 (ID_Edycji=5) – singiel mężczyzn -----------------------
--  ID_Udzialu=21..23
INSERT INTO UDZIAL
    (Nr_Licencji_ITF, ID_Edycji, Status,           Rozstawienie, Ranking_w_momencie_rejestracji, Data_rejestracji)
VALUES
    ('ITF-M0001', 5, 'Główna drabinka',  1,  2, '2025-06-01'),  -- Branković
    ('ITF-M0002', 5, 'Główna drabinka',  2,  6, '2025-06-01'),  -- Mendoza
    ('ITF-M0003', 5, 'Główna drabinka',  3, 11, '2025-06-05');  -- Kowalski

-- ---- US Open 2025 (ID_Edycji=6) – singiel kobiet ----------------------------
--  ID_Udzialu=24..27
INSERT INTO UDZIAL
    (Nr_Licencji_ITF, ID_Edycji, Status,           Rozstawienie, Ranking_w_momencie_rejestracji, Data_rejestracji)
VALUES
    ('ITF-F0001', 6, 'Główna drabinka',  1,  4, '2025-07-01'),  -- Nowak
    ('ITF-F0002', 6, 'Główna drabinka',  2,  7, '2025-07-01'),  -- García
    ('ITF-F0003', 6, 'Główna drabinka',  3, 10, '2025-07-01'),  -- Williams
    ('ITF-F0004', 6, 'Główna drabinka',  4, 18, '2025-07-01');  -- Adams

-- =============================================================================
-- 6. MECZ
--    Trigger: Data BETWEEN Data_rozpoczecia AND Data_zakonczenia edycji
--    ID_Rodzaju: 1=Singiel BO3, 2=Singiel BO5, 3=Debel BO3
-- =============================================================================
INSERT INTO MECZ
    (ID_Edycji, ID_Rodzaju, Runda,        Data,         Kort,                      Czas_trwania_min, Zwycięska_strona)
VALUES
-- === Wimbledon 2024 (edycja 1: 2024-07-01..2024-07-14) ===
-- ID_Meczu=1 : Półfinał singiel M – Branković(A) vs Mendoza(B)
    (1, 2, 'Półfinał',    '2024-07-05', 'Court Central',           135, 'A'),
-- ID_Meczu=2 : Półfinał singiel M – Kowalski(A) vs Harrison(B)
    (1, 2, 'Półfinał',    '2024-07-05', 'Court 1',                 155, 'A'),
-- ID_Meczu=3 : Finał singiel M – Branković(A) vs Kowalski(B)  → Branković zwycięża
    (1, 2, 'Finał',       '2024-07-07', 'Court Central',           195, 'A'),
-- ID_Meczu=4 : Finał singiel K – Nowak(A) vs García(B)
    (1, 1, 'Finał',       '2024-07-06', 'Court Central',            68, 'A'),

-- === Roland Garros 2024 (edycja 2: 2024-05-26..2024-06-09) ===
-- ID_Meczu=5 : Ćwierćfinał singiel M – Branković(A) vs Turner(B)
    (2, 2, 'Ćwierćfinał', '2024-06-04', 'Court Philippe-Chatrier',  98, 'A'),
-- ID_Meczu=6 : Ćwierćfinał singiel M – Mendoza(A) vs Dupont(B)
    (2, 2, 'Ćwierćfinał', '2024-06-04', 'Court Suzanne-Lenglen',   148, 'A'),
-- ID_Meczu=7 : Finał singiel M – Branković(A) vs Mendoza(B)
    (2, 2, 'Finał',        '2024-06-09', 'Court Philippe-Chatrier', 102, 'A'),

-- === US Open 2024 (edycja 3: 2024-08-26..2024-09-08) ===
-- ID_Meczu=8 : Finał debel M – Turner+Kowalski(A) vs Mendoza+Fletcher(B)
    (3, 3, 'Finał',       '2024-09-06', 'Arthur Ashe Stadium',      85, 'A'),

-- === Warsaw Open 2024 (edycja 4: 2024-07-22..2024-07-28) ===
-- ID_Meczu=9  : 1. runda singiel M – Kowalski(A) vs Wiśniewski(B)
    (4, 1, '1. runda',    '2024-07-23', 'Kort Centralny',            72, 'A'),
-- ID_Meczu=10 : 1. runda singiel M – Dupont(A) vs Fletcher(B)
    (4, 1, '1. runda',    '2024-07-23', 'Kort 1',                   88, 'A'),
-- ID_Meczu=11 : Finał singiel M – Kowalski(A) vs Dupont(B)
    (4, 1, 'Finał',       '2024-07-27', 'Kort Centralny',           110, 'A'),

-- === Wimbledon 2025 (edycja 5: 2025-06-30..2025-07-13) ===
-- ID_Meczu=12 : Półfinał singiel M – Branković(A) vs Mendoza(B)
    (5, 2, 'Półfinał',    '2025-07-04', 'Court Central',             95, 'A'),
-- ID_Meczu=13 : Finał singiel M – Branković(A) vs Kowalski(B)  → Kowalski rewanżuje się!
    (5, 2, 'Finał',       '2025-07-06', 'Court Central',            140, 'B'),

-- === US Open 2025 (edycja 6: 2025-08-25..2025-09-07) ===
-- ID_Meczu=14 : Półfinał singiel K – Nowak(A) vs Adams(B)
    (6, 1, 'Półfinał',    '2025-09-04', 'Arthur Ashe Stadium',       78, 'A'),
-- ID_Meczu=15 : Finał singiel K – Nowak(A) vs García(B)
    (6, 1, 'Finał',       '2025-09-06', 'Arthur Ashe Stadium',       88, 'A');

-- =============================================================================
-- 7. MECZ_UDZIAL  (tabela asocjacyjna Mecz <-> Udział)
--    Singiel: 2 wiersze / mecz  |  Debel: 4 wiersze / mecz
--    Trigger: maks. Zawodnikow_na_strone udziałów na jedną stronę
-- =============================================================================
INSERT INTO MECZ_UDZIAL (ID_Meczu, ID_Udzialu, Strona) VALUES
-- M1  Wimbledon 2024 Półfinał M  : Branković(udz=1) A  vs  Mendoza(udz=2) B
    (1,  1, 'A'), (1,  2, 'B'),
-- M2  Wimbledon 2024 Półfinał M  : Kowalski(udz=3) A  vs  Harrison(udz=4) B
    (2,  3, 'A'), (2,  4, 'B'),
-- M3  Wimbledon 2024 Finał M     : Branković(udz=1) A  vs  Kowalski(udz=3) B
    (3,  1, 'A'), (3,  3, 'B'),
-- M4  Wimbledon 2024 Finał K     : Nowak(udz=5) A     vs  García(udz=6) B
    (4,  5, 'A'), (4,  6, 'B'),
-- M5  Roland Garros 2024 QF      : Branković(udz=8) A  vs  Turner(udz=11) B
    (5,  8, 'A'), (5, 11, 'B'),
-- M6  Roland Garros 2024 QF      : Mendoza(udz=9) A   vs  Dupont(udz=10) B
    (6,  9, 'A'), (6, 10, 'B'),
-- M7  Roland Garros 2024 Finał   : Branković(udz=8) A  vs  Mendoza(udz=9) B
    (7,  8, 'A'), (7,  9, 'B'),
-- M8  US Open 2024 Finał DEBEL   : Turner(13)+Kowalski(14) A  vs  Mendoza(15)+Fletcher(16) B
    (8, 13, 'A'), (8, 14, 'A'), (8, 15, 'B'), (8, 16, 'B'),
-- M9  Warsaw Open 2024 1. runda  : Kowalski(udz=17) A  vs  Wiśniewski(udz=18) B
    (9,  17, 'A'), (9,  18, 'B'),
-- M10 Warsaw Open 2024 1. runda  : Dupont(udz=19) A    vs  Fletcher(udz=20) B
    (10, 19, 'A'), (10, 20, 'B'),
-- M11 Warsaw Open 2024 Finał     : Kowalski(udz=17) A  vs  Dupont(udz=19) B
    (11, 17, 'A'), (11, 19, 'B'),
-- M12 Wimbledon 2025 Półfinał    : Branković(udz=21) A  vs  Mendoza(udz=22) B
    (12, 21, 'A'), (12, 22, 'B'),
-- M13 Wimbledon 2025 Finał       : Branković(udz=21) A  vs  Kowalski(udz=23) B  ← B wygrywa
    (13, 21, 'A'), (13, 23, 'B'),
-- M14 US Open 2025 Półfinał K    : Nowak(udz=24) A     vs  Adams(udz=27) B
    (14, 24, 'A'), (14, 27, 'B'),
-- M15 US Open 2025 Finał K       : Nowak(udz=24) A     vs  García(udz=25) B
    (15, 24, 'A'), (15, 25, 'B');

-- =============================================================================
-- 8. SET_MECZU
--    ID_Setu AUTO_INCREMENT – kolejność wstawiania = klucz.
--    M1 -> 1..4, M2 -> 5..8, etc.
-- =============================================================================
INSERT INTO SET_MECZU (ID_Meczu, Numer_setu, Wynik_A, Wynik_B, Tie_break) VALUES
-- M1 (Branković 3:1 Mendoza) ── ID_Setu 1..4
    (1, 1, 6, 4, FALSE),
    (1, 2, 3, 6, FALSE),   -- Mendoza wygrał ten set
    (1, 3, 6, 3, FALSE),
    (1, 4, 6, 2, FALSE),
-- M2 (Kowalski 3:1 Harrison) ── ID_Setu 5..8
    (2, 1, 7, 5, FALSE),
    (2, 2, 6, 4, FALSE),
    (2, 3, 6, 7, TRUE),    -- Harrison wygrał w tie-breaku
    (2, 4, 6, 3, FALSE),
-- M3 (Branković 3:2 Kowalski) ── ID_Setu 9..13
    (3, 1, 6, 3, FALSE),
    (3, 2, 6, 7, TRUE),    -- Kowalski wygrał w tie-breaku
    (3, 3, 4, 6, FALSE),   -- Kowalski wygrał
    (3, 4, 7, 5, FALSE),
    (3, 5, 6, 4, FALSE),
-- M4 (Nowak 2:0 García) ── ID_Setu 14..15
    (4, 1, 6, 4, FALSE),
    (4, 2, 6, 2, FALSE),
-- M5 (Branković 3:0 Turner) ── ID_Setu 16..18
    (5, 1, 6, 2, FALSE),
    (5, 2, 6, 4, FALSE),
    (5, 3, 7, 5, FALSE),
-- M6 (Mendoza 3:1 Dupont) ── ID_Setu 19..22
    (6, 1, 4, 6, FALSE),   -- Dupont wygrał
    (6, 2, 6, 3, FALSE),
    (6, 3, 6, 1, FALSE),
    (6, 4, 6, 4, FALSE),
-- M7 (Branković 3:0 Mendoza) ── ID_Setu 23..25
    (7, 1, 6, 4, FALSE),
    (7, 2, 6, 2, FALSE),
    (7, 3, 6, 3, FALSE),
-- M8 debel (Turner+Kowalski 2:0 Mendoza+Fletcher) ── ID_Setu 26..27
    (8, 1, 6, 4, FALSE),
    (8, 2, 7, 6, TRUE),    -- A wygrał w tie-breaku
-- M9 (Kowalski 2:0 Wiśniewski) ── ID_Setu 28..29
    (9, 1, 6, 3, FALSE),
    (9, 2, 6, 4, FALSE),
-- M10 (Dupont 2:0 Fletcher) ── ID_Setu 30..31
    (10, 1, 7, 5, FALSE),
    (10, 2, 6, 4, FALSE),
-- M11 (Kowalski 2:1 Dupont) ── ID_Setu 32..34
    (11, 1, 6, 4, FALSE),
    (11, 2, 4, 6, FALSE),  -- Dupont wygrał
    (11, 3, 7, 5, FALSE),
-- M12 (Branković 3:0 Mendoza) ── ID_Setu 35..37
    (12, 1, 6, 3, FALSE),
    (12, 2, 6, 4, FALSE),
    (12, 3, 6, 2, FALSE),
-- M13 (Kowalski B 3:1 Branković A) ── ID_Setu 38..41
    (13, 1, 3, 6, FALSE),  -- Kowalski wygrał
    (13, 2, 6, 4, FALSE),  -- Branković wygrał
    (13, 3, 4, 6, FALSE),  -- Kowalski wygrał
    (13, 4, 3, 6, FALSE),  -- Kowalski wygrał → 3:1 dla B
-- M14 (Nowak 2:0 Adams) ── ID_Setu 42..43
    (14, 1, 6, 3, FALSE),
    (14, 2, 6, 4, FALSE),
-- M15 (Nowak 2:0 García) ── ID_Setu 44..45
    (15, 1, 7, 5, FALSE),
    (15, 2, 6, 4, FALSE);

-- =============================================================================
-- 9. GEM
--    Punkty_A / Punkty_B = liczba punktów wygranych w gemie.
--    Serwujący alternuje gem po gemie; komentarz pokazuje wynik seta po gemie.
-- =============================================================================

-- ---- ID_Setu=14 : M4 Set 1, Nowak(A) vs García(B), 6-4 --------------------
--      García serwuje jako pierwsza (B).
INSERT INTO GEM (ID_Setu, Numer_gemu, Serwujacy, Punkty_A, Punkty_B) VALUES
    (14,  1, 'B', 2, 4),   -- B trzyma serwis             → 0-1
    (14,  2, 'A', 4, 1),   -- A trzyma serwis             → 1-1
    (14,  3, 'B', 4, 2),   -- A przełamuje serwis B       → 2-1
    (14,  4, 'A', 4, 2),   -- A trzyma                    → 3-1
    (14,  5, 'B', 2, 4),   -- B trzyma                    → 3-2
    (14,  6, 'A', 4, 2),   -- A trzyma                    → 4-2
    (14,  7, 'B', 1, 4),   -- B trzyma                    → 4-3
    (14,  8, 'A', 2, 4),   -- B przełamuje serwis A       → 4-4
    (14,  9, 'B', 4, 3),   -- A przełamuje serwis B       → 5-4
    (14, 10, 'A', 4, 1);   -- A trzyma, set zakończony    → 6-4 ✓

-- ---- ID_Setu=28 : M9 Set 1, Kowalski(A) vs Wiśniewski(B), 6-3 -------------
--      Wiśniewski serwuje jako pierwszy (B).
INSERT INTO GEM (ID_Setu, Numer_gemu, Serwujacy, Punkty_A, Punkty_B) VALUES
    (28,  1, 'B', 4, 2),   -- A przełamuje serwis B       → 1-0
    (28,  2, 'A', 4, 1),   -- A trzyma                    → 2-0
    (28,  3, 'B', 2, 4),   -- B trzyma                    → 2-1
    (28,  4, 'A', 4, 2),   -- A trzyma                    → 3-1
    (28,  5, 'B', 2, 4),   -- B trzyma                    → 3-2
    (28,  6, 'A', 4, 1),   -- A trzyma                    → 4-2
    (28,  7, 'B', 4, 3),   -- A przełamuje serwis B       → 5-2
    (28,  8, 'A', 2, 4),   -- B przełamuje serwis A       → 5-3
    (28,  9, 'B', 4, 2);   -- A przełamuje, set zakończony → 6-3 ✓

-- ---- ID_Setu=29 : M9 Set 2, Kowalski(A) vs Wiśniewski(B), 6-4 -------------
--      Kowalski serwuje jako pierwszy (A) – naprzemienność między setami.
INSERT INTO GEM (ID_Setu, Numer_gemu, Serwujacy, Punkty_A, Punkty_B) VALUES
    (29,  1, 'A', 4, 1),   -- A trzyma                    → 1-0
    (29,  2, 'B', 2, 4),   -- B trzyma                    → 1-1
    (29,  3, 'A', 4, 2),   -- A trzyma                    → 2-1
    (29,  4, 'B', 4, 3),   -- A przełamuje serwis B       → 3-1
    (29,  5, 'A', 4, 1),   -- A trzyma                    → 4-1
    (29,  6, 'B', 1, 4),   -- B trzyma                    → 4-2
    (29,  7, 'A', 2, 4),   -- B przełamuje serwis A       → 4-3
    (29,  8, 'B', 1, 4),   -- B trzyma                    → 4-4
    (29,  9, 'A', 4, 2),   -- A trzyma                    → 5-4
    (29, 10, 'B', 4, 3);   -- A przełamuje, set zakończony → 6-4 ✓

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- PRZYKŁADOWE ZAPYTANIA DO ĆWICZEŃ
-- =============================================================================

-- 1. Finaliści każdej edycji turnieju (mecze finałowe + strony):
--    SELECT t.Nazwa, e.Rok, z.Imie, z.Nazwisko, mu.Strona, m.Zwycięska_strona
--    FROM MECZ m
--    JOIN EDYCJA_TURNIEJU e  ON e.ID_Edycji = m.ID_Edycji
--    JOIN TURNIEJ t          ON t.ID_Turnieju = e.ID_Turnieju
--    JOIN MECZ_UDZIAL mu     ON mu.ID_Meczu = m.ID_Meczu
--    JOIN UDZIAL u           ON u.ID_Udzialu = mu.ID_Udzialu
--    JOIN ZAWODNIK z         ON z.Nr_Licencji_ITF = u.Nr_Licencji_ITF
--    WHERE m.Runda = 'Finał';

-- 2. Zawodnicy, którzy wygrali więcej niż jeden finał:
--    SELECT z.Imie, z.Nazwisko, COUNT(*) AS tytuly
--    FROM MECZ m
--    JOIN MECZ_UDZIAL mu ON mu.ID_Meczu = m.ID_Meczu AND mu.Strona = m.Zwycięska_strona
--    JOIN UDZIAL u       ON u.ID_Udzialu = mu.ID_Udzialu
--    JOIN ZAWODNIK z     ON z.Nr_Licencji_ITF = u.Nr_Licencji_ITF
--    WHERE m.Runda = 'Finał'
--    GROUP BY z.Nr_Licencji_ITF
--    HAVING tytuly > 1;

-- 3. Mecze z tie-breakiem:
--    SELECT m.ID_Meczu, t.Nazwa, e.Rok, m.Runda, COUNT(*) AS ilosc_tie_breakow
--    FROM SET_MECZU s
--    JOIN MECZ m             ON m.ID_Meczu = s.ID_Meczu
--    JOIN EDYCJA_TURNIEJU e  ON e.ID_Edycji = m.ID_Edycji
--    JOIN TURNIEJ t          ON t.ID_Turnieju = e.ID_Turnieju
--    WHERE s.Tie_break = TRUE
--    GROUP BY m.ID_Meczu;

-- 4. Procent przełamań serwisu (break pointów) per zawodnik:
--    Przeliczane na podstawie tabeli GEM – jeśli Serwujacy='A' i Punkty_B > Punkty_A
--    → B przełamało; analogicznie gdy Serwujacy='B' i Punkty_A > Punkty_B → A przełamało.

-- 5. Ranking aktywności zawodników (liczba rozegranych meczów):
--    SELECT z.Imie, z.Nazwisko, k.Nazwa AS kraj, COUNT(DISTINCT mu.ID_Meczu) AS mecze
--    FROM ZAWODNIK z
--    JOIN KRAJ k    ON k.Kod_ISO = z.Kod_ISO
--    JOIN UDZIAL u  ON u.Nr_Licencji_ITF = z.Nr_Licencji_ITF
--    JOIN MECZ_UDZIAL mu ON mu.ID_Udzialu = u.ID_Udzialu
--    GROUP BY z.Nr_Licencji_ITF
--    ORDER BY mecze DESC;

-- =============================================================================
-- EOF
-- =============================================================================
