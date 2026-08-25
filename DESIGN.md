# DESIGN.md - Duain Fragrances Web Experience

## 1. Brand Philosophy & Aesthetic
The core objective is to deliver a sensory, high-end digital experience that mirrors the luxury of Duain Fragrances. 
*   **Visual Direction:** We utilize a modern, minimalist, and dark-themed aesthetic across all storefront and account touchpoints to create a sense of premium exclusivity.
*   **Design Pillars:** Negative space, sharp contrast, fluid motion, and editorial elegance. Every UI element must feel deliberate and refined.

## 2. Color System
Our palette relies on high contrast, utilizing a deep dark canvas accented by metallic gold illumination and crisp white typography for maximum visual impact and legibility.
*   **Deep Onyx (Base Canvas):** `#0A0A0A` to `#000000`. Primary background for maximum depth.
*   **Elevated Charcoal (Surface & Panels):** `#121212` / `#0D0D0D`. Applied to form containers, cards, and split-screen panels.
*   **Signature Gold (Primary Action & Branding):** `#D4AF37` / `#C5A059`. Reserved for primary buttons, active UI states, key brand graphics, and fine linework.
*   **Radiant Gold (Highlight & Glow):** `#E8D08D`. Applied to focus rings, hover states, and subtle drop-shadow glows.
*   **Pure White (High-Contrast Text):** `#FFFFFF`. Applied to primary headings, form field labels, and critical text elements against dark backgrounds.
*   **Warm Cream (Contrast Canvas):** `#F4EFE6`. Used for inverted storytelling sections (e.g., ingredient history) to create a rhythmic scroll experience.

## 3. Typography & Hierarchy
The typographic pairing balances classic editorial elegance with crisp digital legibility.
*   **Display / Headings:** *Playfair Display* or *Cinzel*. High-contrast, elegant serifs. Reserved for hero statements, brand logotypes, product titles, and welcome headings.
*   **Interface / Body:** *Montserrat* or *Lato*. Clean, geometric sans-serif. Applied with wide tracking (letter-spacing) to maintain a highly breathable interface.
*   **Form Controls:** Uppercase sans-serif in pure white (`#FFFFFF`) with wide letter-spacing (`tracking-wider`, `text-xs`) for sharp field identification.

## 4. UI/UX & Layout Architecture
Interactions must feel expensive—fluid, tactile, and visually balanced across screen sizes.
*   **Split-Screen Authentication (`/login`):** Shared customer and admin authentication pages utilize a 50/50 desktop grid (`grid-cols-1 lg:grid-cols-2 min-h-screen`).
    *   *Left Panel (Brand Hero):* Dedicated dark matte canvas featuring the gold perfume bottle logomark and serif brand name.
    *   *Right Panel (Interactive Form):* Elevated dark section with centered white headings, crisp dark input boxes (`bg-neutral-800`), and a solid gold action button.
*   **The Hero Experience:** Avoid static hero images. Utilize a WebGL render or a seamless, high-resolution slow-motion video loop of the bottle.
*   **Navigation & Framing:** Employ a transparent header with ultra-thin line icons and an off-canvas menu to maximize visual real estate.

## 5. Micro-interactions & Focus States
*   **Input Controls:** Active form fields utilize a Signature Gold border and subtle ring glow (`focus:border-[#D4AF37] focus:ring-[#D4AF37]`).
*   **Primary Action Buttons:** Solid gold buttons (`bg-[#D4AF37] text-black font-bold`) feature smooth 60fps hover transitions (`hover:bg-[#C5A059]`) and subtle gold ambient glow.
*   **Cursor & Hovers:** Custom gold-dot cursor expanding into a translucent ring over clickable elements.