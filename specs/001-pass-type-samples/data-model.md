# Data Model: Pass Type Samples & Media Library

## Overview

This feature introduces a media library, pass-type samples (system and user-created), and a full pass-type field map used to filter form fields. The core pass/template data models remain unchanged; new entities track assets and samples.

## Entities

### MediaLibraryAsset
Represents a selectable image asset for templates and passes.

**Fields**:
- `id`: string/uuid
- `owner_user_id`: string | null (null for global sample assets)
- `source`: `system` | `user`
- `slot`: PassImageSlot | null
- `path`: string
- `url`: string
- `width`: int
- `height`: int
- `mime`: string
- `size_bytes`: int
- `created_at`: datetime
- `updated_at`: datetime

### PassTypeSample
Represents a sample for a specific pass type.

**Fields**:
- `id`: string/uuid
- `owner_user_id`: string | null (null for system samples)
- `source`: `system` | `user`
- `name`: string
- `description`: string | null
- `pass_type`: PassType
- `platform`: `apple` | `google` | null (optional variants)
- `fields`: object (full field payload grouped by field set)
- `images`: object (image slot map with asset references)
- `created_at`: datetime
- `updated_at`: datetime

### PassTypeFieldMap
Defines the full field set for each pass type (Apple/Google variants).

**Fields**:
- `pass_type`: PassType
- `platform`: `apple` | `google`
- `field_groups`: object (header, primary, secondary, auxiliary, back)
- `constraints`: object (limits, required fields)

## Relationships

- MediaLibraryAsset `owner_user_id` is scoped to a user; system assets have no owner.
- PassTypeSample `owner_user_id` is scoped to a user; system samples have no owner.
- PassTypeSample may reference MediaLibraryAsset entries per image slot.
- PassTypeFieldMap is a configuration entity referenced by UI and validation.

## Notes

- All sample images must include every defined image slot.
- Platform-specific samples are optional; when missing, the base pass type sample is used.
