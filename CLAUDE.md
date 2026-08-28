# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

`victor-prdh/recaptcha-bundle` is a Symfony bundle that wraps `google/recaptcha` (v2/v3) as a reusable Symfony Form type (`ReCaptchaType`) plus a validation constraint. It targets Symfony 6.4/7.0/8.0 and PHP >= 8.1. Distributed via Packagist/Composer, installable with Symfony Flex.

There is no CI configured in this repo yet. php-cs-fixer, phpstan (level `max`), and phpunit are wired up locally — run them before committing. Beyond the unit tests, verify behavioral changes by installing the bundle into a real Symfony app (or `composer require` from a local path repository) and exercising a form that uses `ReCaptchaType`.

## Commands

```bash
composer install          # install dependencies
composer validate         # sanity-check composer.json
composer cs-check         # php-cs-fixer, dry-run (--diff)
composer cs-fix           # php-cs-fixer, apply fixes
composer phpstan          # static analysis, level max
composer test             # run the phpunit suite
```

No `composer.lock` is committed (library package).

## Architecture

The bundle follows the standard Symfony bundle skeleton (`src/RecaptchaBundle.php` extends `Bundle`) with these pieces wired together:

- **`src/DependencyInjection/Configuration.php`** — defines the `recaptcha:` config tree (`google_site_key`, `google_secret_key`).
- **`src/DependencyInjection/RecaptchaExtension.php`** — loads `Resources/config/services.yaml`, maps config values to `victor_prdh_recaptcha.*` container parameters, and prepends the bundle's Twig form theme (`@Recaptcha/form/recaptcha.html.twig`) into `twig.form.resources`.
- **`src/Resources/config/services.yaml`** — declares three services:
  - `victor_prdh_recaptcha_bundle.recaptcha_type` — the form type, tagged `form.type` with alias `captcha`.
  - `victor_prdh_recaptcha_bundle.recaptcha_validator` — the constraint validator, tagged `validator.is_valid`.
  - `victor_prdh.recaptcha` — the underlying `ReCaptcha\ReCaptcha` client, constructed with `%env(GOOGLE_RECAPTCHA_SECRET_KEY)%` (note: the secret key is read directly from the env var here, not from the `google_secret_key` config parameter).
- **`src/Form/ReCaptchaType.php`** — a form type extending `HiddenType` (block prefix `recaptcha`) with options `type` (`checkbox` or `invisible`), `mapped: false`, `error_bubbling: false`, and an `IsValidCaptcha` constraint attached by default. Injects the site key into the form view via `ParameterBagInterface`.
- **`src/Validator/Constraints/IsValidCaptcha.php`** / **`IsValidCaptchaValidator.php`** — the constraint and validator pair. The validator reads `g-recaptcha-response` from the current request, calls `ReCaptcha::verify()`, and maps specific Google error codes to translated violations (`missing-input-response`, `timeout-or-duplicate`) or thrown `LogicException`s for misconfiguration errors (`missing-input-secret`, `hostname-mismatch`, `invalid-input-secret`, `bad-request`).
- **`src/Resources/views/form/recaptcha.html.twig`** — renders the `recaptcha_widget` Twig block: a placeholder `<div>` plus inline JS that loads Google's `recaptcha/api.js` and renders the widget (checkbox or invisible mode) client-side, binding invisible-mode challenges to the form's submit buttons.
- **`src/Resources/translations/`** — per-locale (`de`, `en`, `es`, `fr`, `it`) translations for the validator's messages, domain `victorprdh_recaptcha`. Any new user-facing validator message needs a key added in every locale file.

## Conventions

- Strict types (`declare(strict_types=1)`) and constructor property promotion with `readonly` are used throughout — match this style in new/edited PHP files.
- Services are wired via `autowire`/`autoconfigure` in `services.yaml`; prefer extending that file over manual container calls when adding services.
