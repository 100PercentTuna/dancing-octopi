# 12 - MASTER USER STORY LIST
## All 274 User Stories with Acceptance Criteria

---

## OVERVIEW

This document contains **every user story** for the About page implementation, organized by section. Each story has:
- Unique ID
- Priority (P1 = Must Have, P2 = Should Have, P3 = Nice to Have)
- Acceptance criteria checkboxes
- Cross-reference to detailed spec

**Total Stories: 274**

---

## HERO SECTION (24 Stories)
*Detailed spec: [01-HERO-COLLAGE.md](01-HERO-COLLAGE.md)*

### US-HERO-001: Photo Collage Display (P1)
As a visitor, I want to see an asymmetric photo arrangement.
- [ ] Photos positioned organically, not in a grid
- [ ] Different sizes based on photo count (1-4)
- [ ] Overlapping photos create depth
- [ ] Photos have subtle shadows

### US-HERO-002: Grayscale Initial State (P1)
As a visitor, I want photos to start in grayscale.
- [ ] All photos: `grayscale(100%) sepia(10%)`
- [ ] Creates "exhibition" feel
- [ ] Warm-tinted grayscale, not cold

### US-HERO-003: Color Transition on Scroll (P1)
As a visitor, I want photos to transition to color as I scroll.
- [ ] Transition triggers at 30vh scroll
- [ ] 600ms ease-out transition
- [ ] All photos transition together
- [ ] Respects reduced motion preference

### US-HERO-004: Parallax Movement (P2)
As a visitor, I want photos to move at different speeds.
- [ ] Photo 1: speed 0.2 (slowest)
- [ ] Photo 2: speed 0.6 (fastest)
- [ ] Photo 3: speed 0.4
- [ ] Photo 4: speed 0.3
- [ ] Smooth movement via requestAnimationFrame

### US-HERO-005: Angular Clip Paths (P2)
As a visitor, I want photos to have angular edges.
- [ ] Subtle polygon clip-paths
- [ ] Not all photos clipped (variety)
- [ ] Angles create visual interest

### US-HERO-006: Photo Shadows (P1)
As a visitor, I want photos to feel elevated.
- [ ] Multi-layer box-shadow
- [ ] Larger spread for higher z-index
- [ ] Subtle, not harsh

### US-HERO-007: Name Display (P1)
As a visitor, I want to see the person's name prominently.
- [ ] Font: Newsreader (--serif)
- [ ] Size: clamp(32px, 5vw, 56px)
- [ ] Weight: 400 (regular)
- [ ] Positioned bottom-right
- [ ] Color: --ink

### US-HERO-008: Tagline Display (P1)
As a visitor, I want to see a brief descriptor.
- [ ] Font: Inter (--sans)
- [ ] Size: clamp(14px, 1.5vw, 18px)
- [ ] Color: --muted
- [ ] Items separated by brown dots (·)

### US-HERO-009: Handwritten Annotation (P2)
As a visitor, I want a personal touch.
- [ ] Font: Caveat (--hand)
- [ ] Color: --warm (brown, NOT blue)
- [ ] Rotated: -3 degrees
- [ ] Fade-in animation after 1.2s
- [ ] Max 40 characters

### US-HERO-010: Scroll Hint (P3)
As a visitor, I want to know I can scroll.
- [ ] Centered at bottom
- [ ] Text: "scroll" in mono uppercase
- [ ] Animated vertical line
- [ ] Fades on hover

### US-HERO-011: Single Photo Layout (P1)
As a visitor with one photo, I want proper layout.
- [ ] Centered-left positioning
- [ ] Larger size (45vw)
- [ ] Name/tagline positions adjust
- [ ] Still feels balanced

### US-HERO-012: Two Photo Layout (P1)
As a visitor with two photos, I want overlap.
- [ ] Second photo overlaps first
- [ ] Creates depth hierarchy
- [ ] Different aspect ratios

### US-HERO-013: Three Photo Layout (P1)
As a visitor with three photos, I want spread.
- [ ] Photos distributed across viewport
- [ ] Third photo on right side
- [ ] Varied heights

### US-HERO-014: Four Photo Layout (P1)
As a visitor with four photos, I want collage.
- [ ] Maximum coverage
- [ ] Complex layering
- [ ] Fourth photo smallest (accent)

### US-HERO-015: Hero Height (P1)
As a visitor, I want the hero to fill the viewport.
- [ ] min-height: 100vh
- [ ] Content doesn't overflow
- [ ] Breathing room maintained

### US-HERO-016: Photo Aspect Ratios (P2)
As a visitor, I want natural-looking photos.
- [ ] Photo 1: 4:5 (portrait)
- [ ] Photo 2: 3:4 (portrait)
- [ ] Photo 3: 5:4 (landscape)
- [ ] Photo 4: 1:1 (square)

### US-HERO-017: Z-Index Layering (P1)
As a visitor, I want clear depth.
- [ ] Photos: z-index 14-17
- [ ] Identity text: z-index 20
- [ ] Annotation: z-index 21
- [ ] Consistent throughout scroll

### US-HERO-018: Responsive Breakpoints (P1)
As a mobile visitor, I want proper layout.
- [ ] Stack photos vertically on mobile
- [ ] Reduce photo count on small screens
- [ ] Maintain parallax on tablet
- [ ] Disable parallax on mobile

### US-HERO-019: Image Loading (P2)
As a visitor, I want fast photo loading.
- [ ] Lazy loading for below-fold
- [ ] Proper srcset for responsive
- [ ] Placeholder while loading
- [ ] No layout shift

### US-HERO-020: Animation Performance (P1)
As a visitor, I want smooth animations.
- [ ] GPU-accelerated transforms
- [ ] will-change: transform on photos
- [ ] RequestAnimationFrame for scroll
- [ ] Passive scroll listener

### US-HERO-021: Reduced Motion (P1)
As a visitor with motion sensitivity, I want comfort.
- [ ] @media (prefers-reduced-motion)
- [ ] Skip parallax entirely
- [ ] Photos start in color
- [ ] No scroll animations

### US-HERO-022: Print Styles (P3)
As a visitor printing the page, I want photos visible.
- [ ] Full color in print
- [ ] No parallax offsets
- [ ] Simple stacked layout

### US-HERO-023: Keyboard Focus (P2)
As a keyboard user, I want accessibility.
- [ ] Focus visible on any links
- [ ] Skip to content link above
- [ ] Logical tab order

### US-HERO-024: Screen Reader (P1)
As a screen reader user, I want context.
- [ ] Alt text on all photos
- [ ] Aria-label on hero section
- [ ] Name/tagline read properly
- [ ] Decorative annotation marked aria-hidden

---

## ORGANIC FLOW (18 Stories)
*Detailed spec: [02-ORGANIC-FLOW.md](02-ORGANIC-FLOW.md)*

### US-FLOW-001: No Hard Section Boundaries (P1)
As a visitor, I want a continuous experience.
- [ ] No visible dividers between sections
- [ ] Content flows naturally
- [ ] No "jump" between areas

### US-FLOW-002: Overlap Zones (P1)
As a visitor, I want smooth transitions.
- [ ] Sections overlap by 40-80px
- [ ] Gradient fades between areas
- [ ] No abrupt content changes

### US-FLOW-003: Z-Depth Perception (P1)
As a visitor, I want depth.
- [ ] Background layer fixed
- [ ] Atmospheric layer parallax
- [ ] Content layer normal scroll
- [ ] Clear hierarchy

### US-FLOW-004: Fade-In Reveals (P1)
As a visitor, I want content to appear.
- [ ] Sections fade in on scroll
- [ ] 800ms transition duration
- [ ] 30px translateY on entry

### US-FLOW-005: Staggered Children (P2)
As a visitor, I want sequential reveals.
- [ ] Child elements appear one by one
- [ ] 100ms delay between each
- [ ] Max 5 children staggered

### US-FLOW-006: Scroll Rhythm (P2)
As a visitor, I want pacing.
- [ ] Visual events at specific scroll positions
- [ ] Consistent timing between events
- [ ] Not too fast, not too slow

### US-FLOW-007: Content Panels (P1)
As a visitor, I want readable content.
- [ ] White background panels
- [ ] Clear text contrast
- [ ] Proper z-index above images

### US-FLOW-008: Panel Overlays (P2)
As a visitor, I want layered panels.
- [ ] Panels can overlay atmospheric images
- [ ] Gradient fade from transparent to bg
- [ ] Content readable over images

### US-FLOW-009: Window Cutouts (P2)
As a visitor, I want reveal moments.
- [ ] Panel with transparent center
- [ ] Background image visible through
- [ ] Creates "window" effect

### US-FLOW-010: Seamless Transitions (P1)
As a visitor, I want invisible seams.
- [ ] Gradient overlays hide edges
- [ ] Smooth color transitions
- [ ] No visible boundaries

### US-FLOW-011: Rhythm Spacers (P2)
As a visitor, I want breathing room.
- [ ] Consistent spacing values
- [ ] sm/md/lg/xl sizes
- [ ] Maintains visual rhythm

### US-FLOW-012: Scroll Position Awareness (P2)
As the page, I want to track scroll.
- [ ] Track viewport scroll position
- [ ] Fire callbacks at thresholds
- [ ] Use requestAnimationFrame

### US-FLOW-013: Re-animation Support (P3)
As a visitor scrolling back, I want animations.
- [ ] Elements can re-animate
- [ ] Optional behavior
- [ ] Smooth in both directions

### US-FLOW-014: Performance (P1)
As a visitor, I want smooth scrolling.
- [ ] Passive scroll listeners
- [ ] RequestAnimationFrame for updates
- [ ] Minimal DOM manipulation

### US-FLOW-015: Reduced Motion (P1)
As a motion-sensitive visitor, I want comfort.
- [ ] All content visible immediately
- [ ] No fade-in animations
- [ ] No parallax movement

### US-FLOW-016: Mobile Simplification (P1)
As a mobile visitor, I want clarity.
- [ ] Reduced overlap zones (20px)
- [ ] Simpler transitions
- [ ] No parallax

### US-FLOW-017: Print Layout (P3)
As a printing visitor, I want all content.
- [ ] All sections visible
- [ ] No overlaps
- [ ] Sequential layout

### US-FLOW-018: Section Labels (P2)
As a visitor, I want orientation.
- [ ] Mono font labels ("01 ABOUT")
- [ ] Fade in with section
- [ ] Brown accent underline

---

## ATMOSPHERIC IMAGES (32 Stories)
*Detailed spec: [03-ATMOSPHERIC-IMAGES.md](03-ATMOSPHERIC-IMAGES.md)*

### US-ATMO-001: Full-Width Strip Display (P1)
- [ ] 100vw width, edge to edge
- [ ] Height: clamp(200px, 30vh, 400px)
- [ ] object-fit: cover

### US-ATMO-002: Strip Clip Paths (P2)
- [ ] Straight (default)
- [ ] Angle bottom
- [ ] Angle top
- [ ] Angle both

### US-ATMO-003: Strip Parallax (P2)
- [ ] Speed: 0.2 (slow)
- [ ] Smooth movement
- [ ] will-change: transform

### US-ATMO-004: Strip Caption (P3)
- [ ] Mono font, 11px
- [ ] Bottom-right position
- [ ] White text with shadow

### US-ATMO-005: Window Cutout Display (P2)
- [ ] Image behind, panel in front
- [ ] Cutout reveals image
- [ ] Width: clamp(300px, 60%, 700px)

### US-ATMO-006: Window Mask Effect (P2)
- [ ] Box-shadow creates mask
- [ ] Clean edge on cutout
- [ ] Border-radius: 4px

### US-ATMO-007: Window Content (P2)
- [ ] Content on foreground layer
- [ ] z-index above image
- [ ] Readable typography

### US-ATMO-008: Angular Window (P3)
- [ ] Optional angular cutout
- [ ] Polygon clip-path
- [ ] Matches design language

### US-ATMO-009: Dual Image Grid (P2)
- [ ] Two-column grid
- [ ] Gap: var(--space-4)
- [ ] Different heights

### US-ATMO-010: Dual Offset (P2)
- [ ] Left: translateY(20px)
- [ ] Right: translateY(-20px)
- [ ] Visual interest

### US-ATMO-011: Dual Overlap (P3)
- [ ] Optional overlap mode
- [ ] Negative margins
- [ ] Z-index layering

### US-ATMO-012: Dual Responsive (P1)
- [ ] Stack on mobile
- [ ] Full width each
- [ ] Maintain aspect ratios

### US-ATMO-013: Background Layer (P2)
- [ ] Fixed behind content
- [ ] Extends beyond content bounds
- [ ] Height: content + 100px

### US-ATMO-014: Background Parallax (P2)
- [ ] Slowest speed (0.1)
- [ ] Creates depth
- [ ] Peeks around panel edges

### US-ATMO-015: Background Content Panel (P2)
- [ ] White background
- [ ] Centered, max-width
- [ ] Box shadow for lift

### US-ATMO-016: Background Visibility (P2)
- [ ] Visible at edges
- [ ] Creates frame effect
- [ ] Adjusts with scroll

### US-ATMO-017: Quote Display (P2)
- [ ] Positioned bottom-left
- [ ] Max-width: 500px
- [ ] Text shadow for readability

### US-ATMO-018: Quote Typography (P2)
- [ ] Serif, italic
- [ ] Size: clamp(18px, 2.5vw, 24px)
- [ ] White color

### US-ATMO-019: Quote Attribution (P2)
- [ ] Mono, 11px, uppercase
- [ ] Slightly transparent
- [ ] Below quote text

### US-ATMO-020: Quote Background (P2)
- [ ] Gradient overlay
- [ ] Bottom to top fade
- [ ] Ensures readability

### US-ATMO-021: Grayscale Default (P1)
- [ ] grayscale(100%) sepia(10%)
- [ ] Warm grayscale
- [ ] Consistent across types

### US-ATMO-022: Color on Scroll (P1)
- [ ] Transition at 30% visible
- [ ] 600ms ease-out
- [ ] Smooth transition

### US-ATMO-023: Color Persistence (P2)
- [ ] Once colored, stays colored
- [ ] No re-grayscale on scroll back
- [ ] Single transition

### US-ATMO-024: Image Sizing (P1)
- [ ] object-fit: cover always
- [ ] Responsive heights
- [ ] No distortion

### US-ATMO-025: Z-Index Layering (P1)
- [ ] Strip: z-index 10
- [ ] Window image: z-index 10
- [ ] Window foreground: z-index 20
- [ ] Quote: z-index 15

### US-ATMO-026: Admin Positions (P1)
- [ ] 8 position options
- [ ] Auto-place option
- [ ] Clear in Customizer

### US-ATMO-027: Admin Type Selection (P1)
- [ ] 4 type options + hidden
- [ ] Dropdown select
- [ ] Live preview

### US-ATMO-028: Performance (P1)
- [ ] Lazy loading
- [ ] will-change for parallax
- [ ] RequestAnimationFrame

### US-ATMO-029: Reduced Motion (P1)
- [ ] Skip parallax
- [ ] Immediate color
- [ ] Static display

### US-ATMO-030: Mobile Adaptation (P1)
- [ ] Reduced heights
- [ ] Simpler layouts
- [ ] No parallax

### US-ATMO-031: Print Styles (P3)
- [ ] Full color
- [ ] Static position
- [ ] Visible in print

### US-ATMO-032: Empty State (P1)
- [ ] Hidden if no image
- [ ] No broken layout
- [ ] Smooth flow continues

---

## BIO & BOOKSHELF (28 Stories)
*Detailed spec: [04-BIO-BOOKSHELF.md](04-BIO-BOOKSHELF.md)*

### US-BIO-001: Drop Cap Display (P1)
- [ ] First letter: 4.5em, float left
- [ ] Serif font
- [ ] Proper line wrapping

### US-BIO-002: Bio Text Styling (P1)
- [ ] Serif, 19px
- [ ] Line-height: 1.75
- [ ] Max-width: 620px

### US-BIO-003: Pull Quote (P2)
- [ ] Left border: 3px brown
- [ ] Indented (padding-left: 32px)
- [ ] Italic text
- [ ] Attribution in mono

### US-BIO-004: Section Label (P1)
- [ ] Mono, 11px, uppercase
- [ ] "01 ABOUT" format
- [ ] Brown underline accent

### US-BIO-005: Multiple Paragraphs (P1)
- [ ] Proper spacing between
- [ ] Natural reading flow
- [ ] No awkward breaks

### US-BIO-006: Bio from Page Content (P1)
- [ ] Use page editor content
- [ ] Apply proper formatting
- [ ] Preserve paragraphs

### US-BIO-007: Empty Bio Handling (P1)
- [ ] Hide section if empty
- [ ] No placeholder text
- [ ] Smooth flow continues

### US-BIO-008: Very Long Bio (P2)
- [ ] No truncation
- [ ] Multiple paragraphs work
- [ ] Natural scroll

### US-BOOK-001: Bookshelf Display (P1)
- [ ] 6-8 books maximum
- [ ] Shelf surface visible
- [ ] Books standing upright

### US-BOOK-002: Book Hover Tilt (P1)
- [ ] Lift: translateY(-15px)
- [ ] Tilt: rotateX(-5deg)
- [ ] Shadow intensifies

### US-BOOK-003: Book Tooltip (P1)
- [ ] Title and author visible
- [ ] Appears above book
- [ ] 200ms fade animation

### US-BOOK-004: Shelf Texture (P2)
- [ ] Brown gradient
- [ ] Subtle shadow
- [ ] Wood-like appearance

### US-BOOK-005: Book Cover Display (P1)
- [ ] Image from admin
- [ ] object-fit: cover
- [ ] Spine edge effect (left shadow)

### US-BOOK-006: Book Touch Interaction (P1)
- [ ] Tap shows tooltip
- [ ] Tap outside closes
- [ ] Works on mobile

### US-BOOK-007: Single Book Display (P2)
- [ ] Center single book
- [ ] Shorter shelf
- [ ] Still looks good

### US-BOOK-008: No Books Handling (P1)
- [ ] Hide entire section
- [ ] No empty shelf
- [ ] Smooth flow

### US-BOOK-009: Book Link (P3)
- [ ] Optional URL per book
- [ ] Opens in new tab
- [ ] Clear indication

### US-BOOK-010: Missing Book Cover (P1)
- [ ] Gradient placeholder
- [ ] Title still in tooltip
- [ ] No broken image

### US-BOOK-011: Book Responsive (P1)
- [ ] Fewer books on mobile
- [ ] Horizontal scroll option
- [ ] Touch-friendly

### US-BOOK-012: Tooltip Position (P2)
- [ ] Above book on desktop
- [ ] Fixed bottom on mobile
- [ ] Doesn't overflow

### US-BOOK-013: Book Animation Performance (P1)
- [ ] GPU-accelerated
- [ ] Smooth 60fps
- [ ] No jank

### US-BOOK-014: Bookshelf Section Label (P2)
- [ ] "CURRENTLY READING"
- [ ] Mono uppercase
- [ ] Centered above shelf

### US-BOOK-015: Shelf Shadow (P2)
- [ ] Books cast shadow
- [ ] Shelf has depth
- [ ] Realistic feel

### US-BOOK-016: Book Keyboard (P2)
- [ ] Tab to focus
- [ ] Enter shows tooltip
- [ ] Escape closes

### US-BOOK-017: Book Screen Reader (P1)
- [ ] Title announced
- [ ] Author announced
- [ ] Role="button"

### US-BOOK-018: Shelf Responsive Width (P2)
- [ ] Matches book count
- [ ] Centered on page
- [ ] Max-width enforced

### US-BOOK-019: Book Transition Timing (P2)
- [ ] 300ms for hover
- [ ] Cubic-bezier bounce
- [ ] Feels natural

### US-BOOK-020: Print Book Display (P3)
- [ ] All books visible
- [ ] No hover states
- [ ] Simple layout

---

## WORLD MAP (36 Stories)
*Detailed spec: [05-WORLD-MAP.md](05-WORLD-MAP.md)*

### US-MAP-001: SVG World Map (P1)
- [ ] Full world map visible
- [ ] All countries identifiable
- [ ] Clean, minimal style

### US-MAP-002: Country Paths (P1)
- [ ] Each country has data-country attribute
- [ ] ISO 2-letter codes used
- [ ] Paths are selectable

### US-MAP-003: Default Country Color (P1)
- [ ] Unvisited: #E8E8E8 (light gray)
- [ ] Clean, neutral appearance
- [ ] White borders between

### US-MAP-004: Visited Country Color (P1)
- [ ] Visited: var(--map-visited)
- [ ] Light tan (warmLight)
- [ ] Clear difference from default

### US-MAP-005: Lived Country Color (P1)
- [ ] Lived: var(--map-lived)
- [ ] Dark brown (warm)
- [ ] More prominent than visited

### US-MAP-006: Current Country Color (P1)
- [ ] Current: var(--map-current)
- [ ] Terracotta (NOT blue)
- [ ] Most prominent color

### US-MAP-007: Hover States (P2)
- [ ] Interactive countries only
- [ ] Slight color change on hover
- [ ] Cursor changes to pointer

### US-MAP-008: Country Borders (P2)
- [ ] White stroke between countries
- [ ] 0.5px stroke width
- [ ] Clean separation

### US-MAP-009: Marker Display (P2)
- [ ] Dot on current country
- [ ] Centered on country
- [ ] White border for visibility

### US-MAP-010: Pulse Animation (P2)
- [ ] Expanding circle animation
- [ ] 2s duration
- [ ] Infinite loop

### US-MAP-011: Marker Visibility (P2)
- [ ] Above country fill
- [ ] Not clickable (pointer-events: none)
- [ ] Clear focal point

### US-MAP-012: Tooltip Display (P1)
- [ ] Appears on hover/tap
- [ ] Contains country name, years, story
- [ ] Positioned near country

### US-MAP-013: Tooltip Header (P1)
- [ ] Country name (mono, uppercase)
- [ ] Years below name
- [ ] Close button (X)

### US-MAP-014: Tooltip Story (P1)
- [ ] Italic serif font
- [ ] Max 200 characters
- [ ] Readable line height

### US-MAP-015: Tooltip Positioning (P2)
- [ ] Above country by default
- [ ] Flips to below if near top
- [ ] Stays within container

### US-MAP-016: Tooltip Close Button (P1)
- [ ] X icon in corner
- [ ] Clickable/tappable
- [ ] Visible on hover

### US-MAP-017: Tooltip Close Behavior (P1)
- [ ] X button closes
- [ ] Click outside closes
- [ ] Escape key closes

### US-MAP-018: Tooltip Animation (P2)
- [ ] Fade in (200ms)
- [ ] Slight translateY
- [ ] Smooth appearance

### US-MAP-019: Desktop Hover (P1)
- [ ] Tooltip on mouseover
- [ ] Stays while hovering tooltip
- [ ] Hides on mouseout

### US-MAP-020: Touch Support (P1)
- [ ] Tap to show tooltip
- [ ] Tap outside to close
- [ ] No hover states

### US-MAP-021: Keyboard Support (P2)
- [ ] Escape closes tooltip
- [ ] Focus visible on close button
- [ ] Accessible interaction

### US-MAP-022: Countries Without Stories (P2)
- [ ] Still show color
- [ ] No tooltip
- [ ] Cursor: default

### US-MAP-023: Legend Display (P1)
- [ ] Below map
- [ ] Centered
- [ ] All 4 states shown

### US-MAP-024: Legend Items (P1)
- [ ] Color swatch
- [ ] Label text
- [ ] Mono font, 11px

### US-MAP-025: Legend Order (P2)
- [ ] Current first
- [ ] Lived second
- [ ] Visited third
- [ ] Not visited last

### US-MAP-026: Legend Responsive (P2)
- [ ] Wraps on mobile
- [ ] Spacing adjusts
- [ ] Readable at all sizes

### US-MAP-027: Label Display (P2)
- [ ] "02" number
- [ ] Brown underline
- [ ] Customizable text

### US-MAP-028: Label Position (P2)
- [ ] Above map
- [ ] Centered
- [ ] Proper spacing

### US-MAP-029: Map Scaling (P1)
- [ ] Maintains aspect ratio
- [ ] Max-width: 900px
- [ ] Responsive on mobile

### US-MAP-030: Mobile Touch Targets (P1)
- [ ] Countries tappable
- [ ] 44px minimum target
- [ ] Pinch-to-zoom optional

### US-MAP-031: Mobile Tooltip (P1)
- [ ] Simplified on small screens
- [ ] Clear close button
- [ ] Readable text

### US-MAP-032: SVG Optimization (P2)
- [ ] Simplified paths
- [ ] Minimal file size
- [ ] Fast rendering

### US-MAP-033: Animation Performance (P2)
- [ ] CSS animations for pulse
- [ ] No jank
- [ ] GPU accelerated

### US-MAP-034: ARIA Labels (P1)
- [ ] Map role="img"
- [ ] Country aria-labels
- [ ] Tooltip aria-live

### US-MAP-035: Screen Reader (P1)
- [ ] Country names announced
- [ ] Story content accessible
- [ ] Navigation explained

### US-MAP-036: Reduced Motion (P1)
- [ ] No pulse animation
- [ ] Static marker
- [ ] Instant tooltip

---

## INTERESTS CLOUD (22 Stories)
*Detailed spec: [06-INTERESTS-INSPIRATIONS.md](06-INTERESTS-INSPIRATIONS.md)*

### US-INT-001: Cloud Layout (P1)
- [ ] Flex wrap layout
- [ ] Varied vertical offsets (-8px to +8px)
- [ ] Not a rigid grid

### US-INT-002: Interest Image (P1)
- [ ] Circular images (56px)
- [ ] Admin uploads
- [ ] Grayscale default

### US-INT-003: Interest Hover (P1)
- [ ] Grayscale → color
- [ ] Scale: 1.08
- [ ] Brown border (NOT blue)

### US-INT-004: Interest Label (P1)
- [ ] Mono, 11px
- [ ] Gray → black on hover
- [ ] Below image

### US-INT-005: Section Label (P1)
- [ ] "THINGS THAT FASCINATE ME"
- [ ] Customizable text
- [ ] Mono uppercase centered

### US-INT-006: Few Interests (P2)
- [ ] 1-3 interests centered
- [ ] Smaller container
- [ ] Still balanced

### US-INT-007: Many Interests (P2)
- [ ] 15+ wrap properly
- [ ] Maintain organic feel
- [ ] No overflow

### US-INT-008: Missing Image (P2)
- [ ] Show first letter
- [ ] Styled circle
- [ ] Label still visible

### US-INT-009: Empty Section (P1)
- [ ] Hide if no interests
- [ ] Smooth flow
- [ ] No placeholder

### US-INT-010: Interest Not Links (P1)
- [ ] No underline
- [ ] No blue on hover
- [ ] Cursor: default

### US-INT-011: Mobile Grid (P1)
- [ ] Convert to grid layout
- [ ] Smaller images (40px)
- [ ] No offsets

### US-INT-012: Touch Interaction (P1)
- [ ] Tap for color
- [ ] Release returns gray
- [ ] Works on mobile

### US-INT-013: Image Transition (P2)
- [ ] 400ms filter transition
- [ ] 300ms scale transition
- [ ] Smooth feel

### US-INT-014: Admin Limit (P2)
- [ ] Max 20 interests
- [ ] Individual fields
- [ ] Image + name each

### US-INT-015: Offset Pattern (P2)
- [ ] nth-child variations
- [ ] -8px to +8px range
- [ ] Organic appearance

### US-INT-016: Image Loading (P2)
- [ ] Lazy loading
- [ ] Placeholder on load
- [ ] No shift

### US-INT-017: Reduced Motion (P1)
- [ ] No scale animation
- [ ] Instant color
- [ ] Static display

### US-INT-018: Keyboard (P3)
- [ ] Not focusable (not links)
- [ ] Decorative only
- [ ] Skip in tab

### US-INT-019: Screen Reader (P2)
- [ ] List of interests
- [ ] Names announced
- [ ] Grouped semantically

### US-INT-020: Print Display (P3)
- [ ] All visible
- [ ] Full color
- [ ] Simple layout

### US-INT-021: Spacing (P2)
- [ ] Proper gaps
- [ ] Not too tight
- [ ] Breathing room

### US-INT-022: Performance (P1)
- [ ] Efficient transitions
- [ ] No hover jank
- [ ] Smooth feel

---

## INSPIRATIONS (20 Stories)
*Detailed spec: [06-INTERESTS-INSPIRATIONS.md](06-INTERESTS-INSPIRATIONS.md)*

### US-INSP-001: Card Grid (P1)
- [ ] Auto-fill grid
- [ ] Min 200px per card
- [ ] Responsive columns

### US-INSP-002: Photo Display (P1)
- [ ] Circular, 80px
- [ ] Grayscale default
- [ ] Color on hover

### US-INSP-003: Card Hover (P1)
- [ ] Blue border (it's a link)
- [ ] Blue tint background
- [ ] Photo colors

### US-INSP-004: Person Info (P1)
- [ ] Name: serif, 17px
- [ ] Role: sans, 13px
- [ ] Note: serif italic, 14px

### US-INSP-005: Text States (P2)
- [ ] Gray default
- [ ] Black on hover
- [ ] Matches photo transition

### US-INSP-006: Section Label (P1)
- [ ] "PEOPLE WHO INSPIRE ME"
- [ ] Customizable
- [ ] Mono uppercase

### US-INSP-007: Card Links (P1)
- [ ] Optional URL
- [ ] Opens new tab
- [ ] Blue hover for links only

### US-INSP-008: Non-Link Cards (P1)
- [ ] No blue hover
- [ ] Still color photo
- [ ] Cursor: default

### US-INSP-009: Missing Photo (P2)
- [ ] Show initials
- [ ] First + last letter
- [ ] Styled circle

### US-INSP-010: Empty Section (P1)
- [ ] Hide if none
- [ ] Smooth flow
- [ ] No grid visible

### US-INSP-011: Mobile Layout (P1)
- [ ] 2 columns
- [ ] Smaller photos (60px)
- [ ] Hide note

### US-INSP-012: Single Inspiration (P2)
- [ ] Centered card
- [ ] Still looks good
- [ ] Proper width

### US-INSP-013: Card Touch (P1)
- [ ] Tap opens link
- [ ] Active state visible
- [ ] Clear feedback

### US-INSP-014: Photo Transition (P2)
- [ ] 400ms grayscale
- [ ] Smooth reveal
- [ ] Matches interests

### US-INSP-015: Card Border (P2)
- [ ] 1px transparent default
- [ ] Blue on hover (links)
- [ ] 8px radius

### US-INSP-016: Admin Fields (P1)
- [ ] Photo, name, role, note, URL
- [ ] Individual fields
- [ ] Max 8 people

### US-INSP-017: Long Name (P2)
- [ ] Truncate if needed
- [ ] No wrap issues
- [ ] Clean display

### US-INSP-018: Keyboard Focus (P2)
- [ ] Tab to cards (links)
- [ ] Focus ring visible
- [ ] Enter activates

### US-INSP-019: Screen Reader (P1)
- [ ] Name + role read
- [ ] Link announced
- [ ] Grouped list

### US-INSP-020: Print Display (P3)
- [ ] All visible
- [ ] Full color
- [ ] Simple grid

---

## STATS & CONNECT (12 Stories)
*From various specs*

### US-STAT-001: Counter Display (P1)
- [ ] Large numbers
- [ ] Animate on scroll
- [ ] Count up to value

### US-STAT-002: Counter Animation (P1)
- [ ] 2s duration
- [ ] Ease-out curve
- [ ] One-time trigger

### US-STAT-003: Stat Labels (P1)
- [ ] Mono uppercase
- [ ] Below numbers
- [ ] Clear meaning

### US-STAT-004: Stat Grid (P2)
- [ ] Horizontal on desktop
- [ ] 2x2 on mobile
- [ ] Centered

### US-STAT-005: Empty Stats (P1)
- [ ] Hide section if none
- [ ] No placeholders
- [ ] Flow continues

### US-STAT-006: Number Format (P2)
- [ ] Thousands separator
- [ ] Locale-aware
- [ ] Integer only

### US-CONN-001: Connect Heading (P1)
- [ ] Customizable text
- [ ] Serif font
- [ ] Centered

### US-CONN-002: Social Links (P1)
- [ ] From theme settings
- [ ] Icon + text
- [ ] Opens new tab

### US-CONN-003: Link Hover (P2)
- [ ] Blue color
- [ ] Underline visible
- [ ] Accessible

### US-CONN-004: Empty Connect (P1)
- [ ] Hide if no links
- [ ] No empty section
- [ ] Flow to footer

### US-CONN-005: Mobile Connect (P2)
- [ ] Stack vertically
- [ ] Touch-friendly
- [ ] 44px targets

### US-CONN-006: Connect Keyboard (P2)
- [ ] All focusable
- [ ] Focus visible
- [ ] Logical order

---

## ANIMATIONS (26 Stories)
*Detailed spec: [07-ANIMATIONS.md](07-ANIMATIONS.md)*

### US-ANIM-001: Fade-In Reveal (P1)
- [ ] Elements fade in on scroll
- [ ] 30px translateY
- [ ] 800ms duration

### US-ANIM-002: Reveal Variants (P2)
- [ ] Fade only option
- [ ] Slide from left
- [ ] Slide from right
- [ ] Scale up

### US-ANIM-003: Stagger Delays (P2)
- [ ] 100ms increments
- [ ] Up to 6 delays
- [ ] Per-element control

### US-ANIM-004: Reveal Threshold (P1)
- [ ] 10% visible triggers
- [ ] -10% root margin
- [ ] Consistent behavior

### US-ANIM-005: Parallax Movement (P2)
- [ ] Layer-based speeds
- [ ] Smooth 60fps
- [ ] RequestAnimationFrame

### US-ANIM-006: Parallax Speeds (P2)
- [ ] 0.1 (slowest)
- [ ] 0.2, 0.4, 0.6
- [ ] 1.0 (normal scroll)

### US-ANIM-007: Parallax Direction (P3)
- [ ] Up or down option
- [ ] Default: down
- [ ] Data attribute control

### US-ANIM-008: Parallax Bounds (P1)
- [ ] No overflow visible
- [ ] Contained in wrapper
- [ ] No layout shift

### US-ANIM-009: Grayscale Default (P1)
- [ ] grayscale(100%) sepia(10%)
- [ ] Warm tone
- [ ] Applied to all photos

### US-ANIM-010: Color Reveal (P1)
- [ ] 600ms transition
- [ ] ease-out curve
- [ ] At 30% visible

### US-ANIM-011: Hover Color (P2)
- [ ] 400ms transition
- [ ] Interest/inspiration images
- [ ] Reversible

### US-ANIM-012: Book Lift (P1)
- [ ] translateY(-15px)
- [ ] rotateX(-5deg)
- [ ] Bounce easing

### US-ANIM-013: Interest Scale (P1)
- [ ] scale(1.08)
- [ ] Brown border appear
- [ ] Bounce easing

### US-ANIM-014: Card Hover (P2)
- [ ] Blue border (links only)
- [ ] Blue tint background
- [ ] 300ms transition

### US-ANIM-015: Button Feedback (P3)
- [ ] translateY(-2px) on hover
- [ ] Return on active
- [ ] Shadow change

### US-ANIM-016: Count-Up (P1)
- [ ] Start at 0
- [ ] 2s default duration
- [ ] Ease-out cubic

### US-ANIM-017: Counter Trigger (P1)
- [ ] 50% visible
- [ ] IntersectionObserver
- [ ] One-time animation

### US-ANIM-018: Number Format (P2)
- [ ] Locale-aware commas
- [ ] Integer only
- [ ] No decimals

### US-ANIM-019: Hero Sequence (P2)
- [ ] Photos stagger (200ms)
- [ ] Identity at 800ms
- [ ] Annotation at 1200ms

### US-ANIM-020: Section Stagger (P2)
- [ ] Children stagger 100ms
- [ ] Max 5 children
- [ ] CSS keyframes

### US-ANIM-021: Scroll Rhythm (P3)
- [ ] Events at specific vh
- [ ] Consistent pacing
- [ ] Documented triggers

### US-ANIM-022: GPU Layers (P1)
- [ ] will-change on animated
- [ ] Remove after complete
- [ ] Minimal repaints

### US-ANIM-023: Passive Listeners (P1)
- [ ] All scroll listeners
- [ ] All touch listeners
- [ ] No blocking

### US-ANIM-024: RequestAnimationFrame (P1)
- [ ] All scroll updates
- [ ] Single RAF per frame
- [ ] Throttled callbacks

### US-ANIM-025: Reduced Motion (P1)
- [ ] Respect preference
- [ ] Skip all animations
- [ ] Show content immediately

### US-ANIM-026: Motion Detection (P1)
- [ ] Check on load
- [ ] Listen for changes
- [ ] Apply dynamically

---

## EDGE CASES (18 Stories)
*Detailed spec: [08-STATES-EDGE-CASES.md](08-STATES-EDGE-CASES.md)*

### US-EDGE-001: Hero Without Photos (P1)
- [ ] Shows name/tagline centered
- [ ] No broken layout
- [ ] Minimal style applied

### US-EDGE-002: Empty Bio (P1)
- [ ] Section hidden completely
- [ ] Flow continues to next section
- [ ] No gap or placeholder

### US-EDGE-003: No Books (P1)
- [ ] Bookshelf hidden
- [ ] No empty shelf visible
- [ ] Smooth transition

### US-EDGE-004: No Map Countries (P1)
- [ ] Map section hidden
- [ ] No empty map SVG
- [ ] Content flows

### US-EDGE-005: No Interests (P1)
- [ ] Section hidden
- [ ] No empty cloud
- [ ] Clean layout

### US-EDGE-006: No Inspirations (P1)
- [ ] Section hidden
- [ ] No empty grid
- [ ] Proper spacing

### US-EDGE-007: Missing Book Cover (P1)
- [ ] Gradient placeholder
- [ ] Title/author still show
- [ ] No broken image icon

### US-EDGE-008: Missing Interest Image (P2)
- [ ] Letter initial in circle
- [ ] First letter of name
- [ ] Styled appropriately

### US-EDGE-009: Missing Inspiration Photo (P2)
- [ ] Initials in circle
- [ ] First + last initial
- [ ] Card still functional

### US-EDGE-010: Image Load Error (P1)
- [ ] Fallback displayed
- [ ] Error logged
- [ ] No visual break

### US-EDGE-011: Section Toggle Off (P1)
- [ ] Section not rendered
- [ ] No empty space
- [ ] Other sections adjust

### US-EDGE-012: Multiple Sections Off (P1)
- [ ] Page still cohesive
- [ ] Flow maintained
- [ ] Minimum viable page

### US-EDGE-013: Very Long Bio (P2)
- [ ] No truncation
- [ ] Natural paragraph flow
- [ ] Readable layout

### US-EDGE-014: Single Interest (P2)
- [ ] Centered display
- [ ] Still feels intentional
- [ ] Section not awkward

### US-EDGE-015: Maximum Books (P2)
- [ ] 8 books fit
- [ ] Shelf width adjusts
- [ ] Tooltips work

### US-EDGE-016: Many Countries (P2)
- [ ] Map performs well
- [ ] All tooltips work
- [ ] No slowdown

### US-EDGE-017: JavaScript Disabled (P1)
- [ ] Content visible
- [ ] No animations (acceptable)
- [ ] Links work

### US-EDGE-018: Slow Network (P2)
- [ ] Progressive loading
- [ ] Placeholders shown
- [ ] Content appears gradually

---

## ACCESSIBILITY (16 Stories)
*Detailed spec: [09-ACCESSIBILITY.md](09-ACCESSIBILITY.md)*

### US-A11Y-001: Skip Link (P1)
- [ ] Skip link present
- [ ] Visible on focus
- [ ] Jumps to main content

### US-A11Y-002: ARIA Labels (P1)
- [ ] All sections labeled
- [ ] Interactive elements described
- [ ] Decorative elements hidden

### US-A11Y-003: Keyboard Navigation (P1)
- [ ] Tab through all interactive
- [ ] Enter/Space activate
- [ ] Escape closes dialogs

### US-A11Y-004: Focus Visible (P1)
- [ ] Clear focus indicator
- [ ] 2px blue outline
- [ ] Offset for visibility

### US-A11Y-005: Focus Management (P1)
- [ ] Focus returns after close
- [ ] Focus trap in dialogs
- [ ] Logical focus order

### US-A11Y-006: Screen Reader (P1)
- [ ] Heading structure
- [ ] Live announcements
- [ ] sr-only content

### US-A11Y-007: Color Contrast (P1)
- [ ] 4.5:1 for body text
- [ ] 3:1 for large text
- [ ] Links distinguishable

### US-A11Y-008: Color-Blind Safe (P1)
- [ ] Not color-only info
- [ ] Value differences
- [ ] Underlines on links

### US-A11Y-009: Reduced Motion (P1)
- [ ] CSS media query
- [ ] JavaScript check
- [ ] Content visible immediately

### US-A11Y-010: Touch Targets (P1)
- [ ] 44x44px minimum
- [ ] Adequate spacing
- [ ] No overlap

### US-A11Y-011: Alt Text (P1)
- [ ] Meaningful for content
- [ ] Empty for decorative
- [ ] Dynamic from admin

### US-A11Y-012: Tooltip Accessibility (P1)
- [ ] Role="tooltip"
- [ ] aria-haspopup
- [ ] Close button focusable

### US-A11Y-013: Map Accessibility (P1)
- [ ] Countries focusable
- [ ] Aria labels per country
- [ ] Story announced

### US-A11Y-014: Print Accessibility (P3)
- [ ] Content visible
- [ ] No interactive elements
- [ ] Readable layout

### US-A11Y-015: Zoom Support (P1)
- [ ] Works at 200% zoom
- [ ] No horizontal scroll
- [ ] Text reflows

### US-A11Y-016: Text Resize (P1)
- [ ] Works with browser text resize
- [ ] Layout adjusts
- [ ] No content clipping

---

## ADMIN/CUSTOMIZER (22 Stories)
*Detailed spec: [11-ADMIN-CUSTOMIZER.md](11-ADMIN-CUSTOMIZER.md)*

### US-ADMIN-001: No JSON Input (P1)
- [ ] No JSON textareas anywhere
- [ ] Individual fields for all data
- [ ] Comma-separated lists only for ISO codes

### US-ADMIN-002: Image Previews (P2)
- [ ] Thumbnail in Customizer for all images
- [ ] Clear which slot each image belongs to
- [ ] Standard WP media picker

### US-ADMIN-003: Section Toggles (P1)
- [ ] Checkbox to show/hide each section
- [ ] Hidden sections don't break layout
- [ ] Default: all enabled

### US-ADMIN-004: Helpful Descriptions (P2)
- [ ] Description text on complex fields
- [ ] Character limit guidance where relevant
- [ ] Format examples (e.g., ISO codes)

### US-ADMIN-005: Logical Organization (P1)
- [ ] Grouped under About Page panel
- [ ] Sections in page order
- [ ] Consistent naming

### US-ADMIN-006: Hero Settings (P1)
- [ ] Toggle, name, tagline, annotation
- [ ] 4 photo slots
- [ ] All individual fields

### US-ADMIN-007: Bio Settings (P1)
- [ ] Toggle for bio section
- [ ] Pull quote toggle/text/attr
- [ ] Content from page editor

### US-ADMIN-008: Bookshelf Settings (P1)
- [ ] Toggle for section
- [ ] 8 book slots
- [ ] Cover, title, author, URL each

### US-ADMIN-009: Map Settings (P1)
- [ ] Toggle for section
- [ ] Visited countries (comma-separated)
- [ ] Lived countries (comma-separated)
- [ ] Current country (single code)

### US-ADMIN-010: Map Stories (P1)
- [ ] 10 story slots
- [ ] Country code, years, text each
- [ ] Max 200 chars for story

### US-ADMIN-011: Interest Settings (P1)
- [ ] Toggle for section
- [ ] Section label customizable
- [ ] 20 interest slots

### US-ADMIN-012: Interest Fields (P1)
- [ ] Name and image per interest
- [ ] Individual fields
- [ ] Media picker for images

### US-ADMIN-013: Inspiration Settings (P1)
- [ ] Toggle for section
- [ ] Section label customizable
- [ ] 8 people slots

### US-ADMIN-014: Inspiration Fields (P1)
- [ ] Photo, name, role, note, URL
- [ ] Individual fields
- [ ] All optional except name

### US-ADMIN-015: Stats Settings (P1)
- [ ] Toggle for section
- [ ] 4 stat slots
- [ ] Value (number) and label (text)

### US-ADMIN-016: Atmospheric Settings (P1)
- [ ] 12 image slots
- [ ] Type selector (strip/window/dual/bg/hidden)
- [ ] Position selector

### US-ADMIN-017: Atmospheric Options (P2)
- [ ] Clip style per image
- [ ] Quote toggle + text + attribution
- [ ] Caption field

### US-ADMIN-018: Connect Settings (P1)
- [ ] Toggle for section
- [ ] Heading customizable
- [ ] Uses existing social links

### US-ADMIN-019: Sanitization (P1)
- [ ] All inputs sanitized
- [ ] URL fields use esc_url_raw
- [ ] Text uses sanitize_text_field

### US-ADMIN-020: Defaults (P2)
- [ ] Sensible defaults set
- [ ] Name defaults to site title
- [ ] Labels have default text

### US-ADMIN-021: Live Preview (P3)
- [ ] Changes reflect in Customizer
- [ ] Real-time updates
- [ ] No page reload needed

### US-ADMIN-022: Mobile Admin (P3)
- [ ] Customizer works on tablet
- [ ] Fields accessible
- [ ] Save works

---

## SUMMARY

| Section | Story Count |
|---------|-------------|
| Hero | 24 |
| Organic Flow | 18 |
| Atmospheric Images | 32 |
| Bio & Bookshelf | 28 |
| World Map | 36 |
| Interests Cloud | 22 |
| Inspirations | 20 |
| Stats & Connect | 12 |
| Animations | 26 |
| Edge Cases | 18 |
| Accessibility | 16 |
| Admin/Customizer | 22 |
| **TOTAL** | **274** |

---

## PRIORITY BREAKDOWN

| Priority | Count | Description |
|----------|-------|-------------|
| P1 | 156 | Must have for launch |
| P2 | 85 | Should have, enhances experience |
| P3 | 33 | Nice to have, polish items |

---

## DEPENDENCIES

### Critical Path
1. Admin/Customizer fields → All sections
2. Hero implementation → First visual test
3. Organic flow → Sets pattern for all sections
4. Accessibility → Throughout development

### Can Be Parallel
- Bio/Bookshelf ↔ Map ↔ Interests/Inspirations
- Animations ↔ Responsive
- Edge cases → After main implementation


