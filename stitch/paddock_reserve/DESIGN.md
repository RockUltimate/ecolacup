# Design System Strategy: The Heritage Field

## 1. Overview & Creative North Star
The Creative North Star for this design system is **"The Modern Equestrian."** 

We are moving away from the cluttered, data-heavy aesthetic typical of betting sites. Instead, we are leaning into a high-end editorial experience that feels like a prestigious invitation to a country estate. This design system bridges the gap between the raw, organic power of horse racing and the refined precision of event registration. 

To break the "template" look, we utilize **Intentional Asymmetry**. Hero sections should feature overlapping elements—such as a large-scale `display-lg` headline partially obscured by a high-resolution, masked image of a horse in motion. We avoid rigid, centered grids in favor of layouts that breathe, using large amounts of `surface` space to frame our content like a gallery wall.

## 2. Colors: Tonal Depth vs. Structural Lines
Our palette is rooted in the earth. The interaction between the deep forest greens (`primary`) and the warm creams (`surface`) creates a sense of established luxury.

*   **The "No-Line" Rule:** In this design system, 1px solid borders for sectioning are strictly prohibited. Boundaries must be defined solely through background color shifts. For example, a registration form should not be "boxed" in; rather, it should sit on a `surface-container-low` section that transitions into a `surface-container-high` card.
*   **Surface Hierarchy & Nesting:** Treat the UI as a series of physical layers. A `surface-container-lowest` card should be placed on a `surface-container-low` section to create a soft, natural lift. This nesting conveys importance without visual noise.
*   **The "Glass & Gradient" Rule:** To avoid a flat, "out-of-the-box" feel, use Glassmorphism for floating elements (like navigation bars or sticky registration summaries). Use semi-transparent `surface` colors with a 12px–20px `backdrop-blur`.
*   **Signature Textures:** Apply subtle linear gradients to main CTAs, transitioning from `primary` (#173809) to `primary-container` (#2d4f1e) at a 135-degree angle. This provides a "tactile silk" finish that mimics the sheen of a racing silk.

## 3. Typography: The Editorial Authority
The typography pairing is a dialogue between tradition and modernity.

*   **Display & Headlines (Newsreader):** This serif typeface is our "Authoritative Voice." It should be used for storytelling, horse names, and event titles. The high contrast of `newsreader` in `display-lg` scale feels bespoke and editorial.
*   **Body, Titles, & Labels (Manrope):** Our functional workhorse. Use `manrope` for all registration inputs, data tables, and instructional text. It provides a clean, modern counterpoint to the heritage feel of the serif headlines.
*   **Hierarchy Strategy:** Always lead with `headline-lg` in `on-surface` to establish the theme, then support with `body-lg` in `on-surface-variant` for a sophisticated, lowered-contrast look that reduces eye strain.

## 4. Elevation & Depth: Tonal Layering
We reject the standard "drop shadow" approach in favor of **Ambient Light**.

*   **The Layering Principle:** Use the `surface-container` tiers to create depth. A horse’s profile card might use `surface-container-highest` to pop against a `surface` background.
*   **Ambient Shadows:** If a floating element (like a modal) requires a shadow, it must be extra-diffused. Use a blur of 40px with an 8% opacity. The shadow color should be a tinted version of `on-surface` (#1c1c19) rather than pure black, ensuring the shadow feels like a natural cast from the "cream" paper surface.
*   **The "Ghost Border":** If a boundary is required for accessibility in forms, use a "Ghost Border": the `outline-variant` token at 15% opacity. Never use a 100% opaque border.
*   **Organic Shapes:** Avoid perfect geometry. Use the `xl` (0.75rem) roundedness for most containers, but experiment with "Organic Clipping"—subtly asymmetrical border-radius values (e.g., top-left: 0.75rem, top-right: 2rem, bottom-right: 0.75rem, bottom-left: 0.75rem) to mimic the curves of nature.

## 5. Components

### Buttons
*   **Primary:** A gradient of `primary` to `primary-container`. Typography is `label-md` in `on-primary`, all caps with 0.05em letter spacing for an authoritative feel. Use `rounded-lg`.
*   **Secondary:** `surface-container-highest` background with `on-surface` text. No border.
*   **Tertiary:** Transparent background with `primary` text and a subtle 2px bottom stroke (using `surface-tint`) that only appears on hover.

### Input Fields
*   **Structure:** No background color—only a `surface-container-highest` bottom-border (2px). When focused, the border transitions to `primary`.
*   **Labels:** Use `label-md` in `on-surface-variant`. Error states use `error` color for both text and a subtle 1px bottom-border.

### Cards & Event Lists
*   **No Dividers:** Forbid the use of divider lines between list items. Instead, use 24px of vertical white space (from the Spacing Scale) or alternate background shifts between `surface` and `surface-container-low`.
*   **Horse Profile Cards:** Use `surface-container-lowest` with an `xl` corner radius. Use `newsreader` for the horse's name and `manrope` for the stats (Age, Weight, Jockey).

### Event Status Chips
*   **Action Chips:** Use `secondary-container` with `on-secondary-container` text. Use `rounded-full` for a pill shape that contrasts against the more structured rectangular cards.

## 6. Do’s and Don’ts

### Do:
*   **Do** use asymmetrical margins to create an editorial "magazine" layout.
*   **Do** lean into `surface-variant` for large background areas to provide warmth.
*   **Do** use `newsreader` for large pull-quotes or testimonial sections to add prestige.
*   **Do** ensure all interactive elements have a 44px minimum hit target, even if they appear visually smaller.

### Don't:
*   **Don't** use 1px solid borders to separate content sections.
*   **Don't** use pure black (#000000) for text; always use `on-surface` or `on-surface-variant` to maintain the natural, earthy tone.
*   **Don't** use aggressive, fast easing for transitions. Use "Slow & Graceful" easing (e.g., cubic-bezier(0.4, 0, 0.2, 1) over 400ms) to mirror the elegance of the sport.
*   **Don't** over-round everything. Keep `rounded-md` or `rounded-lg` for most elements; only use `rounded-full` for small utility elements like chips or badges.