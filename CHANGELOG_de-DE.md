# 3.0.5

-   Einen TypeError im Verwaltungs-Dashboard behoben, der durch eine fehlende stateMachineState-Assoziation in den Bestellkriterien verursacht wurde und das Laden des Dashboards verhinderte, wenn Bestellungen aus wiederhergestellten Warenkörben vorhanden waren.

# 3.0.4

-   Inkompatibilität mit Shopware Commercial / B2B-Komponenten in Warenkorbabbrüchen behoben.

# 3.0.3
  
-   Konfigurationsoption hinzugefügt, um Erinnerungs-E-Mails für Gastkunden zu beschränken.

# 3.0.2
  
-   Null-Pointer-Exception beim Abrufen der customerGroupId durch Hinzufügen eines null-safe-Operators behoben.
-   Wissensdatenbank-Konfigurations-Button-Link hinzugefügt 

# 3.0.1

- Behoben: Ein fataler Fehler wurde behoben, der durch den Aufruf der undefinierten exec()-Methode der Doctrine\DBAL\Connection-Klasse verursacht wurde. Ersetzt durch die entsprechende executeStatement()-Methode, um die Kompatibilität mit der aktuellen Doctrine DBAL-Version zu gewährleisten.

# 3.0.0

-   Kompatibilität mit Shopware 6.7 hinzugefügt

# 2.0.7

-   E-Mail-Zustellungsfehler aufgrund einer ungültigen Empfängerdomäne behoben.

# 2.0.6

-   CC-E-Mail-Konfigurationsoption für E-Mails zu abgebrochenen Warenkörben hinzugefügt.

# 2.0.5

-   Das Problem, dass ein leerer Aktionscode zu einem Fehler führte, wurde behoben.

# 2.0.4

-   Das Problem, dass geplante Positionen nicht entfernt werden konnten, wurde behoben.

# 2.0.3

-  Abhängigkeit von AsMessageHandler hinzugefügt.

- # 2.0.2

-  Das Problem mit der geplanten Aufgabe wurde behoben.

# 2.0.1

-  Es wurde die Option hinzugefügt, Kundengruppen von der Aktion „Verlassener Warenkorb“ auszuschließen.

# 2.0.0

-   Kompatibilität für Shopware 6.6 hinzugefügt

# 1.5.10

-   Datum auf dem Dashboard für die Konvertierung von abgebrochenen Warenkörben hinzugefügt, um mehr Informationen zu erhalten.

# 1.5.9

-   Der Fehler, dass die Methode "getVariantFromOrderState" nicht im Dashboard deklariert ist, wurde behoben.

# 1.5.8

-   Das Problem des doppelten Eintrags '%s%s%s%s%s' für den Schlüssel individual_code_pattern wurde behoben

# 1.5.7

-   Standard-Werbeaktion für abgebrochene Warenkörbe hinzugefügt, die Kunden einen Rabatt gewährt, wenn sie ihren abgebrochenen Warenkorb wiederherstellen

# 1.5.6

-   Ersetzte CartPersister mit AbstractCartPersister für Redis-Kompatibilität

# 1.5.5

-   Bessere Konvertierung der Basis-E-Mail-Vorlage in niederländischen, deutschen und englischen Sprachumgebungen

# 1.5.4

-   Behobener Fehler im Mail-Template Absender null.

# 1.5.3

-   Kompatibilitätspatch mit Shopware 6.5.4

# 1.5.2

-   Warenkorbdaten und Gesamtpreis in der E-Mail-Vorlage hinzugefügt.

# 1.5.1

-   Behobener Fehler von getEmail null.

# 1.5.0

-   Einführung der Kompatibilität für Shopware 6.5.0

# 1.4.2

-   Die Anweisung !important wurde entfernt

# 1.4.1

-   Geringfügiger Patch im Cart Recovery Service

# 1.4.0

-   Refactor von CartService zur Unterstützung von Redis cart persister

# 1.3.13

-   Es wurde eine Konfiguration für die Größe des Benachrichtigungsstapels hinzugefügt, um eine Überlastung des Mailservers zu vermeiden, die zu fehlgeschlagenen Zustellungen führt.

# 1.3.12

-   Fehler behoben, der in seltenen Fällen beim Laden des Admin-Dashboards auftrat.

# 1.3.11

-   Metriken zur Umwandlung von abgebrochenen Warenkörben zum Verwaltungs-Dashboard hinzugefügt

# 1.3.10

-   Überarbeitung der JS-Dateien der Verwaltung unter v6.4.5.0 zur Behebung des Problems https://github.com/shopware/platform/issues/2420
-   Kundenvorschau-Daten hinzugefügt

# 1.3.9

-   Kompatibilität mit v6.4.10.0 hinzugefügt

# 1.3.8

-   Übersetzung der Kopf- und Fußzeilen von E-Mail-Vorlagen korrigiert

# 1.3.7

-   Kompatibilitäts-Patch für Plugins hinzugefügt, die darauf angewiesen sind, dass Erweiterungen im Warenkorb gesetzt sind.

# 1.3.6

-   Hotfix für Fehler bei der Auftragserstellung über API ohne Angabe eines Auftraggebers.

# 1.3.5

-   Wir löschen jetzt den verlassenen Warenkorb, wenn alle Positionen aus dem ursprünglichen Warenkorb gelöscht wurden.

# 1.3.4

-   Der verlassene Warenkorb wurde beständiger gemacht. Er wird nur gelöscht, wenn ein Warenkorb in eine Bestellung umgewandelt wird oder wenn die Löschfrist erreicht ist.
-   Es wurde eine Option hinzugefügt, mit der man sich aus dem Benutzerkonto im Schaufenster von Mails über verlassene Warenkörbe abmelden kann.
-   Es wurde ein zusätzliches Feld in die Konfiguration eingefügt, um die Fehlersuche zu erleichtern, wenn der Prozessor für verlassene Warenkörbe tatsächlich läuft.
-   Die Auflösung des verlassenen Warenkorbs wurde zu OrderSubscriber verschoben, um zu vermeiden, dass E-Mails an Personen verschickt werden, die bereits eine Bestellung durchgeführt haben, aber nie die Seite zum Abschluss der Bestellung erreichen.
-   Es wurden Vorlagedaten zur E-Mail-Vorlage hinzugefügt, um eine Vorschau der E-Mail zu ermöglichen und die verfügbaren Variablen zu durchsuchen.

# 1.3.3

-   Unterstützung für benutzerdefinierte Produkte hinzugefügt
-   Zusätzliche Touchpoints für die Aktualisierung von abgebrochenen Warenkörben hinzugefügt
-   Verbesserte UX in der Auflistung der abgebrochenen Warenkörbe in der Verwaltun

# 1.3.2

-   Planungsfunktionen mit benutzerdefinierten E-Mail-Vorlagen zum Mailing für abgebrochene Warenkörbe hinzugefügt

# 1.3.1

-   Problem mit der Dekoration in AbandonedCartService behoben

# 1.3.0

-   Plugin kompatibel zu Shopware Version 6.4.0 gemacht

# 1.2.9

-   Abwärtskompatibilität für die E-Mail-Planung erstellt. Diese Version bringt Planungsfunktionen für Shopware 6.3 ab 1.3.2

# 1.2.8

-   Fehler in der Timing-Funktion behoben, der dazu führte, dass verlassene Wagen niemals weggeworfen oder Erinnerungen nie gesendet wurden

# 1.2.7

-   Übersetzungsfehler in Mail-Vorlagen behoben

# 1.2.6

-   Fehlervermeidung zur geplanten Aufgabe hinzugefügt, um die Fortsetzung verlassener Warenkorb-Mails sicherzustellen

# 1.2.5

-   Verbesserte Entitätsdefinition

# 1.2.4

-   Problem behoben, bei dem Kopf- und Fußzeile von E-Mails nicht immer an die E-Mail angehängt wurden

# 1.2.3

-   Fehler im Modal für verlassene Warenkorbdetails behoben, der die Navigation zum Produkt, auf das verwiesen wird, behinderte

# 1.2.2

-   Verbesserte Erweiterbarkeit

# 1.2.1

-   Kompatibilitäts-Patch mit dem ZeobvReorder-Plugin

# 1.2.0

-   Kompatibilität der Backend-Bestellung hinzugefügt
-   Funktion zum Anzeigen von Artikeln im verlassenen Warenkorb hinzugefügt

# 1.1.1

-   v6.3.0.0 kompatibilitätsupdate

# 1.1.0

-   Anonyme Warenkorboption hinzugefügt. Über die Konfiguration ist es jetzt möglich, der verlassenen Warenkorbpost zu erlauben, auch die Kundendaten zum Auschecken wiederherzustellen, oder die Warenkorbwiederherstellung nur den Warenkorbinhalt wiederherzustellen.
-   Problem behoben, bei dem verlassene Warenkörbe bei Wiederherstellung oder Verfall nicht immer korrekt entfernt wurden.

# 1.0.4

-   Wiederhergestellte Warenkorbartikel können jetzt entfernt werden.

# 1.0.3

-   Gepatchtes Problem, bei dem nicht alle verlassenen Wagen verarbeitet würden, wenn ein Vertriebskanal keine verlassenen Wagen hätte.

# 1.0.2

-   Gepatchtes Problem, bei dem über den Link in der E-Mail des verlassenen Warenkorbs kein neuer Warenkorb erstellt wurde
-   Logo hinzugefügt

# 1.0.1

-   Kompatibilitätskorrekturen für Shopware 6.1.0-rc3

# 1.0.0

-   Erste Version des Zeo Abandoned Cart für Shopware 6
