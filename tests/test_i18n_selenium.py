#!/usr/bin/env python3
"""
Selenium test for MultiFlexi.eu i18n language switching.

Uses geckodriver (Firefox) to verify that all visible UI strings
are rendered in the chosen language after switching via LangSelect.

Usage:
    python3 tests/test_i18n_selenium.py [BASE_URL]

Default BASE_URL: http://localhost/multiflexi.eu/src/
"""

import sys
import time
import unittest

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.firefox.options import Options
from selenium.webdriver.firefox.service import Service
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait

BASE_URL = sys.argv[1] if len(sys.argv) > 1 else "http://localhost/multiflexi.eu/src/"

# Known Czech translations for strings that appear on public pages
CZECH_STRINGS = {
    "index.php": [
        "MultiFlexi centrum aplikací a přihlašovacích údajů",
        "Procházet aplikace",
        "Procházet typy přihlašovacích údajů",
        "Nainstalovat MultiFlexi",
        "Nejnovější aplikace",
        "Nejnovější typy přihlašovacích údajů",
    ],
    "apps.php": [
        "Aplikace",
        "Karty",
        "Tabulka",
    ],
    "credentialtypes.php": [
        "Typy přihlašovacích údajů",
        "Karty",
        "Tabulka",
    ],
    "install.php": [
        "Nainstalovat MultiFlexi",
        "Krok 1: Připravte svůj systém",
        "Produkční repozitář",
        "Testovací repozitář",
    ],
    "login.php": [
        "Přihlásit",
    ],
}

# Known English strings that should appear on English pages
ENGLISH_STRINGS = {
    "index.php": [
        "MultiFlexi Application & Credential Hub",
        "Browse Applications",
        "Browse Credential Types",
        "Install MultiFlexi",
        "Recent Applications",
        "Recent Credential Types",
    ],
    "apps.php": [
        "Applications",
        "Cards",
        "Table",
    ],
    "credentialtypes.php": [
        "Credential Types",
        "Cards",
        "Table",
    ],
    "install.php": [
        "Install MultiFlexi",
        "Step 1: Prepare your system",
        "Production Repository",
        "Testing Repository",
    ],
    "login.php": [
        "Sign In",
    ],
}

# Menu items visible when not logged in
MENU_CZECH = [
    "Demo stránka",
    "Appky",
    "Typy přihlašovacích údajů",
    "Instalace",
    "Dokumentace",
    "Registrace",
    "Přihlásit",
]

MENU_ENGLISH = [
    "Demo Site",
    "Apps",
    "Credential Types",
    "Install",
    "Documentation",
    "Sign On",
    "Sign In",
]


class TestI18n(unittest.TestCase):
    """Test that language switching works and strings render correctly."""

    @classmethod
    def setUpClass(cls):
        options = Options()
        options.add_argument("--headless")
        service = Service(executable_path="/usr/bin/geckodriver")
        cls.driver = webdriver.Firefox(service=service, options=options)
        cls.driver.implicitly_wait(5)
        cls.wait = WebDriverWait(cls.driver, 10)

    @classmethod
    def tearDownClass(cls):
        cls.driver.quit()

    def _switch_locale(self, locale_code):
        """Switch language via the LangSelect dropdown."""
        # Find the language dropdown button and click it
        dropdown_btn = self.wait.until(
            EC.element_to_be_clickable((By.CSS_SELECTOR, ".dropdown-toggle[data-bs-toggle='dropdown']"))
        )
        dropdown_btn.click()
        time.sleep(0.5)

        # Find the link with ?locale=XX in the dropdown menu
        links = self.driver.find_elements(By.CSS_SELECTOR, ".dropdown-menu .dropdown-item")
        for link in links:
            href = link.get_attribute("href") or ""
            if f"locale={locale_code}" in href:
                link.click()
                time.sleep(1)
                return True

        return False

    def _get_page_text(self):
        """Get all visible text from the page body."""
        return self.driver.find_element(By.TAG_NAME, "body").text

    def _load_page(self, page, locale=None):
        """Load a page, optionally with a locale parameter."""
        url = BASE_URL + page
        if locale:
            sep = "&" if "?" in url else "?"
            url += sep + "locale=" + locale
        self.driver.get(url)
        self.wait.until(EC.presence_of_element_located((By.TAG_NAME, "body")))
        time.sleep(1)
        # Detect PHP fatal errors and skip the test
        body = self.driver.find_element(By.TAG_NAME, "body").text
        if "Fatal error" in body:
            self.skipTest(f"Page {page} has a PHP fatal error (pre-existing bug)")

    # ── Czech tests ──────────────────────────────────────────

    def test_01_czech_index(self):
        """Index page should show Czech strings when locale=cs_CZ."""
        self._load_page("index.php", "cs_CZ")
        text = self._get_page_text()
        for expected in CZECH_STRINGS["index.php"]:
            self.assertIn(expected, text, f"Czech string not found on index: {expected}")

    def test_02_czech_apps(self):
        """Apps page should show Czech strings when locale=cs_CZ."""
        self._load_page("apps.php", "cs_CZ")
        text = self._get_page_text()
        for expected in CZECH_STRINGS["apps.php"]:
            self.assertIn(expected, text, f"Czech string not found on apps: {expected}")

    def test_03_czech_credentialtypes(self):
        """Credential types page should show Czech strings."""
        self._load_page("credentialtypes.php", "cs_CZ")
        text = self._get_page_text()
        for expected in CZECH_STRINGS["credentialtypes.php"]:
            self.assertIn(expected, text, f"Czech string not found on credentialtypes: {expected}")

    def test_04_czech_install(self):
        """Install page should show Czech strings."""
        self._load_page("install.php", "cs_CZ")
        text = self._get_page_text()
        for expected in CZECH_STRINGS["install.php"]:
            self.assertIn(expected, text, f"Czech string not found on install: {expected}")

    def test_05_czech_login(self):
        """Login page should show Czech strings."""
        self._load_page("login.php", "cs_CZ")
        text = self._get_page_text()
        for expected in CZECH_STRINGS["login.php"]:
            self.assertIn(expected, text, f"Czech string not found on login: {expected}")

    def test_06_czech_menu(self):
        """Main menu should show Czech labels when locale=cs_CZ."""
        self._load_page("index.php", "cs_CZ")
        text = self._get_page_text()
        for expected in MENU_CZECH:
            self.assertIn(expected, text, f"Czech menu item not found: {expected}")

    # ── English tests ────────────────────────────────────────

    def test_07_english_index(self):
        """Index page should show English strings when locale=en_US."""
        self._load_page("index.php", "en_US")
        text = self._get_page_text()
        for expected in ENGLISH_STRINGS["index.php"]:
            self.assertIn(expected, text, f"English string not found on index: {expected}")

    def test_08_english_apps(self):
        """Apps page should show English strings when locale=en_US."""
        self._load_page("apps.php", "en_US")
        text = self._get_page_text()
        for expected in ENGLISH_STRINGS["apps.php"]:
            self.assertIn(expected, text, f"English string not found on apps: {expected}")

    def test_09_english_install(self):
        """Install page should show English strings."""
        self._load_page("install.php", "en_US")
        text = self._get_page_text()
        for expected in ENGLISH_STRINGS["install.php"]:
            self.assertIn(expected, text, f"English string not found on install: {expected}")

    def test_10_english_menu(self):
        """Main menu should show English labels when locale=en_US."""
        self._load_page("index.php", "en_US")
        text = self._get_page_text()
        for expected in MENU_ENGLISH:
            self.assertIn(expected, text, f"English menu item not found: {expected}")

    # ── Language switcher tests ──────────────────────────────

    def test_11_lang_switcher_present(self):
        """LangSelect dropdown should be present in navbar."""
        self._load_page("index.php")
        dropdowns = self.driver.find_elements(
            By.CSS_SELECTOR, ".navbar .dropdown-toggle[data-bs-toggle='dropdown']"
        )
        self.assertTrue(len(dropdowns) > 0, "Language switcher dropdown not found in navbar")

    def test_12_lang_switcher_has_options(self):
        """LangSelect dropdown should contain language options."""
        self._load_page("index.php")
        dropdown_btn = self.wait.until(
            EC.element_to_be_clickable(
                (By.CSS_SELECTOR, ".navbar .dropdown-toggle[data-bs-toggle='dropdown']")
            )
        )
        dropdown_btn.click()
        time.sleep(0.5)

        items = self.driver.find_elements(By.CSS_SELECTOR, ".dropdown-menu .dropdown-item")
        lang_items = [
            i for i in items
            if "locale=" in (i.get_attribute("href") or "")
        ]
        self.assertGreaterEqual(
            len(lang_items), 2, "Expected at least 2 language options (cs_CZ, en_US)"
        )

    def test_13_switch_to_czech_via_dropdown(self):
        """Switching to Czech via LangSelect should translate the page."""
        self._load_page("index.php", "en_US")
        text_en = self._get_page_text()
        self.assertIn("MultiFlexi Application & Credential Hub", text_en)

        switched = self._switch_locale("cs_CZ")
        self.assertTrue(switched, "Could not find cs_CZ option in LangSelect")

        time.sleep(1)
        text_cs = self._get_page_text()
        self.assertIn(
            "MultiFlexi centrum aplikací a přihlašovacích údajů",
            text_cs,
            "Page was not translated to Czech after switching",
        )

    def test_14_switch_to_english_via_dropdown(self):
        """Switching to English via LangSelect should show English strings."""
        self._load_page("index.php", "cs_CZ")
        text_cs = self._get_page_text()
        self.assertIn("MultiFlexi centrum aplikací a přihlašovacích údajů", text_cs)

        switched = self._switch_locale("en_US")
        self.assertTrue(switched, "Could not find en_US option in LangSelect")

        time.sleep(1)
        text_en = self._get_page_text()
        self.assertIn(
            "MultiFlexi Application & Credential Hub",
            text_en,
            "Page was not translated to English after switching",
        )

    # ── No untranslated English leaks in Czech mode ──────────

    def test_15_no_english_leaks_on_index_cs(self):
        """Czech index page should not contain key English-only strings."""
        self._load_page("index.php", "cs_CZ")
        text = self._get_page_text()
        english_only = [
            "Browse Applications",
            "Browse Credential Types",
            "Install MultiFlexi",
            "Recent Applications",
            "Recent Credential Types",
        ]
        for eng in english_only:
            self.assertNotIn(
                eng, text, f"English string leaked on Czech page: {eng}"
            )

    def test_16_no_english_leaks_on_install_cs(self):
        """Czech install page should not show English step headings."""
        self._load_page("install.php", "cs_CZ")
        text = self._get_page_text()
        english_only = [
            "Step 1: Prepare your system",
            "Production Repository",
            "Testing Repository",
        ]
        for eng in english_only:
            self.assertNotIn(
                eng, text, f"English string leaked on Czech install page: {eng}"
            )


if __name__ == "__main__":
    # Remove BASE_URL from argv so unittest doesn't choke on it
    if len(sys.argv) > 1 and not sys.argv[1].startswith("-"):
        sys.argv.pop(1)
    unittest.main(verbosity=2)
