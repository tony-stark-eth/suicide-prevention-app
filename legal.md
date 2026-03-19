# German Legal Requirements (DSGVO / TMG)

## What category this platform is in legally

This platform is an **information and referral service**, NOT a healthcare provider or medical device.
- It does not diagnose, treat, or prescribe
- It refers users to existing services
- The AI feature generates supportive text (like a self-help book), not clinical intervention
- This keeps it outside the DiGA (Digitale Gesundheitsanwendungen) framework
- Governing law: TMG §5 (Impressum), DSGVO/BDSG, TTDSG

---

## Impressum (§5 TMG) — required content

```
Angaben gemäß § 5 TMG

[e.V. Name — e.g. "Lebensgründe e.V."]
[Street address]
[PLZ Stadt]

Vertreten durch: [Vorstandsvorsitzender name]
Vereinsregister: [VR-Nummer] beim Amtsgericht [Stadt]
(add after registration is complete)

Kontakt:
E-Mail: kontakt@yourdomain.help

Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV:
[Full name]
[Address — same as above]

Haftungshinweis:
Diese Plattform stellt keine medizinische oder therapeutische Beratung dar und ersetzt keine professionelle Hilfe. In akuten Krisen wenden Sie sich bitte an die unter "Hilfe finden" aufgeführten Dienste oder den Notruf 112.
```

---

## Datenschutzerklärung — required sections

### 1. Verantwortlicher
Name + address of e.V., email. No DPO required (< 20 persons processing data automatically, no high-risk processing since no health data stored).

### 2. Welche Daten wir NICHT erheben
Explicitly state:
- Keine Nutzerkonten, keine Registrierung
- Kein Name, kein Standort, keine Gesundheitsdaten werden gespeichert
- Keine Cookies (außer bei Plausible — cookieless)
- Keine Profilerstellung, kein Tracking

### 3. Server-Logfiles
Hetzner logs IP addresses in standard access logs (legitimate interest, Art. 6(1)(f) DSGVO). These are deleted automatically after 7 days. We have a DPA (Auftragsverarbeitungsvertrag) with Hetzner. Link: https://www.hetzner.com/de/legal/privacy-policy/

### 4. Anthropic API (KI-Feature)
When a user enters a first name in the "Reasons to Stay" feature, this name is transmitted to Anthropic (USA) for text generation. Legal basis: Art. 6(1)(b) DSGVO (contract performance — the user is explicitly requesting this service) or alternatively Art. 49(1)(b) DSGVO (necessary for performance of a contract between user and controller). The name is not stored by us after the response. Anthropic processes it under their privacy policy and DPA: https://www.anthropic.com/legal/privacy.
Note: data transfer to USA — adequate protection under EU-US Data Privacy Framework.

### 5. Follow-up E-Mails (optional)
If a user voluntarily provides their email address for check-in messages:
- Legal basis: Art. 6(1)(a) DSGVO (explicit consent)
- The email is encrypted (AES-256) and stored temporarily
- It is deleted immediately after the last scheduled message (max. 30 days)
- No marketing, no third-party sharing
- Unsubscribe: link in every email, or write to: datenschutz@yourdomain.help

### 6. Analytics (Plausible)
We use Plausible Analytics (self-hosted on German servers, or plausible.io EU infrastructure). Plausible sets no cookies and collects no personal data. It is therefore exempt from TTDSG consent requirements. No IP addresses are stored. Privacy policy: https://plausible.io/privacy

### 7. Ihre Rechte (Art. 15–21 DSGVO)
- Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung
- Widerspruchsrecht, Datenübertragbarkeit
- Beschwerderecht bei der zuständigen Aufsichtsbehörde
  (Landesbeauftragte/r für Datenschutz NRW if based in NRW: https://www.ldi.nrw.de)

### 8. Kontakt Datenschutz
datenschutz@yourdomain.help

---

## Cookie/TTDSG compliance

Target: **zero cookies, no consent banner needed**.

Do NOT add:
- Google Analytics
- Google Tag Manager
- Facebook Pixel
- Any third-party scripts from external domains (except Plausible self-hosted)

If Plausible is self-hosted on your own domain → no external request → no TTDSG requirement.
If using plausible.io → their script is loaded from plausible.io → still cookieless → exempt from §25 TTDSG.

---

## Haftungsausschluss — display on all pages

Add to footer or Impressum page:
```
Diese Plattform bietet allgemeine Informationen und Verweise auf Krisenservices. Sie ersetzt keine professionelle medizinische oder psychiatrische Behandlung. In einer akuten Notlage rufen Sie bitte den Notruf 112 an oder wenden Sie sich an die Notaufnahme des nächsten Krankenhauses.
```

---

## e.V. registration checklist (do before launch)

- [ ] Write Vereinssatzung (model statute available at your local Amtsgericht)
- [ ] Convene founding assembly with min. 7 members — document in Gründungsprotokoll
- [ ] Have Notar certify founding documents (~€150)
- [ ] Submit to Vereinsregister at local Amtsgericht (~€75 fee)
- [ ] Apply for Gemeinnützigkeit at Finanzamt (submit Satzung + Gründungsprotokoll)
  - Relevant §52 AO purposes: "Förderung des öffentlichen Gesundheitswesens" and/or "Förderung der Hilfe für Personen, die auf Grund ihrer körperlichen, geistigen oder seelischen Condition auf die Hilfe anderer angewiesen sind"
- [ ] Receive preliminary Gemeinnützigkeitsbescheid (~6-12 weeks)
- [ ] Register with Google for Nonprofits using this Bescheid

---

## AI Act (EU) compliance note

The platform uses an AI system to generate supportive text. Under the EU AI Act:
- This is NOT a "high-risk AI system" (does not make decisions affecting individuals' rights, health, or safety in an automated way — it generates text that a human reads)
- The user explicitly initiates the request
- Output is clearly labeled as AI-generated
- A human (the user) makes all decisions based on the output

No special AI Act registration or conformity assessment required at this scope. Reassess if adding automated risk assessment or clinical decision support.
