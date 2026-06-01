# Specyfikacja projektu — archiwum turniejów tenisowych

Projekt składa się z aplikacji webowej w Laravelu 13 (PHP 8.3, Blade, Tailwind z CDN) oraz bazy MariaDB/MySQL zdefiniowanej wcześniej w pliku `turnieje_tenisa_ddl.sql`. Aplikacja nie zmienia schematu bazy przez migracje Laravel — modele łączą się z istniejącymi tabelami o nazwach z DDL (np. TURNIEJ, ZAWODNIK).

## Po co jest aplikacja

Użytkownik może przeglądać turnieje i ich edycje roczne, dodawać nowe turnieje i edycje, przypisywać zawodników (z bazy albo nowych przy zapisie edycji), wpisywać mecze z wynikami setów oraz otwierać profile zawodników z listą startów. Ograniczenia z bazy (CHECK, słowniki, klucze obce) pozostają po stronie SQL z etapu projektowania bazy.

## Co leży w folderze projektu

W katalogu `Tenis applikacja` są trzy rzeczy istotne poza samą aplikacją: `turnieje_tenisa_ddl.sql` (tworzenie bazy i tabel), `turnieje_tenisa_dane.sql` (przykładowe turnieje, zawodnicy, mecze i sety do testów) oraz ten plik specyfikacji. Aplikacja Laravel siedzi w podfolderze `turnieje-tenisa`. Tam są m.in. `app` (modele i kontrolery), `resources/views` (szablony HTML), `routes/web.php` (adresy URL), `database/seeders` (uzupełnienie krajów) i `.env` z połączeniem do bazy `turnieje_tenisa`.

Plik DDL zakłada słowniki PLEC, STATUS, RODZAJ_MECZU i puste KRAJ — kraje dokładamy seedem `KrajSeeder.php` poleceniem `php artisan db:seed` po imporcie DDL. Plik z danymi demo czyści i wypełnia tabele od TURNIEJ w dół, nie ruszając słowników. W `.env` ustawiamy mysql, sesję i cache na pliki, żeby Laravel nie wymagał swoich tabel sessions/cache.

## Jak działa request w Laravelu

Wejście z przeglądarki trafia do `routes/web.php`, stamtąd do odpowiedniego kontrolera, kontroler czyta lub zapisuje dane przez modele Eloquent i na końcu zwraca widok Blade. Wspólny szablon strony to `resources/views/layouts/app.blade.php`; konkretna podstrona to osobny plik w podfolderze turnieje, edycje, zawodnicy lub mecze. Trasy mają middleware web (sesja, CSRF). Nie ma logowania ani API JSON.

Strona główna `/` przekierowuje na listę turniejów. Turnieje: lista, formularz dodania, podgląd jednego turnieju z edycjami. Edycja: podgląd z meczami i zawodnikami, formularz nowej edycji pod adresem z ID turnieju, zapis POST na `/edycje`. Zawodnicy: lista z wyszukiwaniem, profil, dodanie. Mecze: formularz pod edycją, zapis POST na `/mecze`, podgląd meczu. Parametry w URL jak `{turniej}` Laravel podstawia sam z modelu (route model binding).

## Modele

W `app/Models/` jest jedenaście klas. Każda wskazuje właściwą tabelę, wyłącza timestamps i opisuje relacje zgodnie z DDL.

Kraj, Plec, Status i RodzajMeczu to słowniki. Turniej należy do Kraju i ma wiele EdycjaTurnieju; w Turnieju są też metody pomocnicze do kolorów nawierzchni w widokach. EdycjaTurnieju należy do Turnieju i ma mecze oraz udziały. Zawodnik ma kraj i płeć, wiele udziałów; w modelu jest sklejone imię i nazwisko oraz wiek z daty urodzenia. Udział to start zawodnika w danej edycji (status, rozstawienie, ranking); łączy się z meczami przez tabelę MECZ_UDZIAL ze stroną A lub B.

Mecz należy do edycji i rodzaju meczu, ma sety i udziały po obu stronach (metody stronaA i stronaB filtrują pivot). Kolumna Zwycięska_strona ma polski znak — w kodzie ustawiamy ją przez setAttribute przy zapisie, a do odczytu jest accessor zwycieska_strona. SetMeczu należy do meczu; Gem należy do setu — model Gem jest, ale w formularzach wpisujemy tylko wyniki setów, nie gemów.

## Kontrolery

W `app/Http/Controllers/` są cztery kontrolery domenowe plus pusty Controller bazowy. Nad metodami są krótkie komentarze po polsku. Po zapisie zwykle jest redirect z komunikatem success; błędy walidacji wracają do formularza.

TurniejController pokazuje listę turniejów z krajem i liczbą edycji, szczegóły turnieju z edycjami, oraz obsługuje dodanie turnieju (nazwa, miasto, kraj, nawierzchnia z ENUM w bazie).

EdycjaController na podglądzie edycji ładuje turniej, udziały z zawodnikami, mecze z setami i stronami; mecze grupuje po rundzie w ustalonej kolejności. Formularz nowej edycji dostaje listy statusów, zawodników, krajów i płci. Przy zapisie w transakcji tworzy wiersz edycji, potem dla każdego uczestnika z formularza: jeśli typ „nowy”, najpierw tworzy zawodnika, potem udział; jeśli „istniejący”, tylko udział.

ZawodnikController listuje zawodników z paginacją po 24, opcjonalnie filtruje po fragmencie imienia lub nazwiska i po płci. Profil pokazuje dane i historię udziałów z turniejami. Osobny formularz dodaje zawodnika z unikalnym numerem licencji ITF.

MeczController pokazuje mecz ze wszystkim potrzebnym do widoku wyniku. Formularz nowego meczu bierze udziały z danej edycji, rodzaje meczu i listę rund. Zapis w transakcji: rekord meczu, powiązania w MECZ_UDZIAL dla stron A i B, opcjonalnie wiersze w SET_MECZU z tablicy setów z POST.

## Widoki

Layout ma ciemne tło, boczny panel z linkami do turniejów i zawodników, górny pasek z tytułem strony, miejsce na treść i na komunikaty po zapisie lub błędy walidacji. Tailwind jest podłączony z CDN.

W turnieje/index jest siatka kart turniejów. turnieje/show to jeden turniej i jego edycje jako karty. turnieje/create to formularz nowego turnieju.

W edycje/show po lewej mecze wg rund z wynikami setów, po prawej lista zawodników tej edycji. edycje/create to daty i rok edycji oraz panel uczestników: przyciski dodają wiersze w JavaScript — albo wybór z listy zawodników, albo pola nowego gracza; dane list zawodników i krajów idą z serwera jako JSON przygotowany w bloku @php, żeby uniknąć problemów Blade z @json i strzałkami.

W zawodnicy/index jest wyszukiwarka, filtr płci i paginacja. zawodnicy/show to profil i lista turniejów, w których grał. zawodnicy/create to rejestracja.

W mecze/create wybiera się rundę, rodzaj, kort, zawodników na stronę A i B (multi-select) i dodaje sety przyciskiem w JS. mecze/show to podsumowanie meczu z wynikami setów i oznaczeniem zwycięzcy.

Paginacja listy zawodników korzysta z opublikowanego widoku w resources/views/vendor/pagination. Kontrolery ładują relacje przez with/load tam, gdzie widok ich potrzebuje.

## Relacja z bazą w skrócie

Turniej ma wiele edycji. W edycji zawodnik występuje przez Udzial. Mecz w edycji łączy udziały stron A i B; do meczu należą sety, a w DDL są też gemy — w aplikacji ich nie edytujemy. Słowniki KRAJ, PLEC, STATUS, RODZAJ_MECZU aplikacja głównie tylko czyta.

## Uruchomienie

Importuj DDL do MySQL/MariaDB. Opcjonalnie importuj turnieje_tenisa_dane.sql. W folderze turnieje-tenisa ustaw .env, odpal seed krajów, potem php artisan serve. Terminal musi być w katalogu turnieje-tenisa, bo tam jest artisan. Adres domyślny to http://127.0.0.1:8000.

## Czego aplikacja jeszcze nie robi

Nie ma edycji ani usuwania istniejących rekordów — tylko dodawanie i podgląd. Nie ma formularza na gemy. Nie ma kont użytkowników.

## Podział prac na cztery osoby

Założenie: każda osoba trzyma jedną warstwę lub jeden moduł widoków; dogadujecie nazwy tras, zmiennych w widokach i relacji w modelach.

Osoba 1 — modele i seedery. Wszystkie pliki w app/Models: słowniki Kraj, Plec, Status, RodzajMeczu; Turniej i EdycjaTurnieju; Zawodnik i Udzial; Mecz, SetMeczu, Gem. Dodatkowo KrajSeeder i wpis w DatabaseSeeder. Od tej osoby zależą nazwy relacji typu stronaA, pelne_nazwisko, których używają kontrolery i widoki.

Osoba 2 — kontrolery i routing. TurniejController, EdycjaController, ZawodnikController, MeczController oraz cały routes/web.php. Logika walidacji, transakcji przy zapisie edycji i meczu, przekierowania z komunikatami. Może też ogarnąć .env pod MySQL i krótką instrukcję uruchomienia.

Osoba 3 — widoki turniejów i edycji oraz layout. layouts/app.blade.php i pliki turnieje/*.blade.php oraz edycje/*.blade.php, w tym JavaScript w edycje/create do dodawania uczestników. Korzysta z tras i zmiennych z kontrolerów oraz z metod kolorów nawierzchni z modelu Turniej.

Osoba 4 — widoki zawodników i meczów oraz dane SQL. zawodnicy/*.blade.php, mecze/*.blade.php, ewentualnie styl paginacji po vendor:publish. Plik turnieje_tenisa_dane.sql w katalogu nadrzędnym — dane do pokazania projektu na zajęciach. Formularz meczu musi wysyłać pola zgodne z MeczController (strona_a, strona_b, sety).

Schemat DDL z wcześniejszego etapu zwykle robiła osoba lub osoby od bazy wspólnie, poza tym podziałem na cztery role w Laravelu. Katalogi vendor, bootstrap i public to standard frameworka, nie przypisujemy ich jednej osobie z zespołu aplikacji.

Podsumowując: osoba 1 — warstwa danych w PHP; osoba 2 — logika HTTP i zapisu; osoba 3 — szkielet strony i moduł turniej → edycja; osoba 4 — zawodnicy, mecze i plik z przykładowymi danymi SQL.
