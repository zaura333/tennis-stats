-- =============================================================================
-- TURNIEJE_TENISA – Skrypt triggerów
-- Silnik: MariaDB 10.4 / InnoDB
-- Wykonujemy po turnieje_tenisa_ddl.sql
-- =============================================================================

DELIMITER $$

-- =============================================================================
-- TRG_EDYCJA_DATA_ROK
-- Walidacja: rok w Rok musi być zgodny z rokiem w Data_rozpoczecia.
-- Zapobiega rozbieżności np. Rok=2024 przy Data_rozpoczecia='2025-01-15'.
-- =============================================================================
DROP TRIGGER IF EXISTS TRG_EDYCJA_INSERT_DATA_ROK $$
CREATE TRIGGER TRG_EDYCJA_INSERT_DATA_ROK
BEFORE INSERT ON EDYCJA_TURNIEJU
FOR EACH ROW
BEGIN
    IF YEAR(NEW.Data_rozpoczecia) <> NEW.Rok THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Rok musi być zgodny z rokiem Data_rozpoczecia.';
    END IF;
END$$

DROP TRIGGER IF EXISTS TRG_EDYCJA_UPDATE_DATA_ROK $$
CREATE TRIGGER TRG_EDYCJA_UPDATE_DATA_ROK
BEFORE UPDATE ON EDYCJA_TURNIEJU
FOR EACH ROW
BEGIN
    IF YEAR(NEW.Data_rozpoczecia) <> NEW.Rok THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Rok musi być zgodny z rokiem Data_rozpoczecia.';
    END IF;
END$$

-- =============================================================================
-- TRG_MECZ_DATA_W_EDYCJI
-- Walidacja: data meczu musi mieścić się w przedziale edycji turnieju.
-- =============================================================================
DROP TRIGGER IF EXISTS TRG_MECZ_INSERT_DATA $$
CREATE TRIGGER TRG_MECZ_INSERT_DATA
BEFORE INSERT ON MECZ
FOR EACH ROW
BEGIN
    DECLARE v_start DATE;
    DECLARE v_end   DATE;

    SELECT Data_rozpoczecia, Data_zakonczenia
      INTO v_start, v_end
      FROM EDYCJA_TURNIEJU
     WHERE ID_Edycji = NEW.ID_Edycji;

    IF NEW.Data < v_start OR NEW.Data > v_end THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Data meczu musi mieścić się w przedziale edycji turnieju.';
    END IF;
END$$

DROP TRIGGER IF EXISTS TRG_MECZ_UPDATE_DATA $$
CREATE TRIGGER TRG_MECZ_UPDATE_DATA
BEFORE UPDATE ON MECZ
FOR EACH ROW
BEGIN
    DECLARE v_start DATE;
    DECLARE v_end   DATE;

    SELECT Data_rozpoczecia, Data_zakonczenia
      INTO v_start, v_end
      FROM EDYCJA_TURNIEJU
     WHERE ID_Edycji = NEW.ID_Edycji;

    IF NEW.Data < v_start OR NEW.Data > v_end THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Data meczu musi mieścić się w przedziale edycji turnieju.';
    END IF;
END$$

-- =============================================================================
-- TRG_MECZ_UDZIAL_STRONA
-- Walidacja: do meczu można przypisać tylko 2 strony (A i B).
-- Każda strona może mieć 1 udział (singiel) lub 2 (debel) –
-- spójność ze zdefiniowanym RODZAJ_MECZU egzekwuje trigger poniżej.
-- nie może być więcej niż 2 różnych wartości Strona dla danego meczu.
-- =============================================================================
DROP TRIGGER IF EXISTS TRG_MECZ_UDZIAL_INSERT_STRONA $$
CREATE TRIGGER TRG_MECZ_UDZIAL_INSERT_STRONA
BEFORE INSERT ON MECZ_UDZIAL
FOR EACH ROW
BEGIN
    DECLARE v_ile_stron INT;

    SELECT COUNT(DISTINCT Strona)
      INTO v_ile_stron
      FROM MECZ_UDZIAL
     WHERE ID_Meczu = NEW.ID_Meczu;

    -- Jeśli już są 2 strony i nowa jest inną wartością – błąd
    IF v_ile_stron = 2 AND NEW.Strona NOT IN (
        SELECT DISTINCT Strona FROM MECZ_UDZIAL WHERE ID_Meczu = NEW.ID_Meczu
    ) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Mecz może mieć tylko dwie strony: A i B.';
    END IF;
END$$

-- =============================================================================
-- TRG_MECZ_UDZIAL_LICZBA_ZAWODNIKOW
-- Walidacja: liczba udziałów na stronę musi odpowiadać Zawodnikow_na_strone
--   z tabeli RODZAJ_MECZU powiązanego z meczem.
-- =============================================================================
DROP TRIGGER IF EXISTS TRG_MECZ_UDZIAL_INSERT_LICZBA $$
CREATE TRIGGER TRG_MECZ_UDZIAL_INSERT_LICZBA
AFTER INSERT ON MECZ_UDZIAL
FOR EACH ROW
BEGIN
    DECLARE v_wymagani  INT;
    DECLARE v_faktyczni INT;
    DECLARE v_msg       VARCHAR(200);

    -- Pobierz wymaganą liczbę zawodników na stronę
    SELECT rm.Zawodnikow_na_strone
      INTO v_wymagani
      FROM MECZ m
      JOIN RODZAJ_MECZU rm ON rm.ID_Rodzaju = m.ID_Rodzaju
     WHERE m.ID_Meczu = NEW.ID_Meczu;

    -- Policz faktycznych dla tej strony
    SELECT COUNT(*)
      INTO v_faktyczni
      FROM MECZ_UDZIAL
     WHERE ID_Meczu = NEW.ID_Meczu
       AND Strona   = NEW.Strona;

    IF v_faktyczni > v_wymagani THEN
        SET v_msg = CONCAT(
            'Przekroczono liczbę zawodników na stronę. Wymagana: ',
            v_wymagani, ', aktualna: ', v_faktyczni, '.'
        );
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_msg;
    END IF;
END$$

-- =============================================================================
-- TRG_UDZIAL_REJESTRACJA_PO_NARODZINACH
-- Walidacja: data rejestracji nie może być wcześniejsza niż urodziny zawodnika.
-- =============================================================================
DROP TRIGGER IF EXISTS TRG_UDZIAL_INSERT_WIEK $$
CREATE TRIGGER TRG_UDZIAL_INSERT_WIEK
BEFORE INSERT ON UDZIAL
FOR EACH ROW
BEGIN
    DECLARE v_urodziny DATE;

    SELECT Data_urodzenia INTO v_urodziny
      FROM ZAWODNIK
     WHERE Nr_Licencji_ITF = NEW.Nr_Licencji_ITF;

    IF NEW.Data_rejestracji < v_urodziny THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Data rejestracji nie może być wcześniejsza niż data urodzenia zawodnika.';
    END IF;
END$$

DROP TRIGGER IF EXISTS TRG_UDZIAL_UPDATE_WIEK $$
CREATE TRIGGER TRG_UDZIAL_UPDATE_WIEK
BEFORE UPDATE ON UDZIAL
FOR EACH ROW
BEGIN
    DECLARE v_urodziny DATE;

    SELECT Data_urodzenia INTO v_urodziny
      FROM ZAWODNIK
     WHERE Nr_Licencji_ITF = NEW.Nr_Licencji_ITF;

    IF NEW.Data_rejestracji < v_urodziny THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Data rejestracji nie może być wcześniejsza niż data urodzenia zawodnika.';
    END IF;
END$$

-- =============================================================================
-- TRG_UDZIAL_EDYCJA_ZGODNOSC
-- Walidacja: data rejestracji nie może być późniejsza niż zakończenie edycji,
-- do której się rejestrujemy.
-- =============================================================================
DROP TRIGGER IF EXISTS TRG_UDZIAL_INSERT_EDYCJA $$
CREATE TRIGGER TRG_UDZIAL_INSERT_EDYCJA
BEFORE INSERT ON UDZIAL
FOR EACH ROW
BEGIN
    DECLARE v_koniec DATE;

    SELECT Data_zakonczenia INTO v_koniec
      FROM EDYCJA_TURNIEJU
     WHERE ID_Edycji = NEW.ID_Edycji;

    IF NEW.Data_rejestracji > v_koniec THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Data rejestracji nie może być późniejsza niż zakończenie edycji turnieju.';
    END IF;
END$$

DROP TRIGGER IF EXISTS TRG_UDZIAL_UPDATE_EDYCJA $$
CREATE TRIGGER TRG_UDZIAL_UPDATE_EDYCJA
BEFORE UPDATE ON UDZIAL
FOR EACH ROW
BEGIN
    DECLARE v_koniec DATE;

    SELECT Data_zakonczenia INTO v_koniec
      FROM EDYCJA_TURNIEJU
     WHERE ID_Edycji = NEW.ID_Edycji;

    IF NEW.Data_rejestracji > v_koniec THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Data rejestracji nie może być późniejsza niż zakończenie edycji turnieju.';
    END IF;
END$$

-- =============================================================================
-- TRG_ZAWODNIK_URODZINY
-- Walidacja: data urodzenia nie może być w przyszłości.
-- (CHECK z CURDATE() nie jest obsługiwany w MariaDB 10.4)
-- =============================================================================
DROP TRIGGER IF EXISTS TRG_ZAWODNIK_INSERT_URODZINY $$
CREATE TRIGGER TRG_ZAWODNIK_INSERT_URODZINY
BEFORE INSERT ON ZAWODNIK
FOR EACH ROW
BEGIN
    IF NEW.Data_urodzenia > CURDATE() THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Data urodzenia nie może być w przyszłości.';
    END IF;
END$$

DROP TRIGGER IF EXISTS TRG_ZAWODNIK_UPDATE_URODZINY $$
CREATE TRIGGER TRG_ZAWODNIK_UPDATE_URODZINY
BEFORE UPDATE ON ZAWODNIK
FOR EACH ROW
BEGIN
    IF NEW.Data_urodzenia > CURDATE() THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Data urodzenia nie może być w przyszłości.';
    END IF;
END$$

DELIMITER ;

-- =============================================================================
-- EOF
-- =============================================================================
