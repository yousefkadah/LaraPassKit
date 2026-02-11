# Feature Specification: Pass Type Samples & Media Library

**Feature Branch**: `001-pass-type-samples`  
**Created**: 2026-02-11  
**Status**: Draft  
**Input**: User description: "add sample for the templates to easy the flow for the users. on each type of pass the system need to have ready examples and samples that the user can use, media library to select from or to upload images another thing related to the detailes is each pass, by the type the system need to show the relevant fields and data that the user can enter, each entered data is will be the default if the user on create the pass dont pass another value for the field"

## User Scenarios & Testing *(mandatory)*

<!--
  IMPORTANT: User stories should be PRIORITIZED as user journeys ordered by importance.
  Each user story/journey must be INDEPENDENTLY TESTABLE - meaning if you implement just ONE of them,
  you should still have a viable MVP (Minimum Viable Product) that delivers value.
  
  Assign priorities (P1, P2, P3, etc.) to each story, where P1 is the most critical.
  Think of each story as a standalone slice of functionality that can be:
  - Developed independently
  - Tested independently
  - Deployed independently
  - Demonstrated to users independently
-->

### User Story 1 - Choose a Pass Type Sample (Priority: P1)

As a creator starting a new template or pass, I can pick a pass type sample so the system pre-fills example fields and images that match the selected pass type, reducing setup time.

**Why this priority**: Fast starts are the biggest friction point when creating a template from scratch.

**Independent Test**: Create a template for a specific pass type using a sample and verify fields/images are pre-filled without additional setup.

**Acceptance Scenarios**:

1. **Given** I start a new template or pass and select a pass type, **When** I choose a sample for that type, **Then** the form is pre-filled with that sample's fields and images.
2. **Given** a pass type has no available samples, **When** I open the sample picker, **Then** I see a clear empty state and can proceed without a sample.

---

### User Story 2 - Select Images from a Media Library (Priority: P2)

As a creator, I can choose images from a media library or upload new ones so I can quickly populate template or pass visuals without leaving the flow.

**Why this priority**: Sample fields are incomplete without sample imagery, and creators need a fast way to reuse assets.

**Independent Test**: Open the media library, select a sample image for a slot, and verify it appears in the template or pass form.

**Acceptance Scenarios**:

1. **Given** I am editing a template or pass image slot, **When** I open the media library and pick an asset, **Then** the slot is filled with that asset.
2. **Given** I upload a new image to the media library from a template or pass flow, **When** the upload completes, **Then** the new asset becomes selectable immediately.

---

### User Story 3 - Show Relevant Fields by Pass Type (Priority: P3)

As a creator, I see only the fields relevant to the selected pass type, and the values I enter become defaults used for passes created from the template unless overridden.

**Why this priority**: Reduces confusion and ensures consistent default data across passes created from a template.

**Independent Test**: Choose a pass type, verify the field set changes, save a template with defaults, and ensure a new pass uses those defaults when left blank.

**Acceptance Scenarios**:

1. **Given** I select a pass type, **When** the form loads fields, **Then** only fields relevant to that type are shown.
2. **Given** I save default values in a template, **When** I create a pass without overriding a field, **Then** the default value is used.

---

[Add more user stories as needed, each with an assigned priority]

### Edge Cases

- What happens when a pass type has zero samples or sample assets are unavailable?
- How does the system handle a media library upload that fails or is invalid?
- What happens when a user switches pass type after selecting a sample?
- How does the system handle conflicting defaults when a field is removed or renamed?
- How does the system handle switching samples when the user has unsaved edits?
- How does the system handle user-created samples that are deleted or updated?
- What happens when a platform-specific sample variant is missing?
- What happens when a sample has no images at all?
- What happens when a platform-specific sample variant is missing but a base sample exists?
- What happens when the user cancels a sample switch confirmation?

## Clarifications

### Session 2026-02-11

- Q: Should samples and media library apply to template flow only or both template and pass flows? → A: Both template and pass creation/edit flows.
- Q: What is the media library visibility scope? → A: User-scoped only (each user sees only their uploads + global samples).
- Q: How should sample switching handle user-entered data? → A: Prompt to confirm reset when a sample change would overwrite user-entered data.
- Q: How detailed should pass-type field filtering be? → A: Full pass-type field map covering all Apple/Google-specific fields.
- Q: What is the source of samples? → A: Both system-curated and user-created samples.
- Q: Are samples required per pass type and platform? → A: Samples required per pass type; platform variants are optional.
- Q: How many sample images are required? → A: All image slots are required in samples.
- Q: What is the upload timeout for media library uploads? → A: 15 minutes.

## Definitions

### Supported Pass Types

The system supports the following pass types for samples, templates, and passes:

- Apple: `generic`, `coupon`, `boardingPass`, `eventTicket`, `storeCard`
- Google: `generic`, `offer`, `loyalty`, `eventTicket`, `boardingPass`, `transit`
- Cross-platform custom: `stampCard`

### Image Slots

All samples and media library assets reference the same image slots:

- `icon`
- `logo`
- `strip`
- `thumbnail`
- `background`
- `footer`

### Pass-Type Field Map

Field groups apply per pass type and platform as follows (all field groups use the same field schema of `{ key, label, value }`):

- Apple `generic`, `coupon`, `eventTicket`, `storeCard`: `header`, `primary`, `secondary`, `auxiliary`, `back`
- Apple `boardingPass`: `header`, `primary`, `secondary`, `auxiliary`, `back` + `transitType`
- Google `generic`, `offer`, `loyalty`, `eventTicket`: `header`, `primary`, `secondary`, `auxiliary`, `back`
- Google `boardingPass`, `transit`: `header`, `primary`, `secondary`, `auxiliary`, `back` + `transitType`
- `stampCard`: `header`, `primary`, `secondary`, `auxiliary`, `back`

### Default Value Rules

- When a pass field is omitted or `null`, use the template default.
- When a pass field is present with an empty string, treat it as an explicit override.

<!--
  ACTION REQUIRED: The content in this section represents placeholders.
  Fill them out with the right functional requirements.
-->

### Functional Requirements

- **FR-001**: System MUST provide sample templates for each supported pass type.
- **FR-002**: Users MUST be able to apply a sample to pre-fill template or pass fields and images.
- **FR-003**: System MUST provide a media library that includes global sample assets and user-uploaded assets.
- **FR-004**: Users MUST be able to select a media library asset for any image slot from template and pass flows, scoped to their own uploads plus global samples.
- **FR-005**: Users MUST be able to upload new images into the media library from template and pass flows.
- **FR-006**: System MUST show only the fields relevant to the selected pass type.
- **FR-007**: System MUST treat values entered in template fields as defaults for passes created from that template when a pass does not override the value.
- **FR-008**: System MUST allow creators to continue without a sample when none are available.
- **FR-009**: System MUST preserve user-entered data when switching between samples or pass types unless the user confirms a reset.
- **FR-010**: System MUST prompt for confirmation before overwriting user-entered data when switching samples.
- **FR-011**: System MUST filter available fields using a full pass-type field map that includes all Apple- and Google-specific fields.
- **FR-012**: System MUST support both system-curated samples and user-created samples.
- **FR-013**: System MUST provide samples per pass type; platform-specific variants are optional and should be used when available.
- **FR-014**: System MUST include images for all defined image slots in each sample.
- **FR-015**: System MUST allow media library uploads up to 15 minutes before timing out.
- **FR-016**: Users MUST be able to create, list, rename, and delete their own samples.
- **FR-017**: Media library listing MUST support filtering by `source` and `slot`, and MUST support pagination.
- **FR-018**: Global sample assets MUST be curated and owned by the system (not tied to a user).
- **FR-019**: When a platform-specific sample is missing, the system MUST fall back to the base pass-type sample.
- **FR-020**: If a sample is missing required image slots, the system MUST block apply and show a validation error.
- **FR-021**: If the user cancels a sample-switch confirmation, existing edits MUST remain unchanged.
- **FR-022**: Media library uploads MUST enforce the 15-minute timeout as a total request duration per upload.
- **FR-023**: Sample apply and media library listing MUST meet performance goals in Success Criteria and NFRs.

### Key Entities *(include if feature involves data)*

- **Pass Type Sample**: A curated example for a specific pass type, including default field values and suggested images.
- **User Sample**: A user-saved sample derived from a template or pass, scoped to the user's account.
- **Media Library Asset**: An image asset available for selection, tagged by source (sample or user-uploaded).
- **Pass Type Field Set**: The full map of fields relevant to each pass type (including Apple and Google field variants), used to filter the form UI.
- **Template Default Values**: Values saved on a template and applied to passes when a field is not overridden.

### Assumptions

- Sample templates and sample media assets are provided per pass type by the system.
- Media library access is scoped to the individual user account.
- Template defaults only apply when a pass field is omitted, not when a pass explicitly sets an empty value.

## Non-Functional Requirements

- **NFR-001**: Sample apply completes within 1 second in the 95th percentile.
- **NFR-002**: Media library list responses return within 1 second in the 95th percentile.
- **NFR-003**: Media library listing supports pagination without returning more than 100 assets per response.
- **NFR-004**: User-scoped assets are never visible to other users.
- **NFR-005**: Sample and asset create/delete actions are audit-logged.
- **NFR-006**: User assets are deleted immediately upon user deletion; system assets are retained.

## Constitution Check *(mandatory)*

- Confirm Laravel-first approach (Eloquent, Form Requests, policies, Inertia).
- Confirm Wayfinder routes are used (no hardcoded URLs).
- Confirm tests will be added/updated and the minimal test run is identified.
- Confirm authorization and tenant scoping are explicit.
- Confirm heavy work is queued and N+1 risks are addressed.
## Success Criteria *(mandatory)*

<!--
  ACTION REQUIRED: Define measurable success criteria.
  These must be technology-agnostic and measurable.
-->

### Measurable Outcomes

- **SC-001**: 80% of new templates created use a sample or media library asset on first save.
- **SC-002**: Median time to create a template (from open to save) is under 5 minutes.
- **SC-003**: 90% of users complete the template creation flow without assistance.
- **SC-004**: Support requests related to "missing default fields" drop by 30% within one release cycle.

### Measurement Method

- Track sample apply, media library selection, and template save events via product analytics and compute monthly ratios.
- Track median template creation time using client-side timestamps captured on form open and save.
- Track support requests using tagged tickets in the support system.
