# Mail-Konto-Provisionierung für Nextcloud / Mail Account Provisioning for Nextcloud

[Deutsch](#deutsch) | [English](#english)

## Deutsch

Diese Nextcloud-App ermöglicht es Administratoren, E-Mail-Konten für Benutzer und Gruppen zu provisionieren.

### Funktionen

- Automatische Erstellung von E-Mail-Konten für Benutzer und Gruppen
- Verwaltung von IMAP- und SMTP-Einstellungen
- Dynamische Synchronisierung von Gruppenänderungen
- Benutzerfreundliche Administrationsoberfläche

### Installation

1. Platzieren Sie diese App im `apps`-Verzeichnis Ihrer Nextcloud-Installation.
2. Navigieren Sie in das App-Verzeichnis und führen Sie `composer install --no-dev` aus, um die Abhängigkeiten zu installieren.
3. Aktivieren Sie die App über das Nextcloud-Administrationsmenü.

### Verwendung

1. Navigieren Sie zum Administrationsbereich in Nextcloud.
2. Wählen Sie "Mail-Konto-Provisionierung" aus der Seitenleiste.
3. Klicken Sie auf "Neues Konto hinzufügen", um ein neues E-Mail-Konto zu konfigurieren.
4. Füllen Sie die erforderlichen Felder aus (E-Mail, Benutzername, Passwort, IMAP/SMTP-Server, etc.).
5. Weisen Sie das Konto Benutzern oder Gruppen zu.
6. Speichern Sie die Konfiguration.

Die App wird automatisch die E-Mail-Konten für die zugewiesenen Benutzer erstellen und bei Gruppenänderungen aktualisieren.

### Entwicklung

Für die Entwicklung führen Sie `composer install` aus, um auch die Entwicklungsabhängigkeiten einzuschließen.

### Lizenz

Diese App ist unter der AGPL-Lizenz veröffentlicht.

---

## English

This Nextcloud app allows administrators to provision email accounts for users and groups.

### Features

- Automatic creation of email accounts for users and groups
- Management of IMAP and SMTP settings
- Dynamic synchronization of group changes
- User-friendly administration interface

### Installation

1. Place this app in the `apps` directory of your Nextcloud installation.
2. Navigate to the app directory and run `composer install --no-dev` to install dependencies.
3. Enable the app through the Nextcloud admin panel.

### Usage

1. Navigate to the administration area in Nextcloud.
2. Select "Mail Account Provisioning" from the sidebar.
3. Click "Add New Account" to configure a new email account.
4. Fill in the required fields (email, username, password, IMAP/SMTP servers, etc.).
5. Assign the account to users or groups.
6. Save the configuration.

The app will automatically create email accounts for the assigned users and update them when group changes occur.

### Development

For development, run `composer install` to include dev dependencies.

### License

This app is published under the AGPL License.