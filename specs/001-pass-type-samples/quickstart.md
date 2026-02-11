# Quickstart: Pass Type Samples & Media Library

## Prerequisites

- App dependencies installed
- Storage disk configured for pass images

## Manual Verification

1. Open template creation.
2. Select a pass type and apply a sample.
3. Verify fields and images pre-fill, with a confirmation prompt if unsaved edits exist.
4. Save the current template or pass as a user sample and confirm it appears in the picker.
5. Open the media library for an image slot and select an asset.
6. Upload a new asset and confirm it appears in the library.
7. Create a pass from the template and verify defaults apply when a field is left blank.

## Expected Result

- Samples pre-fill fields and images for both templates and passes.
- Media library lists global samples and user uploads only.
- Field sets change based on pass type using the full field map.

## Test Command

```
php artisan test --compact tests/Feature/PassTypeSamplesTest.php tests/Feature/MediaLibraryTest.php tests/Feature/PassTypeFieldMapTest.php tests/Feature/PassTypeDefaultsTest.php
```

## Test Results (2026-02-12)

- ✅ Passed: php artisan test --compact tests/Feature/PassTypeSamplesTest.php tests/Feature/MediaLibraryTest.php tests/Feature/PassTypeFieldMapTest.php tests/Feature/PassTypeDefaultsTest.php
