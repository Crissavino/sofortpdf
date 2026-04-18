# sofortpdf.com — Paid Advertising Strategy

## 1. Business Context

| | |
|---|---|
| **Product** | Online PDF tools (merge, compress, convert, sign, etc.) |
| **Model** | Trial €0.69 (2 days) → €39.90/month subscription |
| **Market** | German-speaking (DACH: Germany, Austria, Switzerland) |
| **Target** | Professionals, freelancers, SMB owners, students — anyone who works with PDFs |
| **Differentiator** | EU servers, GDPR compliant, no installation, instant processing |
| **Current ads** | None — fresh start |

## 2. Competitive Landscape

### Direct Competitors
| Competitor | Est. Monthly Ad Spend | Primary Platforms | Key Messaging |
|---|---|---|---|
| iLovePDF | $200K+ | Google Search, Display | "Free PDF tools" |
| Smallpdf | $150K+ | Google Search, Meta | "Make PDF easy" |
| PDF24 | $50K+ | Google Search (DE) | "Kostenlose PDF-Tools" |
| Adobe Acrobat | $500K+ | Google, YouTube, Meta | "The original PDF" |

### Opportunity
- PDF24 dominates German organic but underspends on ads
- iLovePDF/Smallpdf focus on English markets — DACH is underserved
- None emphasize GDPR/EU-hosted as primary messaging (sofortpdf's edge)
- Long-tail German keywords are cheaper than English equivalents

## 3. Platform Selection

| Platform | Role | Budget % | Rationale |
|---|---|---|---|
| **Google Search** | Primary | 100% | High-intent queries — users actively need PDF tools NOW. Focus all budget here until optimized and profitable. |

### Future Platforms (after Google is profitable)
- **Meta**: Retargeting converters + lookalikes (B2C awareness)
- **LinkedIn**: B2B segment (accountants, lawyers, offices)
- **Microsoft/Bing**: Desktop B2B spillover (import from Google)
- **YouTube**: Product demo videos
- **TikTok**: Low priority for DACH PDF market

## 4. Campaign Architecture

### Google Ads
```
sofortpdf Google Ads
├── BRAND — Brand Defense
│   └── Keywords: sofortpdf, sofort pdf, sofortpdf.com
│
├── SEARCH_HIGH — High Intent Tools (60% of Google budget)
│   ├── Ad Group: PDF zusammenfügen / Merge PDF
│   ├── Ad Group: PDF komprimieren / Compress PDF
│   ├── Ad Group: PDF zu Word / PDF to Word
│   ├── Ad Group: PDF zu JPG / PDF to JPG
│   ├── Ad Group: Word zu PDF / Word to PDF
│   ├── Ad Group: PDF unterschreiben / Sign PDF
│   └── Ad Group: PDF trennen / Split PDF
│
├── SEARCH_MID — Problem-Aware (20% of Google budget)
│   ├── Ad Group: "PDF Dateien zusammenfügen online"
│   ├── Ad Group: "PDF verkleinern kostenlos"
│   └── Ad Group: "PDF bearbeiten ohne Software"
│
├── SEARCH_COMP — Competitor (10% of Google budget)
│   ├── Ad Group: "ilovepdf alternative"
│   ├── Ad Group: "smallpdf alternative deutsch"
│   └── Ad Group: "pdf24 alternative sicher"
│
└── PMAX — Performance Max (10% of Google budget)
    └── Asset group: all tools, all audiences
```

### Meta / LinkedIn / Bing — deferred until Google is profitable

## 5. Budget Plan

### Monthly Budget: €2,000 (recommended starting point)

| Platform | Monthly € | Daily € | Focus |
|---|---|---|---|
| Google Search | €2,000 | €67 | 100% on high-intent keywords |

### Expected Unit Economics
| Metric | Target | Rationale |
|---|---|---|
| Google CPC (DE) | €0.80-€1.50 | German PDF keywords less competitive than EN |
| Google CTR | 5-8% | Targeted exact/phrase match |
| Landing page CVR | 2-4% | Tool-specific landing = higher intent |
| Trial CPA | €15-€30 | CPC / CVR estimate |
| Trial → Subscription | 20-30% | Industry benchmark for low-ticket SaaS |
| Subscriber CPA | €50-€100 | Trial CPA / conversion rate |
| LTV (6 months avg) | €240 | €39.90 × 6 months retention |
| LTV:CAC | 2.4:1 - 4.8:1 | Healthy range |

### Pacing
| Period | Budget | Focus |
|---|---|---|
| Month 1-2 | €2,000/mo | Testing: keywords, ads, landing pages |
| Month 3-4 | €2,000/mo | Optimize: kill losers, scale winners |
| Month 5-6 | €3,000-5,000/mo | Scale: expand to more tools + evaluate other platforms |

## 6. Creative Strategy

### Google Search Ads — Copy Themes

**High Intent (tool-specific)**
```
Headline 1: PDF zusammenfügen — Sofort online
Headline 2: Ohne Installation · DSGVO-konform
Headline 3: Jetzt für 0,69 € testen
Description: PDF-Dateien zusammenfügen, komprimieren und konvertieren.
Server in der EU. Automatische Löschung. Sofort loslegen →
```

**Competitor Alternative**
```
Headline 1: Sichere iLovePDF-Alternative
Headline 2: Server in der EU · DSGVO-konform
Headline 3: Alle PDF-Tools ab 0,69 €
Description: Warum Ihre Daten außerhalb der EU verarbeiten lassen?
sofortpdf: Deutsche Qualität, alle Tools, ein Preis.
```

### Meta Ads — Creative Formats

| Priority | Format | Message | Audience |
|---|---|---|---|
| P1 | Static image + text | "PDF zusammenfügen in 3 Sekunden" | Retargeting |
| P2 | Carousel (5 tools) | "19 PDF-Tools. Ein Preis." | Lookalike |
| P3 | Video 15s (screen recording) | Tool demo: upload → convert → download | Awareness |
| P4 | Testimonial static | Quote from tax advisor/lawyer | Retargeting |

### Key Messages (USPs to emphasize)
1. **Security**: "Server in der EU · DSGVO-konform · Auto-Löschung"
2. **Speed**: "Sofort · Ohne Installation · Direkt im Browser"
3. **Value**: "19 PDF-Tools · Ein Preis · 0,69 € testen"
4. **Trust**: "10.000+ Nutzer · 256-Bit SSL"

## 7. Tracking (already implemented)

| Event | GTM Status | GA4 Conversion |
|---|---|---|
| `file_upload` | ✅ | No |
| `conversion_started` | ✅ | No |
| `payment_form_opened` | ✅ | No |
| `try_to_pay` | ✅ | No |
| `payment_success` | ✅ | **Yes — mark as conversion** |
| `purchase` | ✅ (with value) | **Yes — mark as conversion** |
| `conversion_complete` | ✅ | No |
| `file_download` | ✅ | No |

### Still needed for ads
- [ ] Google Ads conversion import from GA4 (purchase event)
- [ ] Meta Pixel + CAPI (deferred — when Meta campaigns launch)
- [ ] Microsoft UET tag (deferred — when Bing campaigns launch)

## 8. Implementation Roadmap

### Phase 1: Foundation (Week 1-2)
- [ ] Create Google Ads account
- [ ] Link GA4 → Google Ads
- [ ] Import `purchase` as conversion in Google Ads
- [ ] Build keyword lists (DE + EN for each tool)
- [ ] Write ad copy (3 RSAs per ad group)
- [ ] Set up conversion tracking verification

### Phase 2: Launch Google (Week 3-4)
- [ ] Launch Brand campaign (€5/day)
- [ ] Launch SEARCH_HIGH campaign (€25/day)
- [ ] Launch SEARCH_MID campaign (€8/day)
- [ ] Monitor daily, check search terms report
- [ ] Add negative keywords from search terms

### Phase 3: Optimize (Week 5-8)
- [ ] Kill underperforming keywords (3x CPA rule)
- [ ] Scale winning ad groups (+20% budget)
- [ ] A/B test landing pages (per-tool vs. generic)
- [ ] Expand to more tool-specific ad groups
- [ ] Monthly performance review

### Phase 4: Evaluate expansion (Week 9-12)
- [ ] If Google profitable → consider adding Meta retargeting
- [ ] If B2B conversions strong → consider LinkedIn
- [ ] If budget allows → import campaigns to Microsoft Bing

## 9. KPI Targets

| Metric | Month 1 | Month 3 | Month 6 |
|---|---|---|---|
| Trials | 50-80 | 100-150 | 200-300 |
| Trial CPA | €25-40 | €15-25 | €12-20 |
| Subscribers | 10-20 | 30-50 | 60-100 |
| Subscriber CPA | €100-150 | €60-80 | €50-70 |
| ROAS (trial) | 0.03:1 | 0.04:1 | 0.05:1 |
| LTV:CAC (6mo) | 1.6:1 | 3:1 | 4:1 |
| Monthly Revenue | €400-800 | €1,200-2,000 | €2,400-4,000 |
