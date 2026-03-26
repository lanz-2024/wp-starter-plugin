# REST API Reference

Base URL: `/wp-json/wp-starter/v1`

## Authentication

- Read endpoints (`GET`) are public and require no authentication.
- Write endpoints (`POST`, `PUT`, `PATCH`, `DELETE`) require the `edit_posts` capability. Pass a nonce via the `X-WP-Nonce` header or use Application Passwords.

## Portfolio Endpoints

### List Portfolio Items

```
GET /wp-json/wp-starter/v1/portfolio
```

Query Parameters:

| Parameter | Type | Default | Description |
|---|---|---|---|
| `per_page` | integer | 10 | Items per page |
| `page` | integer | 1 | Page number |
| `skill` | string | — | Filter by skill taxonomy slug |
| `industry` | string | — | Filter by industry taxonomy slug |
| `featured` | boolean | — | Filter by featured flag |

Response Headers:

- `X-WP-Total` — total number of matching items
- `X-WP-TotalPages` — total pages

Example:

```bash
curl https://example.com/wp-json/wp-starter/v1/portfolio?per_page=5&featured=true
```

### Get Single Portfolio Item

```
GET /wp-json/wp-starter/v1/portfolio/{id}
```

Example:

```bash
curl https://example.com/wp-json/wp-starter/v1/portfolio/42
```

Response:

```json
{
  "id": 42,
  "title": "My Project",
  "excerpt": "Short description...",
  "content": "<p>Full HTML content</p>",
  "url": "https://myproject.com",
  "repo_url": "https://github.com/org/repo",
  "client": "Acme Corp",
  "year": 2024,
  "featured": true,
  "technologies": "React, Node.js",
  "skills": ["JavaScript", "React"],
  "industries": ["SaaS"],
  "thumbnail": "https://example.com/wp-content/uploads/thumb.jpg",
  "permalink": "https://example.com/portfolio/my-project/",
  "date": "2024-01-15 10:00:00",
  "modified": "2024-03-01 08:30:00"
}
```

### Create Portfolio Item

```
POST /wp-json/wp-starter/v1/portfolio
```

Required: `X-WP-Nonce` header (capability: `edit_posts`)

Body (JSON):

```json
{
  "title": "New Project",
  "content": "<p>Project description</p>",
  "url": "https://newproject.com",
  "repo_url": "https://github.com/org/new-project",
  "client": "Client Name",
  "year": 2025,
  "featured": false,
  "technologies": "PHP, WordPress"
}
```

Example:

```bash
curl -X POST https://example.com/wp-json/wp-starter/v1/portfolio \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: $(wp eval 'echo wp_create_nonce("wp_rest");')" \
  -d '{"title":"New Project","content":"<p>Description</p>"}'
```

### Update Portfolio Item

```
PUT /wp-json/wp-starter/v1/portfolio/{id}
```

Required: `X-WP-Nonce` header (capability: `edit_posts`)

Example:

```bash
curl -X PUT https://example.com/wp-json/wp-starter/v1/portfolio/42 \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: <nonce>" \
  -d '{"title":"Updated Title","featured":true}'
```

### Delete Portfolio Item

```
DELETE /wp-json/wp-starter/v1/portfolio/{id}
```

Required: `X-WP-Nonce` header (capability: `edit_posts`)

Query Parameters:

| Parameter | Type | Default | Description |
|---|---|---|---|
| `force` | boolean | false | Permanently delete instead of trashing |

Example:

```bash
curl -X DELETE https://example.com/wp-json/wp-starter/v1/portfolio/42?force=true \
  -H "X-WP-Nonce: <nonce>"
```

Response:

```json
{ "deleted": true, "id": 42 }
```

## Settings Endpoint

```
GET  /wp-json/wp-starter/v1/settings
POST /wp-json/wp-starter/v1/settings
```

Requires `manage_options` capability for writes.

## Stats Endpoint

```
GET /wp-json/wp-starter/v1/stats
```

Returns aggregate counts for all registered CPTs.
