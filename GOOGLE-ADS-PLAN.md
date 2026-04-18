# sofortpdf — Google Ads Implementation Plan

> Validated with Semrush DE data (April 2026)

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

## Budget Overview

| Campaign | Daily € | Monthly € | % |
|---|---|---|---|
| BRAND | €3 | €90 | 5% |
| SEARCH_HIGH (3 tools) | €40 | €1,200 | 60% |
| SEARCH_MID (problem-aware) | €13 | €400 | 20% |
| SEARCH_COMP (competitors) | €7 | €210 | 10% |
| PMAX | €10 | €100 | 5% |
| **Total** | **€73** | **€2,000** | **100%** |

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
H13: Jetzt testen · Jederzeit kündbar
H14: Tausende zufriedene Nutzer
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
**Bidding:** Maximize Clicks (cap CPC: €1.50) for first 2-3 weeks → switch to Maximize Conversions after 15+ conversions → then tCPA
**Note:** Start with 3 ad groups only. Add more tools once these are profitable.

### Semrush volume validation (DE)
| Tool | Monthly searches (DE) | CPC range |
|---|---|---|
| PDF zusammenfügen | 368,000 | $0.13-$0.50 |
| PDF komprimieren | 110,000 | $0.20-$0.60 |
| PDF zu Word | 86,000 (combined variants) | $0.30-$1.04 |

### Ad Group 1: PDF zusammenfügen (Merge — HIGHEST volume: 368K/mo)
| Match Type | Keyword |
|---|---|
| Exact | [pdf zusammenfügen] |
| Exact | [pdf dateien zusammenfügen] |
| Exact | [pdf zusammenfügen online] |
| Exact | [pdfs zusammenfügen] |
| Exact | [mehrere pdf zusammenfügen] |
| Exact | [pdf zusammenfügen online kostenlos] |
| Exact | [merge pdf] |
| Exact | [merge pdf online] |
| Exact | [combine pdf] |
| Phrase | "pdf zusammenfügen" |

### Ad Group 2: PDF komprimieren (Compress — 110K/mo)
| Match Type | Keyword |
|---|---|
| Exact | [pdf komprimieren] |
| Exact | [pdf verkleinern] |
| Exact | [pdf komprimieren online] |
| Exact | [pdf datei verkleinern] |
| Exact | [pdf größe reduzieren] |
| Exact | [pdf komprimieren online kostenlos] |
| Exact | [compress pdf] |
| Exact | [reduce pdf size] |
| Phrase | "pdf komprimieren" |
| Phrase | "pdf verkleinern" |

### Ad Group 3: PDF zu Word (86K/mo combined)
| Match Type | Keyword |
|---|---|
| Exact | [pdf zu word] |
| Exact | [pdf in word umwandeln] |
| Exact | [pdf zu word konvertieren] |
| Exact | [pdf zu word online] |
| Exact | [pdf zu docx] |
| Exact | [pdf in word umwandeln online kostenlos] |
| Exact | [pdf to word] |
| Exact | [convert pdf to word] |
| Phrase | "pdf zu word" |
| Phrase | "pdf in word" |

### Phase 2 expansion (by volume, add when top 3 profitable)
| Tool | Monthly DE | Priority |
|---|---|---|
| PDF trennen / split | 22,200 | 1st |
| PDF umwandeln (generic convert) | 18,100 | 2nd |
| Word zu PDF | 12,100 | 3rd |
| PDF zu JPG | 9,900 | 4th |
| PDF unterschreiben (sign) | 8,100 | 5th (high CPC = strong commercial intent) |

### RSA Ad Copy per Ad Group

**Template (customize H1 per tool):**
```
Headlines (15):
H1: {Tool} — Sofort online ← PIN to Position 1
H2: Ohne Installation · Sofort loslegen
H3: Jetzt für nur 0,69 € testen
H4: DSGVO-konform · Server in der EU
H5: {Tool} in Sekunden
H6: 256-Bit SSL · Sicher & schnell
H7: Professionelle Ergebnisse
H8: Keine Software nötig
H9: {Tool} direkt im Browser
H10: Automatische Datei-Löschung
H11: Alle PDF-Tools an einem Ort
H12: 19 Tools · Ein Preis
H13: Jederzeit kündbar
H14: Schnell, sicher & einfach
H15: Jetzt ausprobieren →

Descriptions (4):
D1: {Tool} — schnell, sicher und ohne Software. DSGVO-konform, Server in der EU. Automatische Löschung. Jetzt für nur 0,69 € testen →
D2: Professionelle PDF-Bearbeitung direkt im Browser. {Tool} und 18 weitere PDF-Tools in einem Abo. Sofort loslegen.
D3: Ihre Daten bleiben in Europa. 256-Bit Verschlüsselung. Keine Installation. {Tool} in wenigen Sekunden erledigt.
D4: Warum kompliziert? {Tool} online — einfach Datei hochladen, verarbeiten, herunterladen. Sicher und DSGVO-konform.
```

**H1 per Ad Group:**
| Ad Group | H1 (pinned) |
|---|---|
| PDF zusammenfügen | PDF zusammenfügen — Sofort online |
| PDF komprimieren | PDF komprimieren — Sofort & sicher |
| PDF zu Word | PDF zu Word — Sofort konvertieren |

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
H1: Die sichere PDF-Alternative
H2: Server in der EU · DSGVO-konform
H3: Alle PDF-Tools ab 0,69 €
H4: Warum Daten ins Ausland senden?
H5: 19 Tools · Europäische Qualität
H6: Ohne Installation · Sofort starten
H7: 256-Bit SSL-Verschlüsselung
H8: Automatische Datei-Löschung
H9: Günstiger als Adobe Acrobat
H10: PDF-Tools mit EU-Datenschutz
H11: Jetzt testen · Jederzeit kündbar
H12: Alle Formate unterstützt
H13: Professionell & erschwinglich
H14: Ihre Daten bleiben in Europa
H15: Jetzt wechseln & sparen

Descriptions (4):
D1: Warum Ihre sensiblen Dokumente außerhalb der EU verarbeiten? sofortpdf: Alle PDF-Tools, Server in der EU, DSGVO-konform. Ab 0,69 €.
D2: PDF zusammenfügen, komprimieren, konvertieren — sicher und schnell. Die europäische Alternative zu internationalen PDF-Diensten.
D3: 19 professionelle PDF-Tools zum fairen Preis. Keine versteckten Kosten. Server in der EU. Automatische Löschung. Jetzt testen →
D4: Wechseln Sie zu sofortpdf: Schneller, sicherer, günstiger. Alle PDF-Tools in einem Abo. DSGVO-konform. Jetzt ausprobieren.
```

---

## Campaign 5: PMAX — Performance Max (€10/day)

**Goal:** Discover new audiences via Google AI
**Bidding:** Maximize Conversions
**Note:** €10/day minimum for PMax to have enough data for the algorithm to learn.

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
Audience signals: Custom intent (pdf zusammenfügen, pdf komprimieren, pdf zu word)
```

### PMax Settings
- Brand exclusions: ON (exclude "sofortpdf" from PMax)
- URL expansion: OFF (only use specified URLs)
- Exclude existing customers: upload customer email list

---

## Negative Keywords (Shared Lists — Account Level)

### List: "Informational"
```
"was ist"
"wie funktioniert"
[tutorial]
[anleitung]
[download programm]
[software download]
[offline]
[desktop app]
[lernen]
[kurs]
```

### List: "Free — TEST as separate campaign first"
> ⚠️ "kostenlos" has massive volume in DE. With a €0.69 trial, we CAN
> capture this traffic. Start by ALLOWING it in SEARCH_HIGH (keywords
> like [pdf zusammenfügen online kostenlos] are included). Monitor CPA.
> If CPA is too high after 2 weeks, move "kostenlos" keywords to their
> own ad group with lower bids, or add as negative.
```
[gratis]
[free]
[freeware]
[open source]
[crack]
[keygen]
[torrent]
[pirate]
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

### List: "Developer/Technical"
```
[api]
[developer]
[programmieren]
[sdk]
[github]
[code]
[library]
[npm]
[python]
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

Each high-intent ad group links to its **specific tool page**:

| Ad Group | Landing Page |
|---|---|
| PDF zusammenfügen | sofortpdf.com/de/pdf-zusammenfuegen |
| PDF komprimieren | sofortpdf.com/de/pdf-komprimieren |
| PDF zu Word | sofortpdf.com/de/pdf-zu-word |
| Competitor / Brand / PMAX | sofortpdf.com/de (homepage) |

---

## Bidding Strategy Progression

| Phase | When | Strategy | Rationale |
|---|---|---|---|
| 1. Learning | Weeks 1-3 | Maximize Clicks (cap CPC €1.50) | No conversion data yet — gather clicks + conversions |
| 2. Optimize | After 15+ conv/month | Maximize Conversions | Enough data for Smart Bidding to learn |
| 3. Efficiency | After 30+ conv/month | Target CPA | Set CPA target based on Month 1-2 actual data |

---

## Expected Unit Economics (Semrush-validated)

| Metric | Estimate | Source |
|---|---|---|
| Avg CPC (DE) | €0.15-€1.00 | Semrush: $0.13-$1.04 |
| CTR (Search) | 5-8% | Industry benchmark |
| Landing page CVR | 2-4% | Tool-specific landing = higher intent |
| Trial CPA | €8-€25 | Based on CPC range |
| Trial → Subscription | 20-30% | Low-ticket SaaS benchmark |
| Subscriber CPA | €30-€80 | Trial CPA / conversion rate |
| LTV (6 months) | €240 | €39.90 × 6 months |
| LTV:CAC | 3:1 - 8:1 | Very healthy |

---

## Weekly Optimization Checklist

- [ ] Review Search Terms Report → add negatives (especially "kostenlos" performance)
- [ ] Check conversion tracking is firing correctly
- [ ] Kill keywords with spend > 3x target CPA and 0 conversions
- [ ] Pause ads with CTR < 2%
- [ ] Scale ad groups with CPA < target by +20% budget
- [ ] Check Quality Scores → improve ad relevance for QS < 5
- [ ] Review Auction Insights → monitor competitor activity
- [ ] Monitor "kostenlos" keywords separately — are they converting?
