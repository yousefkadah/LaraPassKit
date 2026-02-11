# Research: Pass Type Samples & Media Library

## Decision 1: Reuse existing pass image upload pipeline

**Decision**: Use the existing pass image upload service and response shape for media library assets, storing originals and variants via the same disk/path configuration.

**Rationale**:
- Keeps behavior consistent with current pass/template image uploads.
- Avoids new dependencies and aligns with Laravel-first constraints.
- Reuses validation patterns and storage URL generation already in place.

**Alternatives considered**:
- New standalone media upload pipeline (adds duplication and risk of drift).
- Third-party media library packages (disallowed by constraints).

## Decision 2: Centralize pass-type field map

**Decision**: Define a single, centralized pass-type field map that includes Apple and Google variants, and expose it to the frontend for filtering and default field setup.

**Rationale**:
- Prevents UI and backend from diverging on field relevance.
- Supports full pass-type filtering requirement with a single source of truth.
- Aligns with existing Apple/Google service mappings already in use.

**Alternatives considered**:
- Frontend-only hardcoded field sets (drift risk).
- Infer fields dynamically from templates (unreliable and opaque).

## Decision 3: User-scoped assets plus global samples

**Decision**: Scope media library uploads to individual users while allowing global sample assets, with both system and user-created samples supported.

**Rationale**:
- Matches clarified scope and reduces privacy risks.
- Keeps samples reusable without cross-user data exposure.

**Alternatives considered**:
- Org-wide asset sharing (not requested).
- Global sharing of user uploads (high privacy risk).
