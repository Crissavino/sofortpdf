# sofortpdf — Google Ads Implementation Plan

## Setup Checklist

- [ ] Create Google Ads account (ads.google.com)
- [ ] Link GA4 (G-N0270M9XF9) → Google Ads
- [ ] Import `purchase` event as conversion (primary)
- [ ] Import `payment_success` as conversion (secondary)
- [ ] Set conversion window: 30 days click, 1 day view
- [ ] Set attribution: data-driven
- [ ] Location targeting: Germany, Austria, Switzerland — **"Presence" only** (not "Presence or Interest")
- [ ] Language: German + English
- [ ] Network: Search only (disable Display + Search Partners initially)

---

## Campaign 1: BRAND (€3/day)

**Goal:** Protect brand terms from competitors
**Bidding:** Maximize Clicks (cap CPC: €0.50)

### Ad Group: Brand Terms
| Match Type | Keyword |
|---|---|
| Exact | [sofortpdf] |
| Exact | [sofort pdf] |
| Exact | [sofortpdf.com] |
| Phrase | "sofortpdf" |

### RSA Ad Copy
```
Headlines (15):
H1: sofortpdf.com — Offizielle Website
H2: Alle PDF-Tools an einem Ort
H3: Jetzt für 0,69 € testen
H4: PDF zusammenfügen, komprimieren & mehr
H5: DSGVO-konform · Server in der EU
H6: Ohne Installation · Sofort loslegen
H7: 19 PDF-Tools · Ein Preis
H8: Professionelle PDF-Bearbeitung online
H9: 256-Bit SSL-Verschlüsselung
H10: Automatische Datei-Löschung
H11: sofortpdf — Schnell & Sicher
H12: PDF-Tools direkt im Browser
H13: Kostenlos testen · Jederzeit kündbar
H14: Vertrauen von 10.000+ Nutzern
H15: PDF bearbeiten in Sekunden

Descriptions (4):
D1: PDF zusammenfügen, komprimieren, konvertieren und unterschreiben — alles online, ohne Software. Server in der EU. DSGVO-konform. Jetzt testen →
D2: 19 professionelle PDF-Tools zum Zusammenfügen, Komprimieren, Konvertieren und mehr. Sicher, schnell und direkt im Browser. Ab 0,69 €.
D3: Ihre Daten bleiben in Europa. 256-Bit SSL-Verschlüsselung. Automatische Löschung nach der Verarbeitung. Sofort loslegen.
D4: PDF zu Word, Word zu PDF, JPG zu PDF und mehr. Keine Installation nötig. Professionelle Ergebnisse in Sekunden. Jetzt ausprobieren →
```

---

## Campaign 2: SEARCH_HIGH — High Intent Tools (€40/day)

**Goal:** Capture users searching for the top 3 PDF tools
**Bidding:** Maximize Conversions (switch to tCPA after 15+ conversions/month)
**Note:** Start with 3 ad groups only. Add more tools once these are profitable.

### Ad Group 1: PDF zu Word (PRIMARY — highest volume)
| Match Type | Keyword |
|---|---|
| Exact | [pdf zu word] |
| Exact | [pdf in word umwandeln] |
| Exact | [pdf zu word konvertieren] |
| Exact | [pdf zu word online] |
| Exact | [pdf zu docx] |
| Exact | [pdf to word] |
| Exact | [convert pdf to word] |
| Exact | [pdf in word umwandeln online kostenlos] |
| Phrase | "pdf zu word" |
| Phrase | "pdf in word" |

### Ad Group 2: PDF zusammenfügen (Merge — 2nd highest)
| Match Type | Keyword |
|---|---|
| Exact | [pdf zusammenfügen] |
| Exact | [pdf dateien zusammenfügen] |
| Exact | [pdf zusammenfügen online] |
| Exact | [pdfs zusammenfügen] |
| Exact | [mehrere pdf zusammenfügen] |
| Exact | [merge pdf] |
| Exact | [merge pdf online] |
| Exact | [combine pdf] |
| Phrase | "pdf zusammenfügen" |

### Ad Group 3: PDF komprimieren (Compress — 3rd)
| Match Type | Keyword |
|---|---|
| Exact | [pdf komprimieren] |
| Exact | [pdf verkleinern] |
| Exact | [pdf komprimieren online] |
| Exact | [pdf datei verkleinern] |
| Exact | [pdf größe reduzieren] |
| Exact | [compress pdf] |
| Exact | [reduce pdf size] |
| Phrase | "pdf komprimieren" |
| Phrase | "pdf verkleinern" |

### Future ad groups (add when top 3 are profitable)
- PDF zu JPG / JPG zu PDF
- Word zu PDF
- PDF unterschreiben (Sign)
- PDF trennen (Split)
- PDF zu Excel / Excel zu PDF

### RSA Ad Copy per Ad Group

**Template (customize H1 per tool):**
```
Headlines (15):
H1: {Tool} — Sofort online ← PIN to Position 1
H2: Ohne Installation · Sofort loslegen
H3: Jetzt für 0,69 € testen
H4: DSGVO-konform · Server in der EU
H5: {Tool} in Sekunden
H6: 256-Bit SSL · Sicher & schnell
H7: Professionelle Ergebnisse
H8: Keine Software nötig
H9: {Tool} direkt im Browser
H10: Automatische Datei-Löschung
H11: Alle PDF-Tools in einem Ort
H12: 19 Tools · Ein Preis
H13: Jederzeit kündbar
H14: 10.000+ zufriedene Nutzer
H15: Kostenlos ausprobieren →

Descriptions (4):
D1: {Tool} — schnell, sicher und ohne Software. DSGVO-konform, Server in der EU. Automatische Löschung. Jetzt für nur 0,69 € testen →
D2: Professionelle PDF-Bearbeitung direkt im Browser. {Tool} und 18 weitere PDF-Tools in einem Abo. Sofort loslegen.
D3: Ihre Daten bleiben in Europa. 256-Bit Verschlüsselung. Keine Installation. {Tool} in wenigen Sekunden erledigt.
D4: Warum kompliziert? {Tool} online — einfach Datei hochladen, verarbeiten, herunterladen. Sicher und DSGVO-konform.
```

**H1 per Ad Group:**
| Ad Group | H1 (pinned) |
|---|---|
| PDF zu Word | PDF zu Word — Sofort konvertieren |
| PDF zusammenfügen | PDF zusammenfügen — Sofort online |
| PDF komprimieren | PDF komprimieren — Sofort & sicher |

---

## Campaign 3: SEARCH_MID — Problem-Aware (€13/day)

**Goal:** Capture problem-aware searches
**Bidding:** Maximize Clicks (cap CPC: €1.50)

### Ad Group 1: Online PDF Tools
| Match Type | Keyword |
|---|---|
| Phrase | "pdf bearbeiten online" |
| Phrase | "pdf tools online" |
| Phrase | "pdf online bearbeiten" |
| Exact | [pdf editor online] |
| Exact | [pdf bearbeiten ohne software] |

### Ad Group 2: PDF konvertieren allgemein
| Match Type | Keyword |
|---|---|
| Phrase | "pdf konvertieren" |
| Phrase | "pdf umwandeln" |
| Phrase | "datei in pdf umwandeln" |
| Exact | [pdf konvertieren online] |
| Exact | [pdf converter deutsch] |

### Ad Group 3: PDF sicher bearbeiten
| Match Type | Keyword |
|---|---|
| Phrase | "pdf bearbeiten sicher" |
| Phrase | "pdf tool dsgvo" |
| Exact | [sichere pdf bearbeitung] |
| Exact | [pdf tool server deutschland] |

---

## Campaign 4: SEARCH_COMP — Competitors (€7/day)

**Goal:** Capture users comparing alternatives
**Bidding:** Maximize Clicks (cap CPC: €1.00)

### Ad Group 1: iLovePDF Alternative
| Match Type | Keyword |
|---|---|
| Exact | [ilovepdf alternative] |
| Exact | [ilovepdf alternative deutsch] |
| Exact | [ilovepdf sicher] |
| Phrase | "ilovepdf alternative" |

### Ad Group 2: Smallpdf Alternative
| Match Type | Keyword |
|---|---|
| Exact | [smallpdf alternative] |
| Exact | [smallpdf alternative kostenlos] |
| Phrase | "smallpdf alternative" |

### Ad Group 3: PDF24 Alternative
| Match Type | Keyword |
|---|---|
| Exact | [pdf24 alternative] |
| Exact | [pdf24 alternative online] |
| Phrase | "pdf24 alternative" |

### Ad Group 4: Adobe Alternative
| Match Type | Keyword |
|---|---|
| Exact | [adobe acrobat alternative] |
| Exact | [adobe pdf alternative günstig] |
| Phrase | "acrobat alternative" |

### RSA Ad Copy (Competitor campaigns)
```
Headlines (15):
H1: Sichere PDF-Alternative
H2: Server in der EU · DSGVO-konform
H3: Alle PDF-Tools ab 0,69 €
H4: Warum Daten ins Ausland senden?
H5: 19 Tools · Deutsche Qualität
H6: Ohne Installation · Sofort starten
H7: 256-Bit SSL-Verschlüsselung
H8: Automatische Datei-Löschung
H9: Günstiger als Adobe Acrobat
H10: PDF-Tools mit EU-Datenschutz
H11: Jetzt testen · Jederzeit kündbar
H12: Alle Formate unterstützt
H13: Professionell & erschwinglich
H14: Die sichere Alternative
H15: 10.000+ Nutzer vertrauen uns

Descriptions (4):
D1: Warum Ihre sensiblen Dokumente außerhalb der EU verarbeiten? sofortpdf: Alle PDF-Tools, Server in Deutschland, DSGVO-konform. Ab 0,69 €.
D2: PDF zusammenfügen, komprimieren, konvertieren — sicher und schnell. Die europäische Alternative zu internationalen PDF-Diensten.
D3: 19 professionelle PDF-Tools zum fairen Preis. Keine versteckten Kosten. Server in der EU. Automatische Löschung. Jetzt testen →
D4: Wechseln Sie zu sofortpdf: Schneller, sicherer, günstiger. Alle PDF-Tools in einem Abo. DSGVO-konform. Kostenlos ausprobieren.
```

---

## Campaign 5: PMAX — Performance Max (€3/day)

**Goal:** Discover new audiences via Google AI
**Bidding:** Maximize Conversions

### Asset Group: All Tools
```
Final URL: https://sofortpdf.com/de
Headlines (5): same as Brand campaign top 5
Long headlines (5):
- PDF-Dateien bearbeiten — schnell, sicher und DSGVO-konform
- 19 professionelle PDF-Tools direkt im Browser nutzen
- PDF zusammenfügen, komprimieren und konvertieren — sofort online
- Die sichere Alternative für Ihre PDF-Bearbeitung
- Alle PDF-Tools an einem Ort — ab nur 0,69 € testen

Descriptions (5):
- same as Brand campaign descriptions 1-4 + one extra:
- Professionelle PDF-Bearbeitung ohne Installation. DSGVO-konform, EU-Server, 256-Bit SSL. 19 Tools, ein Preis. Jetzt loslegen →

Images: upload logo + tool screenshots
Audience signals: Custom intent (pdf zusammenfügen, pdf komprimieren, etc.)
```

### PMax Settings
- Brand exclusions: ON (exclude "sofortpdf" from PMax)
- URL expansion: OFF (only use specified URLs)
- Exclude existing customers: upload customer email list

---

## Negative Keywords (Shared List — Account Level)

### List: "Informational & Free Intent"
```
[kostenlos]
[gratis]
[free]
[freeware]
[open source]
"was ist"
"wie funktioniert"
[tutorial]
[anleitung]
[download programm]
[software download]
[offline]
[desktop app]
```

### List: "Job Seekers"
```
[job]
[jobs]
[karriere]
[gehalt]
[stellenangebote]
[bewerbung]
"arbeiten bei"
[vacancy]
```

### List: "Irrelevant"
```
[api]
[developer]
[programmieren]
[sdk]
[github]
[code]
[library]
[crack]
[keygen]
[torrent]
[pirate]
```

---

## Ad Extensions (Account Level)

### Sitelinks (8)
| Sitelink | URL | Description |
|---|---|---|
| PDF zusammenfügen | /de/pdf-zusammenfuegen | PDFs schnell und sicher zusammenfügen |
| PDF komprimieren | /de/pdf-komprimieren | PDF-Dateien verkleinern für E-Mail |
| PDF zu Word | /de/pdf-zu-word | PDF in bearbeitbares Word umwandeln |
| PDF unterschreiben | /de/pdf-unterzeichnen | PDF digital signieren — sicher |
| Alle Tools ansehen | /de#tools | 19 professionelle PDF-Tools |
| Preise | /de#pricing | Ab 0,69 € testen · 39,90 €/Monat |
| Datensicherheit | /de/datenschutz | DSGVO-konform · EU-Server |
| Kontakt | /de/kontakt | Fragen? Wir helfen gerne |

### Callouts (6)
- DSGVO-konform
- Server in der EU
- 256-Bit SSL
- Ohne Installation
- 19 PDF-Tools
- Automatische Löschung

### Structured Snippets
- **Typen:** PDF zusammenfügen, PDF komprimieren, PDF zu Word, PDF unterschreiben, PDF trennen, JPG zu PDF

---

## Landing Page Strategy

Each high-intent ad group should link to its **specific tool page** (not the homepage):

| Ad Group | Landing Page |
|---|---|
| PDF zu Word | sofortpdf.com/de/pdf-zu-word |
| PDF zusammenfügen | sofortpdf.com/de/pdf-zusammenfuegen |
| PDF komprimieren | sofortpdf.com/de/pdf-komprimieren |
| Competitor / Brand / PMAX | sofortpdf.com/de (homepage) |

---

## Weekly Optimization Checklist

- [ ] Review Search Terms Report → add negatives
- [ ] Check conversion tracking is firing
- [ ] Kill keywords with spend > 3x target CPA and 0 conversions
- [ ] Pause ads with CTR < 2%
- [ ] Scale ad groups with CPA < target by +20% budget
- [ ] Check Quality Scores → improve ad relevance for QS < 5
- [ ] Review Auction Insights → monitor competitor activity
