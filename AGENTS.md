# AGENTS.md

## Project Overview

This is a Laravel application using:
- Laravel
- FilamentPHP
- Eloquent ORM
- Blade
- TailwindCSS
- PHP 8+

Follow Laravel and Filament best practices.

---

# Core Rules

- Prefer clean, maintainable code over shortcuts.
- Keep controllers thin.
- Move business logic into Services or Actions.
- Use Eloquent relationships properly.
- Avoid duplicated logic.
- Use eager loading to prevent N+1 queries.
- Use Form Requests for validation.
- Prefer dependency injection.

---

# Filament Rules

- Follow Filament v3 conventions.
- Use Filament Resources for admin CRUD.
- Use Filament Forms and Tables components properly.
- Keep Filament resources organized.
- Prefer reusable form/table components when possible.
- Use proper validation inside Filament forms.
- Use relation managers for related models.
- Avoid custom hacks if Filament already provides a feature.
- Prefer native Filament actions, filters, widgets, and pages.

When generating Filament resources:
- Generate:
  - Resource
  - Pages
  - Forms
  - Tables
  - RelationManagers if needed
- Use proper column formatting.
- Use searchable and sortable columns where appropriate.
- Use soft delete support if model uses SoftDeletes.

---

# Laravel Architecture

## Validation
- Always use Form Request classes for validation outside Filament.
- Keep validation rules clean and reusable.

## Database
- Use migrations properly.
- Never modify old migrations in production projects.
- Create new migrations for schema changes.
- Use foreign keys properly.

## Models
- Use casts where appropriate.
- Use accessors/mutators carefully.
- Keep models focused.

## Routes
- Use route groups and middleware.
- Prefer resource routes.
- Keep routes/web.php organized.

---

# Frontend Rules

- Use TailwindCSS utilities cleanly.
- Avoid inline styles.
- Keep Blade templates readable.
- Extract reusable Blade components when needed.

---

# Security Rules

Never:
- edit .env unless explicitly asked
- expose secrets or API keys
- disable authentication
- remove authorization checks
- delete migrations without asking
- run migrate:fresh without confirmation
- install random packages without approval

---

# Performance Rules

- Avoid N+1 queries.
- Use pagination for large tables.
- Optimize expensive queries.
- Prefer eager loading.
- Cache only when necessary.

---

# Code Style

- Follow PSR-12.
- Use meaningful variable names.
- Avoid huge methods.
- Prefer early returns.
- Keep functions focused.

---

# Testing

After major changes:
- Run tests
- Check routes
- Check Filament panels
- Verify migrations

Commands:

```bash
php artisan test
php artisan route:list
php artisan optimize:clear
```

---

# Before Editing

Before making large changes:
1. Analyze related files first
2. Explain planned changes
3. Edit only necessary files
4. Preserve existing functionality

---

# Preferred Workflow

For new features:
1. Plan
2. Create migration
3. Create model relationships
4. Create Filament Resource
5. Add validation
6. Optimize queries
7. Run tests

---

# Important

If uncertain:
- ask before destructive changes
- prefer minimal safe edits
- preserve existing coding style