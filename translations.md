# Translations

## Structure: messages.{locale}.yaml for each of 8 locales
de | en | ru | ko | ja | lt | uk | es

All keys must exist in all 8 files. Use `en` as fallback in translation.yaml.

---

## Complete key list (messages.de.yaml — source of truth)

```yaml
app:
  title: "Du bist nicht allein"
  description: "Hilfe bei Suizidgedanken — sofort, kostenlos, vertraulich"

nav:
  skip_to_content: "Zum Inhalt springen"

hero:
  eyebrow: "Du musst das nicht alleine tragen"
  headline: "Du musst nicht am absoluten Tiefpunkt sein.\nDu musst nichts erklären.\nDu bist hier — das reicht."
  subline: "Hier findest du einen Weg nach vorne. In deinem Tempo. Ohne Druck."

talk:
  button: "Jetzt sprechen"
  free: "Kostenlos"
  available_24h: "24h erreichbar"
  no_police: "Kein Polizeieinsatz"
  prefer_chat: "Lieber chatten oder mehr Optionen"
  crisis_button: "Hilfe jetzt"
  crisis_region_aria: "Notfallhilfe"
  call_aria: "Jetzt %name% anrufen"
  all_options_title: "Alle Wege zur Hilfe"

transparency:
  toggle: "Was passiert, wenn ich mich melde?"
  heading: "Damit du weißt, was dich erwartet"
  anonymous: "Du musst deinen Namen nicht nennen. Du musst gar nichts erklären."
  no_police_default: "Niemand ruft die Polizei — außer in sehr seltenen Fällen unmittelbarer Lebensgefahr, und nur wenn du dem zustimmst."
  what_happens: "Eine Person hört dir zu. Kein Skript. Kein Urteil. Nur ein Gespräch."
  confidential: "Alles, was du sagst, bleibt vertraulich."
  police_risk_note: "Hinweis: In manchen Ländern können Krisentelefone in bestimmten Situationen Notfalldienste alarmieren. Frage die Hotline bei Bedarf direkt danach."

plan:
  title: "Dein Sicherheitsplan"
  subtitle: "Alles hier bleibt auf deinem Gerät. Nichts wird gespeichert oder übertragen."
  step1_title: "Ein Brief für dich"
  step1_desc: "Trag deinen Vornamen ein — lass uns dir schreiben."
  name_placeholder: "Dein Vorname"
  name_aria: "Vorname eingeben für persönlichen Brief"
  generating: "Wird geschrieben…"
  letter_region_aria: "Persönlicher Brief"
  letter_transition: "Was ist eine Sache — vielleicht winzig — die sich je für einen Moment gut angefühlt hat?"
  step2_title: "Woran erkenne ich, dass es mir schlecht geht?"
  step2_desc: "Frühe Warnsignale, die dir sagen: Es ist Zeit, diesen Plan zu öffnen."
  warning_placeholder: "z.B. Ich isoliere mich von Menschen…"
  add_warning: "+ Warnsignal hinzufügen"
  step3_title: "Was hilft mir in diesem Moment?"
  coping_placeholder: "z.B. Eine kurze Runde draußen gehen…"
  add_coping: "+ Strategie hinzufügen"
  step4_title: "Wen kann ich anrufen?"
  step4_desc: "Menschen, denen du vertraust. Ihre Nummer — direkt hier."
  contact_name: "Name"
  contact_phone: "Telefonnummer"
  add_contact: "+ Kontakt hinzufügen"
  step5_title: "Meine Gründe"
  step5_desc: "Was hält dich hier. Klein oder groß — alles zählt."
  reason_placeholder: "z.B. Mein Hund. Das Meer. Neugier, was noch kommt."
  add_reason: "+ Grund hinzufügen"
  export_pdf: "Als PDF speichern"
  save_bookmark: "Seite als Lesezeichen speichern"
  bookmark_tip: "Tipp: Speichere dieses Lesezeichen. Dein Plan ist beim nächsten Besuch noch da."
  saved_indicator: "Gespeichert ✓"

followup:
  title: "Sollen wir kurz nach dir schauen?"
  desc: "Wir schicken dir nach 24 Stunden, 7 Tagen und einem Monat eine kurze Nachricht — mit einem Link zurück zu deinem Plan. Kein Account. Keine Werbung."
  email_placeholder: "deine@email.de"
  submit: "Ja, bitte melden"
  privacy_note: "Deine E-Mail wird verschlüsselt gespeichert und nach der letzten Nachricht unwiderruflich gelöscht."
  confirmed_title: "Wir melden uns."
  confirmed_body: "Du erhältst in den nächsten 30 Tagen drei kurze Nachrichten. Du kannst dich jederzeit abmelden."
  stopped_title: "Abgemeldet."
  stopped_body: "Du erhältst keine weiteren Nachrichten von uns."

resources:
  card_title: "Hilfe in deiner Nähe"
  card_desc: "Hotlines, Therapie und Unterstützung in über 20 Ländern"
  page_title: "Hilfsangebote"
  page_subtitle: "Finde Unterstützung in deinem Land"
  country_select_label: "Land auswählen"
  free: "Kostenlos"
  no_police: "Kein Polizeieinsatz"
  website: "Website"
  call_aria: "%name% anrufen"
  website_aria: "Website von %name% öffnen"
  type:
    hotline_phone: "Krisentelefon"
    hotline_text: "Krisen-SMS"
    hotline_chat: "Online-Chat"
    online_therapy: "Online-Therapie"
    peer_support: "Peer-Unterstützung"
    youth: "Für Jugendliche"
    lgbtq: "LGBTQ+"
    therapist_finder: "Therapeuten finden"
    self_help: "Selbsthilfe"

footer:
  no_data: "Kein Tracking. Keine Cookies. Keine Daten."

error:
  generic_title: "Etwas ist schiefgelaufen"
  generic_body: "Bitte versuche es noch einmal. Wenn du gerade Hilfe brauchst:"
  not_found_title: "Seite nicht gefunden"
  not_found_body: "Diese Seite existiert nicht — aber Hilfe ist nur einen Klick entfernt."

email:
  checkin_subject: "Kurzes Hallo von uns"
  checkin_body_intro: "Wir wollten kurz nachfragen, wie es dir geht."
  checkin_plan_link: "Dein Sicherheitsplan ist noch da, wenn du ihn brauchst."
  checkin_resources: "Du findest Hilfsangebote hier:"
  checkin_unsubscribe: "Keine weiteren Nachrichten: %link%"
```

---

## Translation config

```yaml
# config/packages/translation.yaml
framework:
    default_locale: de
    translator:
        default_path: '%kernel.project_dir%/translations'
        fallbacks: [en]
        paths:
            - '%kernel.project_dir%/translations'
```

## Locale routing

```yaml
# config/routes.yaml
controllers:
    resource: ../src/Controller/
    type: attribute
    prefix: /{_locale}
    requirements:
        _locale: de|en|ru|ko|ja|lt|uk|es
    defaults:
        _locale: de
```

## Notes for translators
- `%name%` placeholders in strings must be preserved exactly
- `%link%` in email strings is replaced with the unsubscribe URL
- Korean (ko) and Japanese (ja): prefer short, respectful phrasing — avoid clinical terms
- Russian/Ukrainian (ru/uk): use informal second person (ты/ти) for warmth
- The hero headline uses `\n` for line breaks — preserve these in all locales
