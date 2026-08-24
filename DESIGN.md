# DESIGN.md - Duain Fragrances Web Experience

## 1. Brand Philosophy & Aesthetic
The core objective is to deliver a sensory, high-end digital experience that mirrors the luxury of Duain Fragrances. 
*   **Visual Direction:** We are utilizing a modern, minimalist, and dark-themed aesthetic to create a sense of premium exclusivity.
*   **Design Pillars:** Negative space, sharp contrast, and fluid motion. Every UI element must feel deliberate and refined.

## 2. Color System
Our palette relies on high contrast, using a vast dark canvas to make the brand's metallic accents illuminate the screen.
*   **Deep Onyx (Base Canvas):** `#0A0A0A` to `#000000`. The primary background for maximum depth.
*   **Signature Gold (Primary Action):** `#C5A059`. Used strictly for primary buttons, active UI states, and fine linework.
*   **Radiant Gold (Highlight/Glow):** `#E8D08D`. Applied to hover states, text highlights, and subtle drop-shadow glows.
*   **Warm Cream (Contrast Canvas):** `#F4EFE6`. Used for inverted storytelling sections (e.g., ingredient history) to create a rhythmic scroll experience.

## 3. Typography & Hierarchy
The typographic pairing must balance classic, editorial elegance with strict digital legibility.
*   **Display / Headings:** *Playfair Display* or *Cinzel*. High-contrast, elegant serifs. Reserved for hero statements, product titles, and pull quotes.
*   **Interface / Body:** *Montserrat* or *Lato*. A clean, geometric sans-serif. Applied with wide tracking (letter-spacing) to maintain a highly breathable, minimalist interface.

## 4. UI/UX & Interaction Guidelines
Interactions must feel expensive, meaning they should be fluid, tactile, and perfectly eased.
*   **The Hero Experience:** Avoid static hero images. Utilize a WebGL render or a seamless, high-resolution slow-motion video loop of the "Serenity" bottle.
*   **Navigation & Framing:** Employ a transparent header with ultra-thin line icons and a hidden off-canvas menu to maximize the visual real estate.
*   **Micro-interactions:** Implement a custom gold-dot cursor that expands into a translucent ring over clickable elements. All hover states (buttons, cards) must utilize a smooth, 60fps cinematic fade.
*   **Layout Architecture:** Break standard CSS grids. Use asymmetrical, editorial-style product placements to emulate a high-fashion digital magazine rather than a standard e-commerce template.