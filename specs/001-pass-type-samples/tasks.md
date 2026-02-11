# Tasks: Pass Type Samples & Media Library

**Input**: Design documents from [specs/001-pass-type-samples/](specs/001-pass-type-samples/)
**Prerequisites**: plan.md (required), spec.md (required), research.md, data-model.md, contracts/, quickstart.md

**Tests**: Tests are REQUIRED for any change unless a governance-approved waiver is documented in spec.md.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Shared configuration and scaffolding for samples, media library, and field maps.

- [x] T001 Add pass-type field map config in config/pass-type-fields.php
- [x] T002 [P] Add sample/media library types in resources/js/types/sample.ts
- [x] T003 [P] Add API client helpers for samples/media endpoints in resources/js/lib/samples.ts
- [x] T004 [P] Add shared UI shell for sample/media modals in resources/js/components/sample-picker.tsx

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core backend models, storage, policies, and routing needed before user story work.

- [x] T005 Create migrations for media assets and samples in database/migrations/2026_02_12_000001_create_media_library_assets_table.php and database/migrations/2026_02_12_000002_create_pass_type_samples_table.php
- [x] T006 [P] Create MediaLibraryAsset model in app/Models/MediaLibraryAsset.php
- [x] T007 [P] Create PassTypeSample model in app/Models/PassTypeSample.php
- [x] T008 [P] Add policies for assets and samples in app/Policies/MediaLibraryAssetPolicy.php and app/Policies/PassTypeSamplePolicy.php
- [x] T009 Create form requests for samples and assets in app/Http/Requests/MediaLibraryAssetRequest.php and app/Http/Requests/PassTypeSampleRequest.php
- [x] T010 Implement media asset service reusing pass image pipeline in app/Services/MediaLibraryService.php
- [x] T011 Implement sample service for apply/save logic in app/Services/PassTypeSampleService.php
- [x] T012 Add controllers for samples and media assets in app/Http/Controllers/PassTypeSampleController.php and app/Http/Controllers/MediaLibraryAssetController.php
- [x] T013 Add pass-type field map controller in app/Http/Controllers/PassTypeFieldMapController.php
- [x] T014 Register routes for samples, assets, and field maps in routes/passes.php

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel.

---

## Phase 3: User Story 1 - Choose a Pass Type Sample (Priority: P1) 🎯 MVP

**Goal**: Allow creators to pick samples for pass types and apply them in template and pass flows, with overwrite confirmation.

**Independent Test**: Select a sample for a pass type and verify fields/images populate in both template and pass creation flows.

### Tests for User Story 1 (REQUIRED) ⚠️

- [x] T015 [P] [US1] Add feature tests for listing/applying samples in tests/Feature/PassTypeSamplesTest.php
- [x] T016 [P] [US1] Add feature tests for user sample creation/deletion in tests/Feature/PassTypeSamplesTest.php

### Implementation for User Story 1

- [x] T017 [US1] Add sample picker integration to template create/edit in resources/js/pages/templates/create.tsx and resources/js/pages/templates/edit.tsx
- [x] T018 [US1] Add sample picker integration to pass create/edit in resources/js/pages/passes/create.tsx and resources/js/pages/passes/edit.tsx
- [x] T019 [US1] Implement overwrite confirmation modal logic in resources/js/components/sample-picker.tsx
- [x] T020 [US1] Add user sample save action in resources/js/components/sample-picker.tsx
- [x] T021 [US1] Wire sample apply payload into form state using helpers in resources/js/lib/samples.ts

**Checkpoint**: User Story 1 fully functional and independently testable.

---

## Phase 4: User Story 2 - Select Images from a Media Library (Priority: P2)

**Goal**: Allow users to pick or upload images from a media library for any image slot in template and pass flows.

**Independent Test**: Open media library from an image slot, select an asset, and see it applied to the slot.

### Tests for User Story 2 (REQUIRED) ⚠️

- [x] T022 [P] [US2] Add feature tests for listing/uploading assets in tests/Feature/MediaLibraryTest.php

### Implementation for User Story 2

- [x] T023 [US2] Add media library modal to image uploader in resources/js/components/image-uploader.tsx
- [x] T024 [US2] Implement media library grid and upload flow in resources/js/components/media-library.tsx
- [x] T025 [US2] Connect media library selection to image slot updates in resources/js/lib/samples.ts
- [x] T026 [US2] Add asset delete action UI in resources/js/components/media-library.tsx

**Checkpoint**: User Story 2 works independently for templates and passes.

---

## Phase 5: User Story 3 - Show Relevant Fields by Pass Type (Priority: P3)

**Goal**: Filter fields by pass type using the full field map and apply template defaults when pass values are omitted.

**Independent Test**: Switch pass type and verify field groups update; create pass without overriding template fields and confirm defaults apply.

### Tests for User Story 3 (REQUIRED) ⚠️

- [x] T027 [P] [US3] Add feature tests for pass-type field map responses in tests/Feature/PassTypeFieldMapTest.php
- [x] T028 [P] [US3] Add feature tests for default application on pass creation in tests/Feature/PassTypeDefaultsTest.php

### Implementation for User Story 3

- [x] T029 [US3] Add field map fetch and filtering logic in resources/js/lib/pass-type-fields.ts
- [x] T030 [US3] Filter field editors by pass type in resources/js/pages/templates/create.tsx and resources/js/pages/templates/edit.tsx
- [x] T031 [US3] Filter field editors by pass type in resources/js/pages/passes/create.tsx and resources/js/pages/passes/edit.tsx
- [x] T032 [US3] Apply template defaults on pass creation in app/Http/Controllers/PassController.php
- [x] T033 [US3] Ensure template defaults are preserved in app/Http/Controllers/PassTemplateController.php

**Checkpoint**: User Story 3 works independently and defaults apply correctly.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Validation and cross-story cleanup.

- [x] T034 [P] Update quickstart validation steps in specs/001-pass-type-samples/quickstart.md
- [x] T035 [P] Run required test commands and record results in specs/001-pass-type-samples/quickstart.md
- [x] T036 [P] Ensure Wayfinder routes are regenerated if new routes are added in resources/js/routes
- [x] T037 [P] Seed system samples with placeholders in database/seeders/PassTypeSampleSeeder.php

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately.
- **Foundational (Phase 2)**: Depends on Setup completion - blocks all user stories.
- **User Stories (Phase 3+)**: Depend on Foundational completion.
- **Polish (Phase 6)**: Depends on all desired user stories being complete.

### User Story Dependencies

- **US1 (P1)**: No dependency on other stories after Foundational.
- **US2 (P2)**: Depends on Foundational services/endpoints.
- **US3 (P3)**: Depends on Foundational and can proceed independently of US2.

### Parallel Opportunities

- T001–T004 can run in parallel.
- T006–T008 can run in parallel after T005.
- T015–T016 can run in parallel.
- T023–T026 can be parallelized between UI and helper work.
- T027–T028 can run in parallel.

---

## Parallel Example: User Story 1

```bash
Task: "Add feature tests for listing/applying samples in tests/Feature/PassTypeSamplesTest.php"
Task: "Add feature tests for user sample creation/deletion in tests/Feature/PassTypeSamplesTest.php"

Task: "Add sample picker integration to template create/edit in resources/js/pages/templates/create.tsx and resources/js/pages/templates/edit.tsx"
Task: "Add sample picker integration to pass create/edit in resources/js/pages/passes/create.tsx and resources/js/pages/passes/edit.tsx"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational
3. Complete Phase 3: User Story 1
4. Validate US1 with tests in tests/Feature/PassTypeSamplesTest.php

### Incremental Delivery

1. US1: Sample selection + apply flow
2. US2: Media library integration
3. US3: Pass-type field map filtering + defaults
4. Polish: Quickstart validation
