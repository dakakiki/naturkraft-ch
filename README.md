# naturKRAFT – WordPress theme

One-pager for [naturkraft.ch](https://www.naturkraft.ch) («Aus gesunder Landschaft.»), built on the KurtFreyAG starter: ACF Pro flexible content, SCSS, esbuild, GSAP.

## Requirements

- WordPress 6.5+, PHP 8.0+ (Contact Form 7 6.2 itself needs PHP 8.3)
- Plugins: **Advanced Custom Fields Pro**, **Contact Form 7**
- Node 20+ for the build

## Build

```bash
npm install
npm run build   # dev build with source maps
npm run dev     # build + watch
npm run prod    # production build
```

Everything the theme loads comes from `dist/` (committed). Sources live in `assets/`.

## Structure

| Path | What |
| --- | --- |
| `acf-json/` | Field groups «Seiteninhalte» (sections) and «Theme Settings» – synced automatically |
| `template_parts/layouts/` | One template per ACF layout |
| `assets/scss/sections/` | One partial per layout, measurements from the XD file in the comments |
| `inc/contact-form.php` | CF7 integration: hints, privacy note, double-send protection |
| `inc/cf7/kontaktformular.html` | Reference markup of the CF7 form «naturKRAFT Anfrage» |
| `inc/newsletter.php` | Mailchimp double opt-in |

### Sections (ACF layouts)

| Layout | Anchor | XD frame |
| --- | --- | --- |
| `layout_hero` | `#start` | 01 Einstieg + ticker |
| `layout_text_images` | `#rohstoffe` | 02 Rohstoffe |
| `layout_image_card` | `#ansatz` | 03 Warum und wie |
| `layout_collaboration` | `#zusammenarbeit` | 04 Zusammenarbeit |
| `layout_text_cards` | `#ueber-naturkraft`, `#traegerschaft` | 05 Über naturKRAFT |
| `layout_contact` | `#kontakt` | 06 Kontakt |
| `layout_newsletter` | `#newsletter` | 07 Newsletter |

Call-to-action links carry the inquiry area (ACF field «Bereich im Kontaktformular»). Clicking one selects that area in the contact form and leaves anything already typed in place.

## Contact form

- CF7 form «naturKRAFT Anfrage». Its markup is in `inc/cf7/kontaktformular.html`; recipient, sender and subject are set under *Kontakt → Formulare → E-Mail*.
- Subject: `naturKRAFT | Anfrage | [bereich]`, Reply-To: the address the visitor entered.
- The area-specific hints and the privacy note are edited in *Theme Settings → Kontaktformular*.
- If the same inquiry is sent again within 10 minutes, the visitor sees the normal success message and no second e-mail goes out.
- Spam protection is not active yet; it is still to be decided with the client.

## Newsletter (Mailchimp)

Add to `wp-config.php` (never commit these):

```php
define( 'NK_MAILCHIMP_API_KEY', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-us21' );
define( 'NK_MAILCHIMP_AUDIENCE_ID', 'xxxxxxxxxx' );
```

The audience ID is under *Audience → Settings → Audience name and defaults*. New addresses are added as `pending`, so Mailchimp sends the opt-in confirmation itself. Set its texts in Mailchimp (*Audience → Signup forms → Form builder → Opt-in confirmation email*):

- Subject: «Ihre Anmeldung zum naturKRAFT-Newsletter»
- Text: «Bitte bestätigen Sie Ihre Anmeldung, um Neuigkeiten von naturKRAFT zu erhalten.»
- Button: «Anmeldung bestätigen»

Without the constants the form shows the error message and logs a note to the PHP error log.

## Privacy notes

- Fonts are self-hosted (Source Sans Pro, Latin subset). The emoji script and the s.w.org prefetch are removed, so no external requests are made on page load.
- Newsletter data is handled by Mailchimp (US provider). The privacy policy has to cover this.
