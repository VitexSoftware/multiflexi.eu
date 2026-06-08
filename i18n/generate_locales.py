#!/usr/bin/env python3
"""Generate per-language .po/.mo catalogs for the MultiFlexi.eu website.

Reads the canonical template ``i18n/multiflexieu.pot`` and, for every locale
listed in ``LANGS``, writes ``i18n/<locale>/LC_MESSAGES/multiflexieu.po`` with
the translations from ``TR`` filled in, then compiles it to ``.mo`` via msgfmt.

Strings absent from ``TR`` are left untranslated and fall back to the English
source at runtime (standard gettext behaviour). Re-run after editing ``TR`` or
after refreshing the .pot with xgettext.
"""
from __future__ import annotations

import os
import re
import subprocess
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
POT = os.path.join(HERE, "multiflexieu.pot")
DOMAIN = "multiflexieu"

# locale code -> Plural-Forms header value
LANGS = {
    "de_DE": "nplurals=2; plural=(n != 1);",
    "fr_FR": "nplurals=2; plural=(n > 1);",
    "uk_UA": "nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<12 || n%100>14) ? 1 : 2);",
    "es_ES": "nplurals=2; plural=(n != 1);",
    "it_IT": "nplurals=2; plural=(n != 1);",
    "pl_PL": "nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<12 || n%100>14) ? 1 : 2);",
    "nl_NL": "nplurals=2; plural=(n != 1);",
    "pt_PT": "nplurals=2; plural=(n != 1);",
    "sk_SK": "nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2;",
    "hu_HU": "nplurals=2; plural=(n != 1);",
    "ro_RO": "nplurals=3; plural=(n==1 ? 0 : (n==0 || (n%100>0 && n%100<20)) ? 1 : 2);",
    "hr_HR": "nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<12 || n%100>14) ? 1 : 2);",
    "sl_SI": "nplurals=4; plural=(n%100==1 ? 0 : n%100==2 ? 1 : n%100==3 || n%100==4 ? 2 : 3);",
    "sv_SE": "nplurals=2; plural=(n != 1);",
    "fi_FI": "nplurals=2; plural=(n != 1);",
}

# Fixed order matching the 15-element translation tuples below.
ORDER = ["de_DE", "fr_FR", "uk_UA", "es_ES", "it_IT", "pl_PL", "nl_NL",
         "pt_PT", "sk_SK", "hu_HU", "ro_RO", "hr_HR", "sl_SI", "sv_SE", "fi_FI"]

# msgid -> (de, fr, uk, es, it, pl, nl, pt, sk, hu, ro, hr, sl, sv, fi)
TR = {
    # --- Navigation / global ---
    "What is MultiFlexi?": ("Was ist MultiFlexi?", "Qu'est-ce que MultiFlexi ?", "Що таке MultiFlexi?", "¿Qué es MultiFlexi?", "Cos'è MultiFlexi?", "Czym jest MultiFlexi?", "Wat is MultiFlexi?", "O que é o MultiFlexi?", "Čo je MultiFlexi?", "Mi az a MultiFlexi?", "Ce este MultiFlexi?", "Što je MultiFlexi?", "Kaj je MultiFlexi?", "Vad är MultiFlexi?", "Mikä on MultiFlexi?"),
    "Demo": ("Demo", "Démo", "Демо", "Demo", "Demo", "Demo", "Demo", "Demo", "Demo", "Demó", "Demo", "Demo", "Predstavitev", "Demo", "Demo"),
    "Apps": ("Apps", "Applis", "Застосунки", "Aplicaciones", "App", "Aplikacje", "Apps", "Apps", "Aplikácie", "Alkalmazások", "Aplicații", "Aplikacije", "Aplikacije", "Appar", "Sovellukset"),
    "My Apps": ("Meine Apps", "Mes applis", "Мої застосунки", "Mis aplicaciones", "Le mie app", "Moje aplikacje", "Mijn apps", "As minhas apps", "Moje aplikácie", "Alkalmazásaim", "Aplicațiile mele", "Moje aplikacije", "Moje aplikacije", "Mina appar", "Sovellukseni"),
    "Apps ": ("Apps", "Applis", "Застосунки", "Aplicaciones", "App", "Aplikacje", "Apps", "Apps", "Aplikácie", "Alkalmazások", "Aplicații", "Aplikacije", "Aplikacije", "Appar", "Sovellukset"),
    "Browse Apps": ("Apps durchsuchen", "Parcourir les applis", "Переглянути застосунки", "Explorar aplicaciones", "Sfoglia le app", "Przeglądaj aplikacje", "Apps bekijken", "Explorar apps", "Prehľadávať aplikácie", "Alkalmazások böngészése", "Răsfoiește aplicațiile", "Pregledaj aplikacije", "Prebrskaj aplikacije", "Bläddra bland appar", "Selaa sovelluksia"),
    "Submit App": ("App einreichen", "Soumettre une appli", "Надіслати застосунок", "Enviar aplicación", "Invia app", "Zgłoś aplikację", "App indienen", "Submeter app", "Odoslať aplikáciu", "Alkalmazás beküldése", "Trimite o aplicație", "Pošalji aplikaciju", "Pošlji aplikacijo", "Skicka in app", "Lähetä sovellus"),
    "Credential Type": ("Anmeldedatentyp", "Type d'identifiant", "Тип облікових даних", "Tipo de credencial", "Tipo di credenziale", "Typ poświadczeń", "Type inloggegevens", "Tipo de credencial", "Typ poverenia", "Hitelesítő típus", "Tip de credențial", "Vrsta vjerodajnice", "Vrsta poverilnice", "Typ av autentiseringsuppgift", "Tunnistetyyppi"),
    "Credential Types": ("Anmeldedatentypen", "Types d'identifiants", "Типи облікових даних", "Tipos de credencial", "Tipi di credenziale", "Typy poświadczeń", "Types inloggegevens", "Tipos de credencial", "Typy poverení", "Hitelesítő típusok", "Tipuri de credențiale", "Vrste vjerodajnica", "Vrste poverilnic", "Typer av autentiseringsuppgifter", "Tunnistetyypit"),
    "My Credential Types": ("Meine Anmeldedatentypen", "Mes types d'identifiants", "Мої типи облікових даних", "Mis tipos de credencial", "I miei tipi di credenziale", "Moje typy poświadczeń", "Mijn types inloggegevens", "Os meus tipos de credencial", "Moje typy poverení", "Hitelesítő típusaim", "Tipurile mele de credențiale", "Moje vrste vjerodajnica", "Moje vrste poverilnic", "Mina typer av autentiseringsuppgifter", "Tunnistetyyppini"),
    "Submit Credential Type": ("Anmeldedatentyp einreichen", "Soumettre un type d'identifiant", "Надіслати тип облікових даних", "Enviar tipo de credencial", "Invia tipo di credenziale", "Zgłoś typ poświadczeń", "Type inloggegevens indienen", "Submeter tipo de credencial", "Odoslať typ poverenia", "Hitelesítő típus beküldése", "Trimite un tip de credențial", "Pošalji vrstu vjerodajnice", "Pošlji vrsto poverilnice", "Skicka in autentiseringstyp", "Lähetä tunnistetyyppi"),
    "Install": ("Installieren", "Installer", "Встановлення", "Instalar", "Installa", "Instalacja", "Installeren", "Instalar", "Inštalovať", "Telepítés", "Instalare", "Instaliraj", "Namestitev", "Installera", "Asennus"),
    "Sign In": ("Anmelden", "Se connecter", "Увійти", "Iniciar sesión", "Accedi", "Zaloguj się", "Inloggen", "Entrar", "Prihlásiť sa", "Bejelentkezés", "Autentificare", "Prijava", "Prijava", "Logga in", "Kirjaudu sisään"),
    "Sign in": ("Anmelden", "Se connecter", "Увійти", "Iniciar sesión", "Accedi", "Zaloguj się", "Inloggen", "Entrar", "Prihlásiť sa", "Bejelentkezés", "Autentificare", "Prijava", "Prijava", "Logga in", "Kirjaudu sisään"),
    "Sign On": ("Registrieren", "S'inscrire", "Зареєструватися", "Registrarse", "Registrati", "Zarejestruj się", "Registreren", "Registar", "Zaregistrovať sa", "Regisztráció", "Înregistrare", "Registracija", "Registracija", "Registrera dig", "Rekisteröidy"),
    "Sign Off": ("Abmelden", "Se déconnecter", "Вийти", "Cerrar sesión", "Esci", "Wyloguj się", "Uitloggen", "Sair", "Odhlásiť sa", "Kijelentkezés", "Deconectare", "Odjava", "Odjava", "Logga ut", "Kirjaudu ulos"),
    "Logout": ("Abmelden", "Déconnexion", "Вихід", "Cerrar sesión", "Esci", "Wyloguj", "Uitloggen", "Sair", "Odhlásiť sa", "Kijelentkezés", "Deconectare", "Odjava", "Odjava", "Logga ut", "Kirjaudu ulos"),
    "Toggle navigation": ("Navigation umschalten", "Basculer la navigation", "Перемкнути навігацію", "Alternar navegación", "Attiva/disattiva navigazione", "Przełącz nawigację", "Navigatie wisselen", "Alternar navegação", "Prepnúť navigáciu", "Navigáció be/ki", "Comută navigarea", "Uključi/isključi navigaciju", "Preklopi navigacijo", "Växla navigering", "Vaihda navigointi"),
    "Documentation": ("Dokumentation", "Documentation", "Документація", "Documentación", "Documentazione", "Dokumentacja", "Documentatie", "Documentação", "Dokumentácia", "Dokumentáció", "Documentație", "Dokumentacija", "Dokumentacija", "Dokumentation", "Dokumentaatio"),
    "Github Project": ("GitHub-Projekt", "Projet GitHub", "Проєкт GitHub", "Proyecto en GitHub", "Progetto GitHub", "Projekt GitHub", "GitHub-project", "Projeto GitHub", "Projekt GitHub", "GitHub projekt", "Proiect GitHub", "GitHub projekt", "Projekt GitHub", "GitHub-projekt", "GitHub-projekti"),
    "MultiFlexi on GitHub": ("MultiFlexi auf GitHub", "MultiFlexi sur GitHub", "MultiFlexi на GitHub", "MultiFlexi en GitHub", "MultiFlexi su GitHub", "MultiFlexi na GitHub", "MultiFlexi op GitHub", "MultiFlexi no GitHub", "MultiFlexi na GitHube", "MultiFlexi a GitHubon", "MultiFlexi pe GitHub", "MultiFlexi na GitHubu", "MultiFlexi na GitHubu", "MultiFlexi på GitHub", "MultiFlexi GitHubissa"),

    # --- Homepage ---
    "MultiFlexi Hub": ("MultiFlexi-Hub", "Hub MultiFlexi", "MultiFlexi Hub", "Hub de MultiFlexi", "Hub MultiFlexi", "Centrum MultiFlexi", "MultiFlexi-hub", "Hub MultiFlexi", "MultiFlexi Hub", "MultiFlexi Hub", "Hub MultiFlexi", "MultiFlexi čvorište", "MultiFlexi vozlišče", "MultiFlexi-hubb", "MultiFlexi-keskus"),
    "MultiFlexi Application & Credential Hub": ("MultiFlexi – Anwendungs- und Anmeldedaten-Hub", "Hub d'applications et d'identifiants MultiFlexi", "MultiFlexi — центр застосунків та облікових даних", "Hub de aplicaciones y credenciales de MultiFlexi", "Hub di applicazioni e credenziali MultiFlexi", "Centrum aplikacji i poświadczeń MultiFlexi", "MultiFlexi-hub voor applicaties en inloggegevens", "Hub de aplicações e credenciais MultiFlexi", "MultiFlexi – centrum aplikácií a poverení", "MultiFlexi alkalmazás- és hitelesítőközpont", "Hub de aplicații și credențiale MultiFlexi", "MultiFlexi čvorište aplikacija i vjerodajnica", "MultiFlexi vozlišče aplikacij in poverilnic", "MultiFlexi – nav för applikationer och autentiseringsuppgifter", "MultiFlexi-sovellus- ja tunnistekeskus"),
    "Browse, share and discover applications and credential type definitions for MultiFlexi. Sign in to submit your own.": (
        "Durchsuchen, teilen und entdecken Sie Anwendungs- und Anmeldedatentyp-Definitionen für MultiFlexi. Melden Sie sich an, um eigene einzureichen.",
        "Parcourez, partagez et découvrez des définitions d'applications et de types d'identifiants pour MultiFlexi. Connectez-vous pour soumettre les vôtres.",
        "Переглядайте, діліться та відкривайте визначення застосунків і типів облікових даних для MultiFlexi. Увійдіть, щоб надіслати власні.",
        "Explore, comparta y descubra definiciones de aplicaciones y tipos de credenciales para MultiFlexi. Inicie sesión para enviar las suyas.",
        "Sfoglia, condividi e scopri definizioni di applicazioni e tipi di credenziali per MultiFlexi. Accedi per inviare le tue.",
        "Przeglądaj, udostępniaj i odkrywaj definicje aplikacji oraz typów poświadczeń dla MultiFlexi. Zaloguj się, aby zgłosić własne.",
        "Blader door, deel en ontdek definities van applicaties en typen inloggegevens voor MultiFlexi. Log in om uw eigen in te dienen.",
        "Explore, partilhe e descubra definições de aplicações e tipos de credenciais para o MultiFlexi. Inicie sessão para submeter as suas.",
        "Prehľadávajte, zdieľajte a objavujte definície aplikácií a typov poverení pre MultiFlexi. Prihláste sa a odošlite vlastné.",
        "Böngésszen, osszon meg és fedezzen fel alkalmazás- és hitelesítőtípus-definíciókat a MultiFlexihez. Jelentkezzen be a sajátjai beküldéséhez.",
        "Răsfoiește, distribuie și descoperă definiții de aplicații și tipuri de credențiale pentru MultiFlexi. Autentifică-te pentru a le trimite pe ale tale.",
        "Pregledavajte, dijelite i otkrivajte definicije aplikacija i vrsta vjerodajnica za MultiFlexi. Prijavite se da pošaljete svoje.",
        "Prebrskajte, delite in odkrivajte definicije aplikacij ter vrst poverilnic za MultiFlexi. Prijavite se za oddajo svojih.",
        "Bläddra bland, dela och upptäck definitioner av applikationer och autentiseringstyper för MultiFlexi. Logga in för att skicka in dina egna.",
        "Selaa, jaa ja löydä MultiFlexin sovellus- ja tunnistetyyppimäärityksiä. Kirjaudu sisään lähettääksesi omasi."),
    "Browse Applications": ("Anwendungen durchsuchen", "Parcourir les applications", "Переглянути застосунки", "Explorar aplicaciones", "Sfoglia le applicazioni", "Przeglądaj aplikacje", "Applicaties bekijken", "Explorar aplicações", "Prehľadávať aplikácie", "Alkalmazások böngészése", "Răsfoiește aplicațiile", "Pregledaj aplikacije", "Prebrskaj aplikacije", "Bläddra bland applikationer", "Selaa sovelluksia"),
    "Browse Credential Types": ("Anmeldedatentypen durchsuchen", "Parcourir les types d'identifiants", "Переглянути типи облікових даних", "Explorar tipos de credencial", "Sfoglia i tipi di credenziale", "Przeglądaj typy poświadczeń", "Types inloggegevens bekijken", "Explorar tipos de credencial", "Prehľadávať typy poverení", "Hitelesítő típusok böngészése", "Răsfoiește tipurile de credențiale", "Pregledaj vrste vjerodajnica", "Prebrskaj vrste poverilnic", "Bläddra bland autentiseringstyper", "Selaa tunnistetyyppejä"),
    "Install MultiFlexi": ("MultiFlexi installieren", "Installer MultiFlexi", "Встановити MultiFlexi", "Instalar MultiFlexi", "Installa MultiFlexi", "Zainstaluj MultiFlexi", "MultiFlexi installeren", "Instalar o MultiFlexi", "Inštalovať MultiFlexi", "MultiFlexi telepítése", "Instalează MultiFlexi", "Instaliraj MultiFlexi", "Namesti MultiFlexi", "Installera MultiFlexi", "Asenna MultiFlexi"),
    "Recent Applications": ("Neueste Anwendungen", "Applications récentes", "Останні застосунки", "Aplicaciones recientes", "Applicazioni recenti", "Ostatnie aplikacje", "Recente applicaties", "Aplicações recentes", "Najnovšie aplikácie", "Legutóbbi alkalmazások", "Aplicații recente", "Nedavne aplikacije", "Nedavne aplikacije", "Senaste applikationer", "Viimeisimmät sovellukset"),
    "Recent Credential Types": ("Neueste Anmeldedatentypen", "Types d'identifiants récents", "Останні типи облікових даних", "Tipos de credencial recientes", "Tipi di credenziale recenti", "Ostatnie typy poświadczeń", "Recente types inloggegevens", "Tipos de credencial recentes", "Najnovšie typy poverení", "Legutóbbi hitelesítő típusok", "Tipuri de credențiale recente", "Nedavne vrste vjerodajnica", "Nedavne vrste poverilnic", "Senaste autentiseringstyper", "Viimeisimmät tunnistetyypit"),
    "View all applications": ("Alle Anwendungen anzeigen", "Voir toutes les applications", "Переглянути всі застосунки", "Ver todas las aplicaciones", "Mostra tutte le applicazioni", "Zobacz wszystkie aplikacje", "Alle applicaties bekijken", "Ver todas as aplicações", "Zobraziť všetky aplikácie", "Összes alkalmazás megtekintése", "Vezi toate aplicațiile", "Prikaži sve aplikacije", "Prikaži vse aplikacije", "Visa alla applikationer", "Näytä kaikki sovellukset"),
    "View all credential types": ("Alle Anmeldedatentypen anzeigen", "Voir tous les types d'identifiants", "Переглянути всі типи облікових даних", "Ver todos los tipos de credencial", "Mostra tutti i tipi di credenziale", "Zobacz wszystkie typy poświadczeń", "Alle types inloggegevens bekijken", "Ver todos os tipos de credencial", "Zobraziť všetky typy poverení", "Összes hitelesítő típus megtekintése", "Vezi toate tipurile de credențiale", "Prikaži sve vrste vjerodajnica", "Prikaži vse vrste poverilnic", "Visa alla autentiseringstyper", "Näytä kaikki tunnistetyypit"),
    "No applications submitted yet. Be the first!": ("Noch keine Anwendungen eingereicht. Seien Sie der Erste!", "Aucune application soumise pour l'instant. Soyez le premier !", "Ще немає надісланих застосунків. Будьте першим!", "Aún no hay aplicaciones enviadas. ¡Sé el primero!", "Nessuna applicazione ancora inviata. Sii il primo!", "Nie zgłoszono jeszcze żadnych aplikacji. Bądź pierwszy!", "Nog geen applicaties ingediend. Wees de eerste!", "Ainda não foram submetidas aplicações. Seja o primeiro!", "Zatiaľ neboli odoslané žiadne aplikácie. Buďte prvý!", "Még nincs beküldött alkalmazás. Legyen Ön az első!", "Încă nu a fost trimisă nicio aplicație. Fii primul!", "Još nema poslanih aplikacija. Budite prvi!", "Še ni oddanih aplikacij. Bodite prvi!", "Inga applikationer har skickats in ännu. Var först!", "Sovelluksia ei ole vielä lähetetty. Ole ensimmäinen!"),
    "No credential types submitted yet. Be the first!": ("Noch keine Anmeldedatentypen eingereicht. Seien Sie der Erste!", "Aucun type d'identifiant soumis pour l'instant. Soyez le premier !", "Ще немає надісланих типів облікових даних. Будьте першим!", "Aún no hay tipos de credencial enviados. ¡Sé el primero!", "Nessun tipo di credenziale ancora inviato. Sii il primo!", "Nie zgłoszono jeszcze żadnych typów poświadczeń. Bądź pierwszy!", "Nog geen types inloggegevens ingediend. Wees de eerste!", "Ainda não foram submetidos tipos de credencial. Seja o primeiro!", "Zatiaľ neboli odoslané žiadne typy poverení. Buďte prvý!", "Még nincs beküldött hitelesítő típus. Legyen Ön az első!", "Încă nu a fost trimis niciun tip de credențial. Fii primul!", "Još nema poslanih vrsta vjerodajnica. Budite prvi!", "Še ni oddanih vrst poverilnic. Bodite prvi!", "Inga autentiseringstyper har skickats in ännu. Var först!", "Tunnistetyyppejä ei ole vielä lähetetty. Ole ensimmäinen!"),
    "applications": ("Anwendungen", "applications", "застосунки", "aplicaciones", "applicazioni", "aplikacje", "applicaties", "aplicações", "aplikácie", "alkalmazások", "aplicații", "aplikacije", "aplikacije", "applikationer", "sovellukset"),
    "credential types": ("Anmeldedatentypen", "types d'identifiants", "типи облікових даних", "tipos de credencial", "tipi di credenziale", "typy poświadczeń", "types inloggegevens", "tipos de credencial", "typy poverení", "hitelesítő típusok", "tipuri de credențiale", "vrste vjerodajnica", "vrste poverilnic", "autentiseringstyper", "tunnistetyypit"),
    "Try Demo": ("Demo ausprobieren", "Essayer la démo", "Спробувати демо", "Probar la demo", "Prova la demo", "Wypróbuj demo", "Demo proberen", "Experimentar a demo", "Vyskúšať demo", "Demó kipróbálása", "Încearcă demo", "Isprobaj demo", "Preizkusi predstavitev", "Prova demo", "Kokeile demoa"),

    # --- Browse / table / filters ---
    "Applications": ("Anwendungen", "Applications", "Застосунки", "Aplicaciones", "Applicazioni", "Aplikacje", "Applicaties", "Aplicações", "Aplikácie", "Alkalmazások", "Aplicații", "Aplikacije", "Aplikacije", "Applikationer", "Sovellukset"),
    "Application": ("Anwendung", "Application", "Застосунок", "Aplicación", "Applicazione", "Aplikacja", "Applicatie", "Aplicação", "Aplikácia", "Alkalmazás", "Aplicație", "Aplikacija", "Aplikacija", "Applikation", "Sovellus"),
    "Cards": ("Karten", "Cartes", "Картки", "Tarjetas", "Schede", "Karty", "Kaarten", "Cartões", "Karty", "Kártyák", "Carduri", "Kartice", "Kartice", "Kort", "Kortit"),
    "Table": ("Tabelle", "Tableau", "Таблиця", "Tabla", "Tabella", "Tabela", "Tabel", "Tabela", "Tabuľka", "Táblázat", "Tabel", "Tablica", "Tabela", "Tabell", "Taulukko"),
    "Search applications...": ("Anwendungen suchen …", "Rechercher des applications…", "Пошук застосунків…", "Buscar aplicaciones…", "Cerca applicazioni…", "Szukaj aplikacji…", "Applicaties zoeken…", "Pesquisar aplicações…", "Hľadať aplikácie…", "Alkalmazások keresése…", "Caută aplicații…", "Pretraži aplikacije…", "Iskanje aplikacij…", "Sök applikationer…", "Hae sovelluksia…"),
    "Search credential types...": ("Anmeldedatentypen suchen …", "Rechercher des types d'identifiants…", "Пошук типів облікових даних…", "Buscar tipos de credencial…", "Cerca tipi di credenziale…", "Szukaj typów poświadczeń…", "Types inloggegevens zoeken…", "Pesquisar tipos de credencial…", "Hľadať typy poverení…", "Hitelesítő típusok keresése…", "Caută tipuri de credențiale…", "Pretraži vrste vjerodajnica…", "Iskanje vrst poverilnic…", "Sök autentiseringstyper…", "Hae tunnistetyyppejä…"),
    "Filter": ("Filter", "Filtrer", "Фільтр", "Filtrar", "Filtra", "Filtruj", "Filteren", "Filtrar", "Filtrovať", "Szűrő", "Filtrează", "Filtriraj", "Filter", "Filtrera", "Suodata"),
    "Filter by Tags": ("Nach Tags filtern", "Filtrer par étiquettes", "Фільтрувати за тегами", "Filtrar por etiquetas", "Filtra per tag", "Filtruj według tagów", "Filteren op tags", "Filtrar por etiquetas", "Filtrovať podľa značiek", "Szűrés címkék szerint", "Filtrează după etichete", "Filtriraj po oznakama", "Filtriraj po oznakah", "Filtrera efter taggar", "Suodata tunnisteilla"),
    "Filter by Topics": ("Nach Themen filtern", "Filtrer par sujets", "Фільтрувати за темами", "Filtrar por temas", "Filtra per argomenti", "Filtruj według tematów", "Filteren op onderwerpen", "Filtrar por tópicos", "Filtrovať podľa tém", "Szűrés témák szerint", "Filtrează după subiecte", "Filtriraj po temama", "Filtriraj po temah", "Filtrera efter ämnen", "Suodata aiheilla"),
    "Reset Filter": ("Filter zurücksetzen", "Réinitialiser le filtre", "Скинути фільтр", "Restablecer filtro", "Reimposta filtro", "Resetuj filtr", "Filter resetten", "Repor filtro", "Obnoviť filter", "Szűrő visszaállítása", "Resetează filtrul", "Poništi filtar", "Ponastavi filter", "Återställ filter", "Nollaa suodatin"),
    "Tags": ("Tags", "Étiquettes", "Теги", "Etiquetas", "Tag", "Tagi", "Tags", "Etiquetas", "Značky", "Címkék", "Etichete", "Oznake", "Oznake", "Taggar", "Tunnisteet"),
    "Tags separated by comma": ("Tags durch Komma getrennt", "Étiquettes séparées par des virgules", "Теги через кому", "Etiquetas separadas por comas", "Tag separati da virgola", "Tagi oddzielone przecinkami", "Tags gescheiden door komma's", "Etiquetas separadas por vírgulas", "Značky oddelené čiarkou", "Vesszővel elválasztott címkék", "Etichete separate prin virgulă", "Oznake odvojene zarezom", "Oznake, ločene z vejico", "Taggar separerade med komma", "Pilkulla erotetut tunnisteet"),
    "All pages": ("Alle Seiten", "Toutes les pages", "Усі сторінки", "Todas las páginas", "Tutte le pagine", "Wszystkie strony", "Alle pagina's", "Todas as páginas", "Všetky stránky", "Összes oldal", "Toate paginile", "Sve stranice", "Vse strani", "Alla sidor", "Kaikki sivut"),
    "Reload": ("Neu laden", "Recharger", "Перезавантажити", "Recargar", "Ricarica", "Odśwież", "Opnieuw laden", "Recarregar", "Znovu načítať", "Újratöltés", "Reîncarcă", "Ponovno učitaj", "Ponovno naloži", "Ladda om", "Lataa uudelleen"),
    "Import": ("Importieren", "Importer", "Імпорт", "Importar", "Importa", "Importuj", "Importeren", "Importar", "Importovať", "Importálás", "Importă", "Uvezi", "Uvozi", "Importera", "Tuo"),
    "Export": ("Exportieren", "Exporter", "Експорт", "Exportar", "Esporta", "Eksportuj", "Exporteren", "Exportar", "Exportovať", "Exportálás", "Exportă", "Izvezi", "Izvozi", "Exportera", "Vie"),
    "Download": ("Herunterladen", "Télécharger", "Завантажити", "Descargar", "Scarica", "Pobierz", "Downloaden", "Transferir", "Stiahnuť", "Letöltés", "Descarcă", "Preuzmi", "Prenesi", "Ladda ner", "Lataa"),

    # --- Common labels ---
    "Name": ("Name", "Nom", "Назва", "Nombre", "Nome", "Nazwa", "Naam", "Nome", "Názov", "Név", "Nume", "Naziv", "Ime", "Namn", "Nimi"),
    "Description": ("Beschreibung", "Description", "Опис", "Descripción", "Descrizione", "Opis", "Beschrijving", "Descrição", "Popis", "Leírás", "Descriere", "Opis", "Opis", "Beskrivning", "Kuvaus"),
    "Version": ("Version", "Version", "Версія", "Versión", "Versione", "Wersja", "Versie", "Versão", "Verzia", "Verzió", "Versiune", "Verzija", "Različica", "Version", "Versio"),
    "Owner": ("Eigentümer", "Propriétaire", "Власник", "Propietario", "Proprietario", "Właściciel", "Eigenaar", "Proprietário", "Vlastník", "Tulajdonos", "Proprietar", "Vlasnik", "Lastnik", "Ägare", "Omistaja"),
    "Code": ("Code", "Code", "Код", "Código", "Codice", "Kod", "Code", "Código", "Kód", "Kód", "Cod", "Kôd", "Koda", "Kod", "Koodi"),
    "Type": ("Typ", "Type", "Тип", "Tipo", "Tipo", "Typ", "Type", "Tipo", "Typ", "Típus", "Tip", "Vrsta", "Vrsta", "Typ", "Tyyppi"),
    "Key": ("Schlüssel", "Clé", "Ключ", "Clave", "Chiave", "Klucz", "Sleutel", "Chave", "Kľúč", "Kulcs", "Cheie", "Ključ", "Ključ", "Nyckel", "Avain"),
    "Keyword": ("Schlüsselwort", "Mot-clé", "Ключове слово", "Palabra clave", "Parola chiave", "Słowo kluczowe", "Trefwoord", "Palavra-chave", "Kľúčové slovo", "Kulcsszó", "Cuvânt-cheie", "Ključna riječ", "Ključna beseda", "Nyckelord", "Avainsana"),
    "Image": ("Abbild", "Image", "Образ", "Imagen", "Immagine", "Obraz", "Image", "Imagem", "Obraz", "Kép", "Imagine", "Slika", "Slika", "Avbild", "Levykuva"),
    "Logo": ("Logo", "Logo", "Логотип", "Logotipo", "Logo", "Logo", "Logo", "Logótipo", "Logo", "Logó", "Logo", "Logo", "Logotip", "Logotyp", "Logo"),
    "Logo URL": ("Logo-URL", "URL du logo", "URL логотипа", "URL del logotipo", "URL del logo", "URL logo", "Logo-URL", "URL do logótipo", "URL loga", "Logó URL", "URL logo", "URL loga", "URL logotipa", "Logotyp-URL", "Logon URL"),
    "Homepage": ("Startseite", "Page d'accueil", "Домашня сторінка", "Página principal", "Pagina iniziale", "Strona główna", "Startpagina", "Página inicial", "Domovská stránka", "Honlap", "Pagina principală", "Početna stranica", "Domača stran", "Startsida", "Kotisivu"),
    "URL": ("URL", "URL", "URL", "URL", "URL", "URL", "URL", "URL", "URL", "URL", "URL", "URL", "URL", "URL", "URL"),
    "UUID": ("UUID", "UUID", "UUID", "UUID", "UUID", "UUID", "UUID", "UUID", "UUID", "UUID", "UUID", "UUID", "UUID", "UUID", "UUID"),
    "Id": ("ID", "ID", "Ідентифікатор", "ID", "ID", "ID", "ID", "ID", "ID", "Azonosító", "ID", "ID", "ID", "ID", "Tunnus"),
    "Enabled": ("Aktiviert", "Activé", "Увімкнено", "Habilitado", "Abilitato", "Włączone", "Ingeschakeld", "Ativado", "Povolené", "Engedélyezve", "Activat", "Omogućeno", "Omogočeno", "Aktiverad", "Käytössä"),
    "Required": ("Erforderlich", "Obligatoire", "Обов'язково", "Obligatorio", "Obbligatorio", "Wymagane", "Vereist", "Obrigatório", "Povinné", "Kötelező", "Obligatoriu", "Obavezno", "Obvezno", "Obligatorisk", "Pakollinen"),
    "Fields": ("Felder", "Champs", "Поля", "Campos", "Campi", "Pola", "Velden", "Campos", "Polia", "Mezők", "Câmpuri", "Polja", "Polja", "Fält", "Kentät"),
    "Config fields": ("Konfigurationsfelder", "Champs de configuration", "Поля конфігурації", "Campos de configuración", "Campi di configurazione", "Pola konfiguracji", "Configuratievelden", "Campos de configuração", "Konfiguračné polia", "Konfigurációs mezők", "Câmpuri de configurare", "Konfiguracijska polja", "Konfiguracijska polja", "Konfigurationsfält", "Asetuskentät"),
    "Configuration": ("Konfiguration", "Configuration", "Конфігурація", "Configuración", "Configurazione", "Konfiguracja", "Configuratie", "Configuração", "Konfigurácia", "Konfiguráció", "Configurare", "Konfiguracija", "Konfiguracija", "Konfiguration", "Asetukset"),
    "No configuration available.": ("Keine Konfiguration verfügbar.", "Aucune configuration disponible.", "Конфігурація недоступна.", "No hay configuración disponible.", "Nessuna configurazione disponibile.", "Brak dostępnej konfiguracji.", "Geen configuratie beschikbaar.", "Nenhuma configuração disponível.", "Žiadna konfigurácia nie je k dispozícii.", "Nincs elérhető konfiguráció.", "Nicio configurare disponibilă.", "Nema dostupne konfiguracije.", "Konfiguracija ni na voljo.", "Ingen konfiguration tillgänglig.", "Asetuksia ei ole saatavilla."),
    "No fields defined": ("Keine Felder definiert", "Aucun champ défini", "Поля не визначені", "No hay campos definidos", "Nessun campo definito", "Nie zdefiniowano pól", "Geen velden gedefinieerd", "Nenhum campo definido", "Nie sú definované žiadne polia", "Nincsenek mezők megadva", "Niciun câmp definit", "Nema definiranih polja", "Ni določenih polj", "Inga fält definierade", "Kenttiä ei ole määritetty"),
    "From": ("Von", "De", "Від", "Desde", "Da", "Od", "Van", "De", "Od", "Tól", "De la", "Od", "Od", "Från", "Mistä"),
    "To": ("An", "À", "До", "Hasta", "A", "Do", "Tot", "Para", "Do", "Ig", "Până la", "Do", "Do", "Till", "Mihin"),
    "Save": ("Speichern", "Enregistrer", "Зберегти", "Guardar", "Salva", "Zapisz", "Opslaan", "Guardar", "Uložiť", "Mentés", "Salvează", "Spremi", "Shrani", "Spara", "Tallenna"),
    "Save and back": ("Speichern und zurück", "Enregistrer et revenir", "Зберегти й повернутися", "Guardar y volver", "Salva e torna", "Zapisz i wróć", "Opslaan en terug", "Guardar e voltar", "Uložiť a späť", "Mentés és vissza", "Salvează și înapoi", "Spremi i natrag", "Shrani in nazaj", "Spara och tillbaka", "Tallenna ja takaisin"),
    "Save and next": ("Speichern und weiter", "Enregistrer et continuer", "Зберегти й далі", "Guardar y siguiente", "Salva e continua", "Zapisz i dalej", "Opslaan en volgende", "Guardar e seguinte", "Uložiť a ďalej", "Mentés és tovább", "Salvează și continuă", "Spremi i dalje", "Shrani in naprej", "Spara och nästa", "Tallenna ja jatka"),
    "Click to hide messages": ("Zum Ausblenden der Meldungen klicken", "Cliquez pour masquer les messages", "Натисніть, щоб приховати повідомлення", "Haga clic para ocultar los mensajes", "Clicca per nascondere i messaggi", "Kliknij, aby ukryć komunikaty", "Klik om berichten te verbergen", "Clique para ocultar as mensagens", "Kliknutím skryjete správy", "Kattintson az üzenetek elrejtéséhez", "Faceți clic pentru a ascunde mesajele", "Kliknite za skrivanje poruka", "Kliknite za skritje sporočil", "Klicka för att dölja meddelanden", "Piilota viestit napsauttamalla"),

    # --- Install wizard ---
    "MultiFlexi Installation": ("MultiFlexi-Installation", "Installation de MultiFlexi", "Встановлення MultiFlexi", "Instalación de MultiFlexi", "Installazione di MultiFlexi", "Instalacja MultiFlexi", "MultiFlexi-installatie", "Instalação do MultiFlexi", "Inštalácia MultiFlexi", "MultiFlexi telepítése", "Instalarea MultiFlexi", "Instalacija MultiFlexija", "Namestitev MultiFlexija", "MultiFlexi-installation", "MultiFlexin asennus"),
    "Set up the MultiFlexi automation platform on your Debian or Ubuntu system": ("Richten Sie die MultiFlexi-Automatisierungsplattform auf Ihrem Debian- oder Ubuntu-System ein", "Installez la plateforme d'automatisation MultiFlexi sur votre système Debian ou Ubuntu", "Налаштуйте платформу автоматизації MultiFlexi у вашій системі Debian або Ubuntu", "Configure la plataforma de automatización MultiFlexi en su sistema Debian o Ubuntu", "Configura la piattaforma di automazione MultiFlexi sul tuo sistema Debian o Ubuntu", "Skonfiguruj platformę automatyzacji MultiFlexi w systemie Debian lub Ubuntu", "Stel het MultiFlexi-automatiseringsplatform in op uw Debian- of Ubuntu-systeem", "Configure a plataforma de automação MultiFlexi no seu sistema Debian ou Ubuntu", "Nastavte automatizačnú platformu MultiFlexi vo svojom systéme Debian alebo Ubuntu", "Állítsa be a MultiFlexi automatizálási platformot Debian vagy Ubuntu rendszerén", "Configurați platforma de automatizare MultiFlexi pe sistemul dvs. Debian sau Ubuntu", "Postavite platformu za automatizaciju MultiFlexi na svom Debian ili Ubuntu sustavu", "Nastavite platformo za avtomatizacijo MultiFlexi v svojem sistemu Debian ali Ubuntu", "Konfigurera automatiseringsplattformen MultiFlexi på ditt Debian- eller Ubuntu-system", "Määritä MultiFlexi-automaatioalusta Debian- tai Ubuntu-järjestelmääsi"),
    "Production Repository": ("Produktions-Repository", "Dépôt de production", "Репозиторій для виробництва", "Repositorio de producción", "Repository di produzione", "Repozytorium produkcyjne", "Productierepository", "Repositório de produção", "Produkčný repozitár", "Éles tároló", "Depozit de producție", "Produkcijski repozitorij", "Produkcijski repozitorij", "Produktionsförråd", "Tuotantopakettivarasto"),
    "Testing Repository": ("Test-Repository", "Dépôt de test", "Тестовий репозиторій", "Repositorio de pruebas", "Repository di test", "Repozytorium testowe", "Testrepository", "Repositório de testes", "Testovací repozitár", "Tesztelési tároló", "Depozit de testare", "Testni repozitorij", "Testni repozitorij", "Testförråd", "Testipakettivarasto"),
    "Stable": ("Stabil", "Stable", "Стабільний", "Estable", "Stabile", "Stabilne", "Stabiel", "Estável", "Stabilné", "Stabil", "Stabil", "Stabilno", "Stabilno", "Stabil", "Vakaa"),
    "Testing": ("Testing", "Test", "Тестування", "Pruebas", "Test", "Testowe", "Testing", "Testes", "Testovacie", "Tesztelés", "Testare", "Testiranje", "Testiranje", "Testning", "Testaus"),
    "Stable releases — recommended for production systems": ("Stabile Versionen – empfohlen für Produktivsysteme", "Versions stables — recommandées pour les systèmes de production", "Стабільні випуски — рекомендовано для робочих систем", "Versiones estables — recomendadas para sistemas de producción", "Versioni stabili — consigliate per i sistemi di produzione", "Wydania stabilne — zalecane dla systemów produkcyjnych", "Stabiele releases — aanbevolen voor productiesystemen", "Versões estáveis — recomendadas para sistemas de produção", "Stabilné vydania — odporúčané pre produkčné systémy", "Stabil kiadások – éles rendszerekhez ajánlott", "Versiuni stabile — recomandate pentru sistemele de producție", "Stabilna izdanja — preporučeno za produkcijske sustave", "Stabilne izdaje — priporočeno za produkcijske sisteme", "Stabila utgåvor – rekommenderas för produktionssystem", "Vakaat julkaisut — suositellaan tuotantojärjestelmiin"),
    "Development builds — latest features, may be unstable": ("Entwicklungs-Builds – neueste Funktionen, möglicherweise instabil", "Versions de développement — dernières fonctionnalités, peut être instable", "Збірки для розробки — найновіші можливості, можуть бути нестабільними", "Compilaciones de desarrollo — últimas funciones, pueden ser inestables", "Build di sviluppo — funzionalità più recenti, potrebbero essere instabili", "Wersje deweloperskie — najnowsze funkcje, mogą być niestabilne", "Ontwikkelbuilds — nieuwste functies, kunnen instabiel zijn", "Compilações de desenvolvimento — funcionalidades mais recentes, podem ser instáveis", "Vývojové zostavenia — najnovšie funkcie, môžu byť nestabilné", "Fejlesztői build-ek – legújabb funkciók, instabil lehet", "Versiuni de dezvoltare — cele mai noi funcții, pot fi instabile", "Razvojne verzije — najnovije značajke, mogu biti nestabilne", "Razvojne gradnje — najnovejše funkcije, lahko nestabilne", "Utvecklingsbyggen – senaste funktioner, kan vara instabila", "Kehitysversiot — uusimmat ominaisuudet, voivat olla epävakaita"),
    "Choose distribution": ("Distribution wählen", "Choisir la distribution", "Виберіть дистрибутив", "Elija la distribución", "Scegli la distribuzione", "Wybierz dystrybucję", "Kies distributie", "Escolha a distribuição", "Vyberte distribúciu", "Válasszon disztribúciót", "Alegeți distribuția", "Odaberite distribuciju", "Izberite distribucijo", "Välj distribution", "Valitse jakelu"),
    "Select your distribution:": ("Wählen Sie Ihre Distribution:", "Sélectionnez votre distribution :", "Виберіть свій дистрибутив:", "Seleccione su distribución:", "Seleziona la tua distribuzione:", "Wybierz swoją dystrybucję:", "Selecteer uw distributie:", "Selecione a sua distribuição:", "Vyberte svoju distribúciu:", "Válassza ki a disztribúcióját:", "Selectați distribuția:", "Odaberite svoju distribuciju:", "Izberite svojo distribucijo:", "Välj din distribution:", "Valitse jakelusi:"),
    "Select components:": ("Komponenten auswählen:", "Sélectionnez les composants :", "Виберіть компоненти:", "Seleccione los componentes:", "Seleziona i componenti:", "Wybierz komponenty:", "Selecteer componenten:", "Selecione os componentes:", "Vyberte komponenty:", "Válassza ki az összetevőket:", "Selectați componentele:", "Odaberite komponente:", "Izberite komponente:", "Välj komponenter:", "Valitse osat:"),
    "Create Configuration File": ("Konfigurationsdatei erstellen", "Créer le fichier de configuration", "Створити файл конфігурації", "Crear archivo de configuración", "Crea il file di configurazione", "Utwórz plik konfiguracyjny", "Configuratiebestand maken", "Criar ficheiro de configuração", "Vytvoriť konfiguračný súbor", "Konfigurációs fájl létrehozása", "Creați fișierul de configurare", "Stvori konfiguracijsku datoteku", "Ustvari konfiguracijsko datoteko", "Skapa konfigurationsfil", "Luo asetustiedosto"),
    "Import the GPG Key": ("GPG-Schlüssel importieren", "Importer la clé GPG", "Імпортувати ключ GPG", "Importar la clave GPG", "Importa la chiave GPG", "Zaimportuj klucz GPG", "GPG-sleutel importeren", "Importar a chave GPG", "Importovať GPG kľúč", "GPG-kulcs importálása", "Importați cheia GPG", "Uvezi GPG ključ", "Uvozi ključ GPG", "Importera GPG-nyckeln", "Tuo GPG-avain"),
    "Step 1: Prepare your system": ("Schritt 1: System vorbereiten", "Étape 1 : Préparez votre système", "Крок 1: Підготуйте систему", "Paso 1: Prepare su sistema", "Passo 1: Prepara il sistema", "Krok 1: Przygotuj system", "Stap 1: Bereid uw systeem voor", "Passo 1: Prepare o seu sistema", "Krok 1: Pripravte svoj systém", "1. lépés: Készítse elő a rendszerét", "Pasul 1: Pregătiți-vă sistemul", "Korak 1: Pripremite svoj sustav", "Korak 1: Pripravite svoj sistem", "Steg 1: Förbered ditt system", "Vaihe 1: Valmistele järjestelmäsi"),
    "Step 2: Configure APT Repository": ("Schritt 2: APT-Repository konfigurieren", "Étape 2 : Configurez le dépôt APT", "Крок 2: Налаштуйте репозиторій APT", "Paso 2: Configure el repositorio APT", "Passo 2: Configura il repository APT", "Krok 2: Skonfiguruj repozytorium APT", "Stap 2: Configureer de APT-repository", "Passo 2: Configure o repositório APT", "Krok 2: Nakonfigurujte APT repozitár", "2. lépés: Állítsa be az APT-tárolót", "Pasul 2: Configurați depozitul APT", "Korak 2: Konfigurirajte APT repozitorij", "Korak 2: Konfigurirajte repozitorij APT", "Steg 2: Konfigurera APT-förrådet", "Vaihe 2: Määritä APT-pakettivarasto"),
    "Step 3: Update Sources": ("Schritt 3: Quellen aktualisieren", "Étape 3 : Mettez à jour les sources", "Крок 3: Оновіть джерела", "Paso 3: Actualice las fuentes", "Passo 3: Aggiorna le sorgenti", "Krok 3: Zaktualizuj źródła", "Stap 3: Werk de bronnen bij", "Passo 3: Atualize as fontes", "Krok 3: Aktualizujte zdroje", "3. lépés: Frissítse a forrásokat", "Pasul 3: Actualizați sursele", "Korak 3: Ažurirajte izvore", "Korak 3: Posodobite vire", "Steg 3: Uppdatera källorna", "Vaihe 3: Päivitä lähteet"),
    "Step 4: Install for your chosen database": ("Schritt 4: Für die gewählte Datenbank installieren", "Étape 4 : Installez pour la base de données choisie", "Крок 4: Встановіть для обраної бази даних", "Paso 4: Instale para la base de datos elegida", "Passo 4: Installa per il database scelto", "Krok 4: Zainstaluj dla wybranej bazy danych", "Stap 4: Installeer voor de gekozen database", "Passo 4: Instale para a base de dados escolhida", "Krok 4: Nainštalujte pre zvolenú databázu", "4. lépés: Telepítse a választott adatbázishoz", "Pasul 4: Instalați pentru baza de date aleasă", "Korak 4: Instalirajte za odabranu bazu podataka", "Korak 4: Namestite za izbrano podatkovno zbirko", "Steg 4: Installera för din valda databas", "Vaihe 4: Asenna valitsemallesi tietokannalle"),
    "Step 5: Discover available applications": ("Schritt 5: Verfügbare Anwendungen entdecken", "Étape 5 : Découvrez les applications disponibles", "Крок 5: Перегляньте доступні застосунки", "Paso 5: Descubra las aplicaciones disponibles", "Passo 5: Scopri le applicazioni disponibili", "Krok 5: Odkryj dostępne aplikacje", "Stap 5: Ontdek beschikbare applicaties", "Passo 5: Descubra as aplicações disponíveis", "Krok 5: Objavte dostupné aplikácie", "5. lépés: Fedezze fel az elérhető alkalmazásokat", "Pasul 5: Descoperiți aplicațiile disponibile", "Korak 5: Otkrijte dostupne aplikacije", "Korak 5: Odkrijte razpoložljive aplikacije", "Steg 5: Upptäck tillgängliga applikationer", "Vaihe 5: Tutustu saatavilla oleviin sovelluksiin"),
    "Quick install command:": ("Schnellinstallationsbefehl:", "Commande d'installation rapide :", "Команда швидкого встановлення:", "Comando de instalación rápida:", "Comando di installazione rapida:", "Polecenie szybkiej instalacji:", "Opdracht voor snelle installatie:", "Comando de instalação rápida:", "Príkaz na rýchlu inštaláciu:", "Gyors telepítési parancs:", "Comandă de instalare rapidă:", "Naredba za brzu instalaciju:", "Ukaz za hitro namestitev:", "Snabbinstallationskommando:", "Pika-asennuskomento:"),
    "Copy and paste this into your terminal:": ("Kopieren Sie dies in Ihr Terminal:", "Copiez-collez ceci dans votre terminal :", "Скопіюйте це у свій термінал:", "Copie y pegue esto en su terminal:", "Copia e incolla questo nel tuo terminale:", "Skopiuj i wklej to do terminala:", "Kopieer en plak dit in uw terminal:", "Copie e cole isto no seu terminal:", "Skopírujte toto do svojho terminálu:", "Másolja be ezt a terminálba:", "Copiați și lipiți acest lucru în terminal:", "Kopirajte ovo u svoj terminal:", "Kopirajte to v svoj terminal:", "Kopiera och klistra in detta i din terminal:", "Kopioi ja liitä tämä päätteeseesi:"),
    "Search the repository for MultiFlexi application packages:": ("Durchsuchen Sie das Repository nach MultiFlexi-Anwendungspaketen:", "Recherchez les paquets d'applications MultiFlexi dans le dépôt :", "Шукайте пакети застосунків MultiFlexi у репозиторії:", "Busque paquetes de aplicaciones MultiFlexi en el repositorio:", "Cerca i pacchetti delle applicazioni MultiFlexi nel repository:", "Wyszukaj w repozytorium pakiety aplikacji MultiFlexi:", "Zoek in de repository naar MultiFlexi-applicatiepakketten:", "Pesquise no repositório pacotes de aplicações MultiFlexi:", "Vyhľadajte v repozitári balíky aplikácií MultiFlexi:", "Keressen MultiFlexi alkalmazáscsomagokat a tárolóban:", "Căutați în depozit pachete de aplicații MultiFlexi:", "Pretražite repozitorij za pakete aplikacija MultiFlexi:", "Poiščite pakete aplikacij MultiFlexi v repozitoriju:", "Sök i förrådet efter MultiFlexi-applikationspaket:", "Etsi pakettivarastosta MultiFlexi-sovelluspaketteja:"),

    # --- Auth / account ---
    "Sign in to MultiFlexi": ("Bei MultiFlexi anmelden", "Se connecter à MultiFlexi", "Увійти до MultiFlexi", "Iniciar sesión en MultiFlexi", "Accedi a MultiFlexi", "Zaloguj się do MultiFlexi", "Inloggen bij MultiFlexi", "Entrar no MultiFlexi", "Prihlásiť sa do MultiFlexi", "Bejelentkezés a MultiFlexibe", "Autentificare în MultiFlexi", "Prijava u MultiFlexi", "Prijava v MultiFlexi", "Logga in på MultiFlexi", "Kirjaudu MultiFlexiin"),
    "Email address": ("E-Mail-Adresse", "Adresse e-mail", "Адреса електронної пошти", "Dirección de correo electrónico", "Indirizzo e-mail", "Adres e-mail", "E-mailadres", "Endereço de e-mail", "E-mailová adresa", "E-mail-cím", "Adresă de e-mail", "Adresa e-pošte", "E-poštni naslov", "E-postadress", "Sähköpostiosoite"),
    "eMail address": ("E-Mail-Adresse", "Adresse e-mail", "Адреса електронної пошти", "Dirección de correo electrónico", "Indirizzo e-mail", "Adres e-mail", "E-mailadres", "Endereço de e-mail", "E-mailová adresa", "E-mail-cím", "Adresă de e-mail", "Adresa e-pošte", "E-poštni naslov", "E-postadress", "Sähköpostiosoite"),
    "Password": ("Passwort", "Mot de passe", "Пароль", "Contraseña", "Password", "Hasło", "Wachtwoord", "Palavra-passe", "Heslo", "Jelszó", "Parolă", "Lozinka", "Geslo", "Lösenord", "Salasana"),
    "Password confirmation": ("Passwortbestätigung", "Confirmation du mot de passe", "Підтвердження пароля", "Confirmación de contraseña", "Conferma password", "Potwierdzenie hasła", "Wachtwoordbevestiging", "Confirmação da palavra-passe", "Potvrdenie hesla", "Jelszó megerősítése", "Confirmarea parolei", "Potvrda lozinke", "Potrditev gesla", "Bekräfta lösenord", "Salasanan vahvistus"),
    "User name": ("Benutzername", "Nom d'utilisateur", "Ім'я користувача", "Nombre de usuario", "Nome utente", "Nazwa użytkownika", "Gebruikersnaam", "Nome de utilizador", "Používateľské meno", "Felhasználónév", "Nume de utilizator", "Korisničko ime", "Uporabniško ime", "Användarnamn", "Käyttäjänimi"),
    "Username": ("Benutzername", "Nom d'utilisateur", "Ім'я користувача", "Nombre de usuario", "Nome utente", "Nazwa użytkownika", "Gebruikersnaam", "Nome de utilizador", "Používateľské meno", "Felhasználónév", "Nume de utilizator", "Korisničko ime", "Uporabniško ime", "Användarnamn", "Käyttäjänimi"),
    "Firstname": ("Vorname", "Prénom", "Ім'я", "Nombre", "Nome", "Imię", "Voornaam", "Nome próprio", "Meno", "Keresztnév", "Prenume", "Ime", "Ime", "Förnamn", "Etunimi"),
    "Lastname": ("Nachname", "Nom", "Прізвище", "Apellido", "Cognome", "Nazwisko", "Achternaam", "Apelido", "Priezvisko", "Vezetéknév", "Nume de familie", "Prezime", "Priimek", "Efternamn", "Sukunimi"),
    "Register": ("Registrieren", "S'inscrire", "Зареєструватися", "Registrarse", "Registrati", "Zarejestruj się", "Registreren", "Registar", "Zaregistrovať", "Regisztráció", "Înregistrare", "Registracija", "Registracija", "Registrera", "Rekisteröidy"),
    "finish registration": ("Registrierung abschließen", "terminer l'inscription", "завершити реєстрацію", "finalizar el registro", "completa la registrazione", "zakończ rejestrację", "registratie voltooien", "concluir o registo", "dokončiť registráciu", "regisztráció befejezése", "finalizați înregistrarea", "dovršiti registraciju", "dokončaj registracijo", "slutför registreringen", "viimeistele rekisteröinti"),
    "Password Recovery": ("Passwortwiederherstellung", "Récupération du mot de passe", "Відновлення пароля", "Recuperación de contraseña", "Recupero password", "Odzyskiwanie hasła", "Wachtwoordherstel", "Recuperação de palavra-passe", "Obnova hesla", "Jelszó-helyreállítás", "Recuperarea parolei", "Oporavak lozinke", "Obnovitev gesla", "Återställning av lösenord", "Salasanan palautus"),
    "Password recovery": ("Passwortwiederherstellung", "Récupération du mot de passe", "Відновлення пароля", "Recuperación de contraseña", "Recupero password", "Odzyskiwanie hasła", "Wachtwoordherstel", "Recuperação de palavra-passe", "Obnova hesla", "Jelszó-helyreállítás", "Recuperarea parolei", "Oporavak lozinke", "Obnovitev gesla", "Återställning av lösenord", "Salasanan palautus"),
    "Send New Password": ("Neues Passwort senden", "Envoyer un nouveau mot de passe", "Надіслати новий пароль", "Enviar nueva contraseña", "Invia nuova password", "Wyślij nowe hasło", "Nieuw wachtwoord verzenden", "Enviar nova palavra-passe", "Odoslať nové heslo", "Új jelszó küldése", "Trimite parola nouă", "Pošalji novu lozinku", "Pošlji novo geslo", "Skicka nytt lösenord", "Lähetä uusi salasana"),
    "Forgot your password? Enter the e-mail address you used during registration and we will send you a new one.": ("Passwort vergessen? Geben Sie die bei der Registrierung verwendete E-Mail-Adresse ein, und wir senden Ihnen ein neues.", "Mot de passe oublié ? Saisissez l'adresse e-mail utilisée lors de l'inscription et nous vous en enverrons un nouveau.", "Забули пароль? Введіть адресу електронної пошти, яку ви використовували під час реєстрації, і ми надішлемо вам новий.", "¿Olvidó su contraseña? Introduzca la dirección de correo electrónico que usó durante el registro y le enviaremos una nueva.", "Hai dimenticato la password? Inserisci l'indirizzo e-mail usato durante la registrazione e te ne invieremo una nuova.", "Nie pamiętasz hasła? Wpisz adres e-mail użyty podczas rejestracji, a wyślemy Ci nowe.", "Wachtwoord vergeten? Voer het e-mailadres in dat u bij de registratie hebt gebruikt, dan sturen wij u een nieuw wachtwoord.", "Esqueceu-se da palavra-passe? Introduza o endereço de e-mail que usou no registo e enviar-lhe-emos uma nova.", "Zabudli ste heslo? Zadajte e-mailovú adresu, ktorú ste použili pri registrácii, a pošleme vám nové.", "Elfelejtette a jelszavát? Adja meg a regisztrációkor használt e-mail-címet, és küldünk egy újat.", "Ați uitat parola? Introduceți adresa de e-mail folosită la înregistrare și vă vom trimite una nouă.", "Zaboravili ste lozinku? Unesite e-adresu koju ste koristili pri registraciji i poslat ćemo vam novu.", "Ste pozabili geslo? Vnesite e-poštni naslov, ki ste ga uporabili ob registraciji, in poslali vam bomo novega.", "Glömt lösenordet? Ange e-postadressen du använde vid registreringen så skickar vi ett nytt.", "Unohditko salasanasi? Anna rekisteröinnissä käyttämäsi sähköpostiosoite, niin lähetämme sinulle uuden."),
    "Check your inbox — your new password has been sent.": ("Prüfen Sie Ihren Posteingang – Ihr neues Passwort wurde gesendet.", "Vérifiez votre boîte de réception — votre nouveau mot de passe a été envoyé.", "Перевірте свою поштову скриньку — ваш новий пароль надіслано.", "Revise su bandeja de entrada: se ha enviado su nueva contraseña.", "Controlla la tua casella di posta — la nuova password è stata inviata.", "Sprawdź skrzynkę odbiorczą — nowe hasło zostało wysłane.", "Controleer uw inbox — uw nieuwe wachtwoord is verzonden.", "Verifique a sua caixa de entrada — a sua nova palavra-passe foi enviada.", "Skontrolujte si schránku — vaše nové heslo bolo odoslané.", "Ellenőrizze a postaládáját – elküldtük az új jelszavát.", "Verificați-vă căsuța de e-mail — noua parolă a fost trimisă.", "Provjerite pristiglu poštu — vaša nova lozinka je poslana.", "Preverite svoj nabiralnik — vaše novo geslo je bilo poslano.", "Kontrollera din inkorg – ditt nya lösenord har skickats.", "Tarkista postilaatikkosi — uusi salasanasi on lähetetty."),
    "Back to Sign In": ("Zurück zur Anmeldung", "Retour à la connexion", "Назад до входу", "Volver a iniciar sesión", "Torna all'accesso", "Powrót do logowania", "Terug naar inloggen", "Voltar ao início de sessão", "Späť na prihlásenie", "Vissza a bejelentkezéshez", "Înapoi la autentificare", "Natrag na prijavu", "Nazaj na prijavo", "Tillbaka till inloggning", "Takaisin kirjautumiseen"),
    "Good bye": ("Auf Wiedersehen", "Au revoir", "До побачення", "Adiós", "Arrivederci", "Do widzenia", "Tot ziens", "Adeus", "Dovidenia", "Viszontlátásra", "La revedere", "Doviđenja", "Nasvidenje", "Hej då", "Näkemiin"),
    "I agree to the terms and conditions": ("Ich stimme den Allgemeinen Geschäftsbedingungen zu", "J'accepte les conditions générales", "Я погоджуюся з умовами та положеннями", "Acepto los términos y condiciones", "Accetto i termini e le condizioni", "Akceptuję regulamin", "Ik ga akkoord met de algemene voorwaarden", "Aceito os termos e condições", "Súhlasím s obchodnými podmienkami", "Elfogadom a felhasználási feltételeket", "Sunt de acord cu termenii și condițiile", "Prihvaćam uvjete i odredbe", "Strinjam se s pogoji uporabe", "Jag godkänner villkoren", "Hyväksyn käyttöehdot"),
    "I consent to the processing of personal data according to GDPR": ("Ich willige in die Verarbeitung personenbezogener Daten gemäß DSGVO ein", "Je consens au traitement des données personnelles conformément au RGPD", "Я даю згоду на обробку персональних даних відповідно до GDPR", "Doy mi consentimiento para el tratamiento de datos personales según el RGPD", "Acconsento al trattamento dei dati personali secondo il GDPR", "Wyrażam zgodę na przetwarzanie danych osobowych zgodnie z RODO", "Ik geef toestemming voor de verwerking van persoonsgegevens volgens de AVG", "Consinto o tratamento de dados pessoais de acordo com o RGPD", "Súhlasím so spracovaním osobných údajov podľa GDPR", "Hozzájárulok a személyes adatok GDPR szerinti kezeléséhez", "Sunt de acord cu prelucrarea datelor personale conform GDPR", "Pristajem na obradu osobnih podataka u skladu s GDPR-om", "Soglašam z obdelavo osebnih podatkov v skladu s GDPR", "Jag samtycker till behandling av personuppgifter enligt GDPR", "Hyväksyn henkilötietojen käsittelyn GDPR:n mukaisesti"),

    # --- App / credential editor labels ---
    "Application name": ("Anwendungsname", "Nom de l'application", "Назва застосунку", "Nombre de la aplicación", "Nome dell'applicazione", "Nazwa aplikacji", "Applicatienaam", "Nome da aplicação", "Názov aplikácie", "Alkalmazás neve", "Numele aplicației", "Naziv aplikacije", "Ime aplikacije", "Applikationsnamn", "Sovelluksen nimi"),
    "Application code": ("Anwendungscode", "Code de l'application", "Код застосунку", "Código de la aplicación", "Codice dell'applicazione", "Kod aplikacji", "Applicatiecode", "Código da aplicação", "Kód aplikácie", "Alkalmazás kódja", "Codul aplicației", "Kôd aplikacije", "Koda aplikacije", "Applikationskod", "Sovelluksen koodi"),
    "Application Description": ("Anwendungsbeschreibung", "Description de l'application", "Опис застосунку", "Descripción de la aplicación", "Descrizione dell'applicazione", "Opis aplikacji", "Applicatiebeschrijving", "Descrição da aplicação", "Popis aplikácie", "Alkalmazás leírása", "Descrierea aplicației", "Opis aplikacije", "Opis aplikacije", "Applikationsbeskrivning", "Sovelluksen kuvaus"),
    "Application Icon": ("Anwendungssymbol", "Icône de l'application", "Значок застосунку", "Icono de la aplicación", "Icona dell'applicazione", "Ikona aplikacji", "Applicatiepictogram", "Ícone da aplicação", "Ikona aplikácie", "Alkalmazás ikonja", "Pictograma aplicației", "Ikona aplikacije", "Ikona aplikacije", "Applikationsikon", "Sovelluksen kuvake"),
    "Application Homepage": ("Anwendungs-Startseite", "Page d'accueil de l'application", "Домашня сторінка застосунку", "Página principal de la aplicación", "Pagina iniziale dell'applicazione", "Strona główna aplikacji", "Startpagina van de applicatie", "Página inicial da aplicação", "Domovská stránka aplikácie", "Alkalmazás honlapja", "Pagina principală a aplicației", "Početna stranica aplikacije", "Domača stran aplikacije", "Applikationens startsida", "Sovelluksen kotisivu"),
    "Container image": ("Container-Abbild", "Image du conteneur", "Образ контейнера", "Imagen del contenedor", "Immagine del contenitore", "Obraz kontenera", "Containerimage", "Imagem do contentor", "Obraz kontajnera", "Tárolókép", "Imaginea containerului", "Slika spremnika", "Slika vsebnika", "Containeravbild", "Säilön levykuva"),
    "Path to binary": ("Pfad zur Binärdatei", "Chemin du binaire", "Шлях до виконуваного файлу", "Ruta al binario", "Percorso del binario", "Ścieżka do pliku binarnego", "Pad naar binary", "Caminho do binário", "Cesta k binárnemu súboru", "Bináris elérési útja", "Calea către binar", "Putanja do binarne datoteke", "Pot do binarne datoteke", "Sökväg till binärfil", "Polku binääriin"),
    "Setup Command": ("Einrichtungsbefehl", "Commande de configuration", "Команда налаштування", "Comando de configuración", "Comando di configurazione", "Polecenie konfiguracji", "Installatieopdracht", "Comando de configuração", "Príkaz na nastavenie", "Beállítási parancs", "Comandă de configurare", "Naredba za postavljanje", "Ukaz za namestitev", "Konfigurationskommando", "Asetuskomento"),
    "Command arguments": ("Befehlsargumente", "Arguments de la commande", "Аргументи команди", "Argumentos del comando", "Argomenti del comando", "Argumenty polecenia", "Opdrachtargumenten", "Argumentos do comando", "Argumenty príkazu", "Parancsargumentumok", "Argumentele comenzii", "Argumenti naredbe", "Argumenti ukaza", "Kommandoargument", "Komennon argumentit"),
    "Credential Type Name": ("Name des Anmeldedatentyps", "Nom du type d'identifiant", "Назва типу облікових даних", "Nombre del tipo de credencial", "Nome del tipo di credenziale", "Nazwa typu poświadczeń", "Naam van type inloggegevens", "Nome do tipo de credencial", "Názov typu poverenia", "Hitelesítő típus neve", "Numele tipului de credențial", "Naziv vrste vjerodajnice", "Ime vrste poverilnice", "Namn på autentiseringstyp", "Tunnistetyypin nimi"),
    "Add users who can edit this item.": ("Fügen Sie Benutzer hinzu, die diesen Eintrag bearbeiten dürfen.", "Ajoutez les utilisateurs autorisés à modifier cet élément.", "Додайте користувачів, які можуть редагувати цей елемент.", "Añada los usuarios que pueden editar este elemento.", "Aggiungi gli utenti che possono modificare questo elemento.", "Dodaj użytkowników, którzy mogą edytować ten element.", "Voeg gebruikers toe die dit item mogen bewerken.", "Adicione utilizadores que podem editar este item.", "Pridajte používateľov, ktorí môžu upravovať túto položku.", "Adja meg azokat a felhasználókat, akik szerkeszthetik ezt az elemet.", "Adăugați utilizatorii care pot edita acest element.", "Dodajte korisnike koji mogu uređivati ovu stavku.", "Dodajte uporabnike, ki lahko urejajo ta element.", "Lägg till användare som kan redigera detta objekt.", "Lisää käyttäjät, jotka voivat muokata tätä kohdetta."),
    "Coworkers": ("Mitarbeiter", "Collaborateurs", "Колеги", "Colaboradores", "Collaboratori", "Współpracownicy", "Medewerkers", "Colaboradores", "Spolupracovníci", "Munkatársak", "Colegi", "Suradnici", "Sodelavci", "Medarbetare", "Työtoverit"),
}


def parse_pot(path):
    """Return list of (comment_lines, msgid_block_text, msgid_value)."""
    with open(path, encoding="utf-8") as fh:
        lines = fh.read().split("\n")
    blocks = []
    cur = []
    for line in lines:
        if line.strip() == "" and cur:
            blocks.append(cur)
            cur = []
        else:
            cur.append(line)
    if cur:
        blocks.append(cur)
    return blocks


def block_msgid(block):
    """Extract the (possibly multi-line) msgid value of a block."""
    collecting = False
    parts = []
    for line in block:
        if line.startswith("msgid "):
            collecting = True
            parts.append(line[len("msgid "):])
        elif collecting and line.startswith('"'):
            parts.append(line)
        elif collecting and line.startswith("msgstr"):
            break
    raw = "".join(parts)
    return "".join(re.findall(r'"((?:[^"\\]|\\.)*)"', raw))


def po_escape(s):
    return s.replace("\\", "\\\\").replace('"', '\\"').replace("\n", "\\n")


def build_po(locale, plural):
    blocks = parse_pot(POT)
    out = []
    header = f'''msgid ""
msgstr ""
"Project-Id-Version: MultiFlexiEU\\n"
"Report-Msgid-Bugs-To: \\n"
"PO-Revision-Date: 2026-06-08 00:00+0000\\n"
"Last-Translator: MultiFlexi i18n generator\\n"
"Language-Team: {locale}\\n"
"Language: {locale}\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"Plural-Forms: {plural}\\n"'''
    out.append(header)
    idx = ORDER.index(locale)
    translated = 0
    for block in blocks:
        msgid = block_msgid(block)
        if msgid == "":
            continue  # header handled above
        tr = TR.get(msgid)
        comment = [l for l in block if l.startswith("#")]
        chunk = list(comment)
        # re-emit the original msgid block lines (preserve multiline form)
        in_msgid = False
        for line in block:
            if line.startswith("msgid "):
                in_msgid = True
                chunk.append(line)
            elif in_msgid and line.startswith('"'):
                chunk.append(line)
            elif line.startswith("msgstr"):
                in_msgid = False
        if tr and tr[idx]:
            chunk.append(f'msgstr "{po_escape(tr[idx])}"')
            translated += 1
        else:
            chunk.append('msgstr ""')
        out.append("\n".join(chunk))
    return "\n\n".join(out) + "\n", translated


def main():
    total = 0
    for locale, plural in LANGS.items():
        d = os.path.join(HERE, locale, "LC_MESSAGES")
        os.makedirs(d, exist_ok=True)
        po_path = os.path.join(d, f"{DOMAIN}.po")
        mo_path = os.path.join(d, f"{DOMAIN}.mo")
        content, translated = build_po(locale, plural)
        with open(po_path, "w", encoding="utf-8") as fh:
            fh.write(content)
        subprocess.run(["msgfmt", "-o", mo_path, po_path], check=True)
        print(f"{locale}: {translated} translated -> {po_path}")
        total += 1
    print(f"Generated {total} locales.")


if __name__ == "__main__":
    sys.exit(main())
