-- =============================================================================
-- TURNIEJE_TENISA – Skrypt DDL
-- Silnik: MariaDB 10.4 / InnoDB
-- Kodowanie: utf8mb4
-- =============================================================================

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS turnieje_tenisa
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_polish_ci;

USE turnieje_tenisa;

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- Usunięcie tabel (odwrotna kolejność FK)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS GEM;
DROP TABLE IF EXISTS SET_MECZU;
DROP TABLE IF EXISTS MECZ_UDZIAL;
DROP TABLE IF EXISTS MECZ;
DROP TABLE IF EXISTS UDZIAL;
DROP TABLE IF EXISTS STATUS;
DROP TABLE IF EXISTS ZAWODNIK;
DROP TABLE IF EXISTS PLEC;
DROP TABLE IF EXISTS EDYCJA_TURNIEJU;
DROP TABLE IF EXISTS TURNIEJ;
DROP TABLE IF EXISTS KRAJ;
DROP TABLE IF EXISTS RODZAJ_MECZU;

-- =============================================================================
-- TABELE SŁOWNIKOWE
-- =============================================================================

-- -----------------------------------------------------------------------------
-- KRAJ
-- -----------------------------------------------------------------------------
CREATE TABLE KRAJ (
    Kod_ISO   CHAR(3)      NOT NULL,
    Nazwa     VARCHAR(100) NOT NULL,
    Kontynent VARCHAR(50)  NOT NULL,

    CONSTRAINT PK_KRAJ PRIMARY KEY (Kod_ISO),
    CONSTRAINT CHK_KRAJ_Kontynent CHECK (
        Kontynent IN ('Afryka','Ameryka Północna','Ameryka Południowa',
                      'Azja','Australia i Oceania','Europa')
    )
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_polish_ci
  COMMENT='Kraje uczestniczące w turniejach – ISO 3166-1 alpha-3';

-- -----------------------------------------------------------------------------
-- PLEC
-- Alternatywą byłby ENUM bezpośrednio w ZAWODNIK.
-- -----------------------------------------------------------------------------
CREATE TABLE PLEC (
    Nazwa VARCHAR(20) NOT NULL,

    CONSTRAINT PK_PLEC PRIMARY KEY (Nazwa)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_polish_ci
  COMMENT='Słownik płci zawodników';

-- -----------------------------------------------------------------------------
-- STATUS (uczestnictwa w turnieju)
-- -----------------------------------------------------------------------------
CREATE TABLE STATUS (
    Status VARCHAR(30) NOT NULL,

    CONSTRAINT PK_STATUS PRIMARY KEY (Status)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_polish_ci
  COMMENT='Słownik statusów udziału (np. Główna drabinka, Kwalifikacje, Dzika karta)';

-- -----------------------------------------------------------------------------
-- RODZAJ_MECZU
-- -----------------------------------------------------------------------------
CREATE TABLE RODZAJ_MECZU (
    ID_Rodzaju          INT          NOT NULL AUTO_INCREMENT,
    Nazwa               VARCHAR(50)  NOT NULL,
    Sety_do_wygrania    INT          NOT NULL,
    Zawodnikow_na_strone INT         NOT NULL,

    CONSTRAINT PK_RODZAJ_MECZU PRIMARY KEY (ID_Rodzaju),
    CONSTRAINT CHK_RODZAJ_Sety CHECK (Sety_do_wygrania IN (2, 3)),
    CONSTRAINT CHK_RODZAJ_Zawodnicy CHECK (Zawodnikow_na_strone IN (1, 2))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_polish_ci
  COMMENT='Rodzaje meczu: singiel bo3, singiel bo5, debel bo3 itd.';

-- =============================================================================
-- TURNIEJE
-- =============================================================================

-- -----------------------------------------------------------------------------
-- TURNIEJ
-- -----------------------------------------------------------------------------
CREATE TABLE TURNIEJ (
    ID_Turnieju  INT          NOT NULL AUTO_INCREMENT,
    Kod_ISO      CHAR(3)      NOT NULL  COMMENT 'Kraj organizatora',
    Nazwa        VARCHAR(100) NOT NULL,
    Miasto       VARCHAR(100) NOT NULL,
    Nawierzchnia ENUM('Twarda','Twarda (hala)','Ziemna','Trawa','Dywan') NOT NULL,

    CONSTRAINT PK_TURNIEJ  PRIMARY KEY (ID_Turnieju),
    CONSTRAINT FK_TURNIEJ_KRAJ FOREIGN KEY (Kod_ISO)
        REFERENCES KRAJ (Kod_ISO)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_polish_ci
  COMMENT='Turnieje tenisowe (stałe cechy, niezależne od edycji)';

-- -----------------------------------------------------------------------------
-- EDYCJA_TURNIEJU
-- UNIQUE(ID_Turnieju, Rok) – jeden turniej, jedna edycja rocznie.
-- CHECK Data_zakonczenia >= Data_rozpoczecia.
-- -----------------------------------------------------------------------------
CREATE TABLE EDYCJA_TURNIEJU (
    ID_Edycji         INT  NOT NULL AUTO_INCREMENT,
    ID_Turnieju       INT  NOT NULL,
    Rok               INT  NOT NULL,
    Data_rozpoczecia  DATE NOT NULL,
    Data_zakonczenia  DATE NOT NULL,

    CONSTRAINT PK_EDYCJA_TURNIEJU PRIMARY KEY (ID_Edycji),
    CONSTRAINT UQ_EDYCJA_TURNIEJU_ROK UNIQUE (ID_Turnieju, Rok),
    CONSTRAINT FK_EDYCJA_TURNIEJ FOREIGN KEY (ID_Turnieju)
        REFERENCES TURNIEJ (ID_Turnieju)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT CHK_EDYCJA_Daty CHECK (Data_zakonczenia >= Data_rozpoczecia),
    CONSTRAINT CHK_EDYCJA_Rok  CHECK (Rok BETWEEN 1877 AND 2100)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_polish_ci
  COMMENT='Edycja roczna turnieju';

-- =============================================================================
-- ZAWODNICY
-- =============================================================================

-- -----------------------------------------------------------------------------
-- ZAWODNIK
-- -----------------------------------------------------------------------------
CREATE TABLE ZAWODNIK (
    Nr_Licencji_ITF  VARCHAR(20)  NOT NULL,
    Plec             VARCHAR(20)  NOT NULL,
    Kod_ISO          CHAR(3)      NOT NULL  COMMENT 'Reprezentowany kraj',
    Imie             VARCHAR(50)  NOT NULL,
    Nazwisko         VARCHAR(50)  NOT NULL,
    Data_urodzenia   DATE         NOT NULL,

    CONSTRAINT PK_ZAWODNIK PRIMARY KEY (Nr_Licencji_ITF),
    CONSTRAINT FK_ZAWODNIK_PLEC FOREIGN KEY (Plec)
        REFERENCES PLEC (Nazwa)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT FK_ZAWODNIK_KRAJ FOREIGN KEY (Kod_ISO)
        REFERENCES KRAJ (Kod_ISO)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_polish_ci
  COMMENT='Zawodnik tenisowy – klucz główny: numer licencji ITF';

-- -----------------------------------------------------------------------------
-- UDZIAL
-- UNIQUE(Nr_Licencji_ITF, ID_Edycji) – zawodnik raz na edycję.
-- CHECK Rozstawienie > 0.
-- CHECK Ranking_w_momencie_rejestracji > 0.
-- -----------------------------------------------------------------------------
CREATE TABLE UDZIAL (
    ID_Udzialu                    INT          NOT NULL AUTO_INCREMENT,
    Nr_Licencji_ITF               VARCHAR(20)  NOT NULL,
    ID_Edycji                     INT          NOT NULL,
    Status                        VARCHAR(30)  NOT NULL,
    Rozstawienie                  INT          NOT NULL,
    Ranking_w_momencie_rejestracji INT         NOT NULL,
    Data_rejestracji              DATE         NOT NULL,

    CONSTRAINT PK_UDZIAL PRIMARY KEY (ID_Udzialu),
    CONSTRAINT UQ_UDZIAL_ZAWODNIK_EDYCJA UNIQUE (Nr_Licencji_ITF, ID_Edycji),
    CONSTRAINT FK_UDZIAL_ZAWODNIK FOREIGN KEY (Nr_Licencji_ITF)
        REFERENCES ZAWODNIK (Nr_Licencji_ITF)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT FK_UDZIAL_EDYCJA FOREIGN KEY (ID_Edycji)
        REFERENCES EDYCJA_TURNIEJU (ID_Edycji)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT FK_UDZIAL_STATUS FOREIGN KEY (Status)
        REFERENCES STATUS (Status)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT CHK_UDZIAL_Rozstawienie CHECK (Rozstawienie > 0),
    CONSTRAINT CHK_UDZIAL_Ranking      CHECK (Ranking_w_momencie_rejestracji > 0)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_polish_ci
  COMMENT='Udział zawodnika w edycji turnieju';

-- =============================================================================
-- MECZE
-- =============================================================================

-- -----------------------------------------------------------------------------
-- MECZ
-- Zwycięska_strona – CHECK (NULL dozwolony dla meczu niezakończonego).
-- Zwycięska_strona jest redundantna (da się wyliczyć z SET_MECZU),
--   zachowana świadomie dla wydajności zapytań.
-- -----------------------------------------------------------------------------
CREATE TABLE MECZ (
    ID_Meczu           INT          NOT NULL AUTO_INCREMENT,
    ID_Edycji          INT          NOT NULL,
    ID_Rodzaju         INT          NOT NULL,
    Runda              ENUM('Kwalifikacje R1','Kwalifikacje R2','Kwalifikacje R3',
                           '1. runda','2. runda','3. runda','4. runda',
                           '1/8 finału','Ćwierćfinał','Półfinał','Finał') NOT NULL,
    Data               DATE         NOT NULL,
    Kort               VARCHAR(50)  NOT NULL,
    Czas_trwania_min   INT          NULL      COMMENT 'NULL gdy mecz nie dobiegł końca',
    Zwycięska_strona   CHAR(1)      NULL      COMMENT 'A lub B; NULL gdy mecz w toku lub walkower',

    CONSTRAINT PK_MECZ PRIMARY KEY (ID_Meczu),
    CONSTRAINT FK_MECZ_EDYCJA FOREIGN KEY (ID_Edycji)
        REFERENCES EDYCJA_TURNIEJU (ID_Edycji)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT FK_MECZ_RODZAJ FOREIGN KEY (ID_Rodzaju)
        REFERENCES RODZAJ_MECZU (ID_Rodzaju)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT CHK_MECZ_Strona CHECK (Zwycięska_strona IS NULL OR Zwycięska_strona IN ('A','B')),
    CONSTRAINT CHK_MECZ_Czas   CHECK (Czas_trwania_min IS NULL OR Czas_trwania_min > 0)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_polish_ci
  COMMENT='Mecz w ramach edycji turnieju';

-- -----------------------------------------------------------------------------
-- MECZ_UDZIAL  (tabela asocjacyjna Mecz <-> Udział)
-- Liczba wierszy na mecz musi wynosić 2 (singiel) lub 4 (debel).
-- Ograniczenie to egzekwuje trigger TRG_MECZ_UDZIAL_LICZBA_STRON.
-- -----------------------------------------------------------------------------
CREATE TABLE MECZ_UDZIAL (
    ID_Meczu   INT    NOT NULL,
    ID_Udzialu INT    NOT NULL,
    Strona     CHAR(1) NOT NULL,

    CONSTRAINT PK_MECZ_UDZIAL PRIMARY KEY (ID_Meczu, ID_Udzialu),
    CONSTRAINT FK_MECZ_UDZIAL_MECZ FOREIGN KEY (ID_Meczu)
        REFERENCES MECZ (ID_Meczu)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT FK_MECZ_UDZIAL_UDZIAL FOREIGN KEY (ID_Udzialu)
        REFERENCES UDZIAL (ID_Udzialu)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT CHK_MECZ_UDZIAL_Strona CHECK (Strona IN ('A','B'))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_polish_ci
  COMMENT='Powiązanie meczu z udziałami (2 lub 4 wiersze na mecz)';

-- =============================================================================
-- STRUKTURA MECZU
-- =============================================================================

-- -----------------------------------------------------------------------------
-- SET_MECZU  
-- nazwa SET jest z modelu relacyjnego jest słowem kluczowym SQL
-- używamy SET_MECZU
-- -----------------------------------------------------------------------------
CREATE TABLE SET_MECZU (
    ID_Setu    INT     NOT NULL AUTO_INCREMENT,
    ID_Meczu   INT     NOT NULL,
    Numer_setu INT     NOT NULL,
    Wynik_A    INT     NULL     COMMENT 'NULL gdy set w toku',
    Wynik_B    INT     NULL     COMMENT 'NULL gdy set w toku',
    Tie_break  BOOLEAN NOT NULL DEFAULT FALSE,

    CONSTRAINT PK_SET_MECZU PRIMARY KEY (ID_Setu),
    CONSTRAINT UQ_SET_NUMER  UNIQUE (ID_Meczu, Numer_setu),
    CONSTRAINT FK_SET_MECZU  FOREIGN KEY (ID_Meczu)
        REFERENCES MECZ (ID_Meczu)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT CHK_SET_Wynik_A CHECK (Wynik_A IS NULL OR Wynik_A >= 0),
    CONSTRAINT CHK_SET_Wynik_B CHECK (Wynik_B IS NULL OR Wynik_B >= 0),
    CONSTRAINT CHK_SET_Numer   CHECK (Numer_setu BETWEEN 1 AND 5)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_polish_ci
  COMMENT='Sety w meczu (nazwa SET_MECZU – SET jest słowem kluczowym SQL)';

-- -----------------------------------------------------------------------------
-- GEM
-- -----------------------------------------------------------------------------
CREATE TABLE GEM (
    ID_Gemu    INT     NOT NULL AUTO_INCREMENT,
    ID_Setu    INT     NOT NULL,
    Numer_gemu INT     NOT NULL,
    Serwujacy  CHAR(1) NOT NULL,
    Punkty_A   INT     NULL     COMMENT 'NULL gdy gem w toku',
    Punkty_B   INT     NULL     COMMENT 'NULL gdy gem w toku',

    CONSTRAINT PK_GEM     PRIMARY KEY (ID_Gemu),
    CONSTRAINT UQ_GEM_NUMER UNIQUE (ID_Setu, Numer_gemu),
    CONSTRAINT FK_GEM_SET FOREIGN KEY (ID_Setu)
        REFERENCES SET_MECZU (ID_Setu)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT CHK_GEM_Serwujacy CHECK (Serwujacy IN ('A','B')),
    CONSTRAINT CHK_GEM_Punkty_A  CHECK (Punkty_A IS NULL OR Punkty_A >= 0),
    CONSTRAINT CHK_GEM_Punkty_B  CHECK (Punkty_B IS NULL OR Punkty_B >= 0),
    CONSTRAINT CHK_GEM_Numer     CHECK (Numer_gemu >= 1)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_polish_ci
  COMMENT='Gemy w secie';

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- Dane słownikowe
-- =============================================================================

INSERT INTO PLEC (Nazwa) VALUES
    ('Mężczyzna'),
    ('Kobieta'),
    ('Inna');

INSERT INTO STATUS (Status) VALUES
    ('Główna drabinka'),
    ('Kwalifikacje'),
    ('Dzika karta'),
    ('Lucky Loser'),
    ('Wycofał się');

INSERT INTO RODZAJ_MECZU (Nazwa, Sety_do_wygrania, Zawodnikow_na_strone) VALUES
    ('Singiel BO3', 2, 1),
    ('Singiel BO5', 3, 1),
    ('Debel BO3',   2, 2),
    ('Debel BO5',   3, 2),
    ('Mikst BO3',   2, 2);

-- =============================================================================
-- EOF
-- =============================================================================
