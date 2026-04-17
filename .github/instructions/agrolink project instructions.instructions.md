---
description: AgroLink project coding standards, architecture rules, and cleanup guidelines for AI agents
applyTo: "**/*"
---

# AgroLink Project Instructions

## Current Priority

Focus on:

* codebase cleanup and consistency
* fixing incorrect implementations before adding features
* removing duplication and naming conflicts
* ensuring all features are fully functional end-to-end

---

## General Principles

* Do not rush into writing code without understanding context.
* Prefer clarity, consistency, and correctness over quick fixes.
* Do not introduce breaking changes unless explicitly requested.
* Always align with existing project structure.

---

## Architecture Rules

* This project follows a PHP MVC architecture (no frameworks).
* Controllers are separated by role:

  * buyer
  * farmer
  * transporter
  * admin

* Views are role-specific.
* JavaScript is role-specific where necessary.

### Models

* Models represent database entities and MUST remain shared.
* Do NOT create separate models per user role for the same data.

Example:
- One NotificationModel for all users
- One UserModel for all users

---

## Database Rules

* Database schema is managed via SQL/migrations only.
* NEVER create tables inside PHP code.
* Treat database as single source of truth.
* Do NOT delete tables or columns without verifying full usage.

Always check:
- models
- controllers
- queries

before suggesting removal.

---

## Code Consistency Rules

* Maintain consistent naming across:
  - database columns
  - models
  - controllers
  - JavaScript

* Do NOT mix role-prefixed naming across modules:
  - no farmer-* inside buyer code
  - no buyer-* inside transporter code

* Use shared/neutral naming for reusable components.

---

## Reuse and Duplication Rules

* Avoid duplicating logic across controllers.
* Extract shared logic into helpers/services when needed.
* Shared systems must not be reimplemented per role:

  - notifications
  - users
  - payments
  - orders

---

## Frontend Rules

* Do NOT use localStorage for critical data.
* All important data must come from backend/database.
* Frontend must strictly match backend responses.
* Do not rename JS selectors blindly.
* Maintain UI consistency across roles.

---

## CSS Rules

* CSS must be modular:

  - shared styles → shared/
  - role-specific styles → buyer/, farmer/, transporter/

* Do NOT duplicate styles.
* Do NOT remove CSS unless confirmed unused.
* Do NOT change UI design unless explicitly requested.

---

## Security Rules

* All actions must be validated in backend.
* Users can only modify their own data.
* Admin actions must still be validated.

Highlight missing protections:
- CSRF protection
- input validation issues
- authorization gaps

---

## Feature Validation Rules

Before confirming any feature:

* Verify data is saved correctly in DB
* Verify data is retrieved correctly after refresh
* Ensure frontend-backend alignment
* Handle edge cases
* Enforce role permissions

---

## Legacy Code Cleanup Rules

* Identify hardcoded values, duplicate logic, and unused legacy code.
* Remove code ONLY when it is confirmed unused across the entire project.

Before removing anything:
- Verify it is not referenced in controllers, views, models, or helpers
- Verify it is not part of session fallback or backward compatibility logic
- Perform full project search before deletion

If uncertain:
- Prefer refactoring over deletion
- Migrate legacy logic instead of removing it

If partially used:
- Preserve and gradually refactor
- Do not delete shared or ambiguous utilities

---

## Output Expectations

* Be structured and clear
* Explain reasoning before changes
* Highlight risks
* Prefer step-by-step guidance for complex changes

---

## Project Context

System includes:
- Buyer, Farmer, Transporter, Admin roles
- Notifications system (DB-backed)
- Orders, deliveries, crop requests
- Payments and payout accounts
- Shipping cost calculations

---

## Critical Thinking

* Question incorrect or inconsistent patterns
* Do not blindly follow instructions if design is weak
* Suggest better architecture when needed

---

Act as a senior engineer, not just a code generator.