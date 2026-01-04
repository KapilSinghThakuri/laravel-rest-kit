# RestKit: Core API Infrastructure

RestKit provides a complete API toolkit for Laravel projects, offering standardized, professional-grade API functionality out-of-the-box.

## 1. Request Handling

- Force JSON requests/responses
- Content-Type enforcement
- API-only middleware group
- Request normalization (trim/null handling)
- API version detection (header/URL)

## 2. Response Standardization

- Success & error response formatters
- Meta support (pagination, version, request ID)
- Empty response & file/stream wrappers

## 3. HTTP Status Management

- Centralized mapping of common status codes: 200, 201, 204, 400, 401, 403, 404, 409, 422, 429, 500

## 4. Exception & Error Handling

- Base `ApiException`
- Validation, authentication, authorization, and model exceptions
- Fallback handler with environment-aware responses

## 5. Validation Layer

- Base API request class
- Automatic standardized validation responses
- Custom error messages structure

## 6. Authentication & Authorization

- Guard resolver & token auth helpers
- Current user resolver
- Standardized auth/permission error responses

## 7. Pagination

- Standardized response structure
- Cursor & offset pagination
- Pagination metadata (`current_page`, `per_page`, `total`)

## 8. Filtering, Sorting & Searching

- Query parameter parsing
- Whitelisted filters & sorts
- Safe search operators

## 9. API Resources / Transformers

- Base API Resource class & collection wrapper
- Conditional fields & relationship inclusion

## 10. Rate Limiting & Throttling

- API throttling config
- Standardized rate-limit exceeded response
- Per-token / per-IP support

## 11. API Versioning

- Version resolver (URL/header)
- Versioned response metadata
- Backward compatibility helpers

## 12. API Documentation Support

- Swagger/OpenAPI base config
- Auth scheme & global response schemas

## 13. Logging & Observability

- Request ID & error correlation
- API request/response logging
- Slow request detection hooks

## 14. CORS Handling

- API-only CORS rules
- Preflight response handling

## 15. Security Defaults

- Input sanitization & header hardening
- Mass-assignment protection
- File upload validation

## 16. Testing Utilities

- API test base class
- Response assertion helpers
- Authentication mocking helpers

## 17. Configuration System

- Publishable config file
- Feature toggles (pagination, debug, versioning)
- Environment awareness
